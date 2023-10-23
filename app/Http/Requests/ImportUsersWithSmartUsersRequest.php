<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportUsersWithSmartUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'file_users' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv',
            'file_logos' => 'required|file|mimetypes:application/zip',
            'password' => function($attr, $value, $fail) {
                if($value !== '78s7*a77xghhsa5219129382(*728292SPsk%%%shssajlk') $fail('Senha inválida');
            }
        ];
    }
}
