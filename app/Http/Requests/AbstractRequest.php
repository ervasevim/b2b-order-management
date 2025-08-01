<?php

namespace App\Http\Requests;

use App\Http\Trait\HttpResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

abstract class AbstractRequest extends FormRequest
{
    use HttpResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @param Validator $validator
     * @return mixed
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->error($validator->errors()->toArray(),
                ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                'Lütfen alanları kontrol ediniz!')
        );
    }
}
