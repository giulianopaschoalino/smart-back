<?php

namespace App\Imports;

use App\Models\DadosCadastrais;
use App\Models\User;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use ZanySoft\Zip\Facades\Zip;

class UsersWithSmartUsersImport implements ToCollection
{
    /**
     * 
     * @param array<string, string> $files_paths 
     */
    private array $files_paths = [];

    public function __construct(private UploadedFile $files_logos)
    {
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $this->uploadProfilePictures();

        $rows->shift();

        $rows->each(function (Collection $row) {
            $email_user = $row->get(1);
            $client_id = $row->get(0);

            $client = DadosCadastrais::where("cod_smart_cliente", $client_id)->first();

            $client_name_format = (string) Str::of($client->cliente ?? "")
                ->trim()
                ->lower()
                ->replace(" ", "");

            $email = "cli_{$client_name_format}@energiasmart.com.br";

            $user_exists = User::where('email', $email_user)->exists();
            $user_smart_exists = User::where('email', $email)->exists();

            !empty($client) && !$user_exists && $this->createUserAccess(
                ...\compact('row', 'client')
            );
            !empty($client) && !$user_smart_exists && $this->createSmartUserAccess(
                ...\compact('row', 'client', 'email')
            );
        });
    }

    private function createUserAccess(Collection $row, DadosCadastrais $client): void
    {
        $client_id = $row->get(0);
        $name = $client->cliente;
        $email = \trim($row->get(1));
        $password = Hash::make(Str::random(7));
        $profile_picture = \array_key_exists($client_id, $this->files_paths)
            ? $this->files_paths[$client_id]
            : '';

        User::create(\compact(
            'client_id',
            'name',
            'email',
            'password',
            'profile_picture'
        ));
    }

    private function createSmartUserAccess(Collection $row, DadosCadastrais $client, string $email): void
    {
        $client_id = $row->get(0);
        $name = $client->cliente;
        $password = Hash::make($row->get(2));
        $profile_picture = \array_key_exists($client_id, $this->files_paths)
            ? $this->files_paths[$client_id]
            : '';

        User::create(\compact(
            'client_id',
            'name',
            'email',
            'password',
            'profile_picture'
        ));
    }

    private function uploadProfilePictures()
    {
        $temp_extract_files_path = storage_path('app/extract');

        $zip = Zip::open($this->files_logos);
        $zip->extract($temp_extract_files_path);
        $zip->close();

        collect(\scandir($temp_extract_files_path))
            ->each(function ($filename) use ($temp_extract_files_path) {
                if (\in_array($filename, ['.', '..'])) return;

                $temp_file_path = "$temp_extract_files_path/$filename";

                $picture = new File($temp_file_path);
                $pathS3 = "avatars/{$picture->hashName()}";

                Storage::disk('s3')->put($pathS3, $picture->getContent());

                $filename = \preg_replace("/\.[^\.]+$/", "", $filename);

                $this->files_paths[$filename] = url('/images/test.png') ?? Storage::disk('s3')->url($pathS3);

                \unlink($temp_file_path);
            });
    }
}
