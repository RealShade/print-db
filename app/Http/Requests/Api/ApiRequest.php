<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiRequest extends FormRequest
{

    /* **************************************** Public **************************************** */
    public function authorize() : bool
    {
        return true;
    }

    /* **************************************** Protected **************************************** */
    protected function failedValidation(Validator $validator) : void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    protected function prepareForValidation() : void
    {
        $mergeData = [];
        foreach ($this->rules() as $field => $rule) {
            $mergeData[ $field ] = $this->input($field, $this->query($field));
        }
        $this->merge($mergeData);
    }

}
