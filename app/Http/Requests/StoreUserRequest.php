<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255|min:3',
            'email' => [
                'required',
                'email',
                "unique:users,email"
            ],
            'password'=> 'required|string|min:6|confirmed',
            'client_id' => 'nullable',
            'profile_picture' =>'nullable|image|max:1024'
        ];
    }
}
