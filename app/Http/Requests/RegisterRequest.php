<?php

namespace App\Http\Requests;

use App\Http\Trait\HttpResponse;

class RegisterRequest extends AbstractRequest
{
    use HttpResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'in:admin,customer',
        ];
    }

}
