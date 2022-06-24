<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadInfoSectorialRequest;
use App\Models\InfoSectorial;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InfoSectorialController extends Controller
{
    use ApiResponse;

    public function updateFile(UploadInfoSectorialRequest $uploadInfoSectorialRequest)
    {
        $data = $uploadInfoSectorialRequest->all();

        if (!$uploadInfoSectorialRequest->hasFile('file')) {
            return $this->errorResponse( false, '', 500);
        }

        $file = $uploadInfoSectorialRequest->file('file');

        $data['name'] = Str::of($file->getClientOriginalName())->explode('.')->offsetGet(0);
        $data['uid'] = Str::of($file->hashName())->explode('.')->offsetGet(0);
        $extension = $file->getClientOriginalExtension();
        $path = $file->store('pdf','s3');

        dd($path);
//        $path = Storage::disk('s3')->put('pdf', $data['uid'].".{$extension}");
//        $path = Storage::disk('s3')->url($path);
        dd($path);
        //$data['uid'].".{$extension}"

        return InfoSectorial::query()->create($data);

    }

    public function download()
    {
       $created_at = InfoSectorial::query()->max('created_at');

       $data = InfoSectorial::query()->where('created_at', '=', $created_at)->first();

       if (Storage::disk('public')->exists($data->path))
       {
           $path = Storage::disk('public')->path(($data->path));

          $heders = [
               'Content-Type' => mime_content_type($path)
           ];

           return response()->download($path, $data->name, $heders);

       }
    }
}
