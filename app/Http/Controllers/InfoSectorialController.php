<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadInfoSectorialRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;

class InfoSectorialController extends Controller
{
    use ApiResponse;

    public function updateFile(UploadInfoSectorialRequest $uploadInfoSectorialRequest)
    {
        $data = $uploadInfoSectorialRequest->all();

        if (!$uploadInfoSectorialRequest->hasFile('reportfile')) {
            return $this->errorResponse( false, '', 500);
        }

        $file = $uploadInfoSectorialRequest->file('reportfile');

        $data['name'] = Str::of($file->getClientOriginalName())->explode('.')->offsetGet(0);
        $extension = $file->getClientOriginalExtension();

        $data['reportfile'] = $file->storeAs('file', $data['name'].".{$extension}");

        dd($data);

    }

    public function download()
    {
        $file = public_path("Clockify_Time_Report_Detailed_01_05_2022-31_05_2022.pdf");

        $path = storage_path("public/file/Clockify_Time_Report_Detailed_01_05_2022-31_05_2022.pdf");


        $headers = ['Content-Type: application/pdf'];
        $newName = 'itsolutionstuff-pdf-file-'.time().'.pdf';

        return response()->download($file, $newName, $headers);
    }
}
