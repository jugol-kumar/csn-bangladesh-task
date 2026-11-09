<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_thumbnail' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];

        if (! $this->route('listing')) {
            $shop = $this->user()?->shop;

            $rules['product_id'] = [
                'required',
                'integer',
                'exists:products,id',
                Rule::unique('shop_products', 'product_id')->where('shop_id', $shop?->id),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'product_id.unique' => 'This product is already in your shop.',
        ];
    }
}
