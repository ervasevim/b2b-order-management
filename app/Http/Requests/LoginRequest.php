<?php

namespace App\Http\Requests;

use App\Http\Trait\HttpResponseTrait;

class LoginRequest extends AbstractRequest
{
    use HttpResponseTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

}
