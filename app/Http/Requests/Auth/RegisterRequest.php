<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        return true;
    }

    public function messages() : array
    {
        return [
        ];
    }

    public function rules() : array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function userData() : array
    {
        return [
            'name'     => $this->input('name'),
            'email'    => $this->input('email'),
            'password' => $this->input('password'),
        ];
    }
}
