<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'items' => ['required', 'array', 'min:1'],
        'items.*.product_id' => ['required', 'exists:products,id'],
        'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->items ?? [] as $index => $item) {
                $product = \App\Models\Product::find($item['product_id'] ?? null);

                if ($product && ($item['qty'] ?? 0) > $product->stock) {
                    $validator->errors()->add(
                        'items',
                        "Stok produk {$product->name} tidak mencukupi."
                    );
                }
            }
        });
    }
}
