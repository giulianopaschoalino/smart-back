<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJsonMessage;
use App\Http\Requests\UploadInfoSectorialRequest;
use App\Models\InfoSectorial;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InfoSectorialController extends Controller
{
    

    public function updateFile(UploadInfoSectorialRequest $uploadInfoSectorialRequest)
    {
        $data = $uploadInfoSectorialRequest->all();

        if (!$uploadInfoSectorialRequest->hasFile('file')) {
            return $this->errorResponse(false, '', 500);
        }

        $file = $uploadInfoSectorialRequest->file('file');

        $data['name'] = Str::of($file->getClientOriginalName())->explode('.')->offsetGet(0);
        $data['uid'] = Str::of($file->hashName())->explode('.')->offsetGet(0);
        $extension = $file->getClientOriginalExtension();
        $data['path'] = $file->storeAs('pdf', $data['uid'] . ".{$extension}", 's3');

        return InfoSectorial::query()->create($data);
    }

    public function download()
    {
        $data = InfoSectorial::query()->latest('created_at')->first();

        if ($data === null) {
            return ResponseJsonMessage::withData('');
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        try {
            $temporaryUrl = $disk->temporaryUrl($data->path, now()->addMinutes(15));
        } catch (\Throwable) {
            return ResponseJsonMessage::withError('Unable to generate download link', 500);
        }

        return ResponseJsonMessage::withData($temporaryUrl);
    }
}
