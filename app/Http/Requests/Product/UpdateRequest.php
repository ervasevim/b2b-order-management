<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends AbstractRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? null;

        return [
            'name' => 'sometimes|required|string',
            'sku' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'price' => 'sometimes|required|numeric',
            'stock_quantity' => 'sometimes|required|integer',
        ];
    }

}
