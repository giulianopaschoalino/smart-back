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
        $data['path'] = $file->storeAs('pdf', $data['uid'].".{$extension}", 's3');

        return InfoSectorial::query()->create($data);

    }

    public function download()
    {
       $created_at = InfoSectorial::query()->max('created_at');

       $data = InfoSectorial::query()->where('created_at', '=', $created_at)->first();

       if (!Storage::disk('s3')->exists($data->path))
       {
           return $this->errorResponse( false, '', 500);
       }
       $path['path'] = Storage::disk('s3')->url($data->path);

       return response()->json($path, 200);
    }
}
