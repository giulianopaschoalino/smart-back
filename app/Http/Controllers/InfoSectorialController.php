<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadInfoSectorialRequest;
use Illuminate\Http\Request;

class InfoSectorialController extends Controller
{
    public function updateFile(UploadInfoSectorialRequest $uploadInfoSectorialRequest)
    {

        dd($uploadInfoSectorialRequest->validated());

    }

    public function download(){

        return response()->download();
    }
}
