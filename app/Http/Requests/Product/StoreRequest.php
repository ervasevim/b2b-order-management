<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\AbstractRequest;

class StoreRequest extends AbstractRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'sku' => 'required|string|unique:products',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
        ];
    }

}
