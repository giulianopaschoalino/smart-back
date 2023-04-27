<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function store(Request $request) 
    {
        if (!$request->hasFile('file')) {
            return $this->errorResponse( false, 'É necessário enviar um arquivo de imagem', 500);
        }

        $file = $request->file('file');
        
        $path = $file->storeAs('avatars', $file->hashName(),'s3');

        $url = '';
        try{
            $url = Storage::disk('s3')->url($path);
        }catch(\Exception $e){
            return response()->json([
                'erro' => 'Não foi possível salvar o arquivo'
            ], 500);
        }

        return response()->json([
            'url' => $url
        ], 201);
    }
}
