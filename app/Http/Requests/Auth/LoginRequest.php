<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        return true;
    }

    public function credentials() : array
    {
        return $this->only(['email', 'password']);
    }

    public function messages() : array
    {
        return [
        ];
    }

    public function rules() : array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|string',
        ];
    }

}
