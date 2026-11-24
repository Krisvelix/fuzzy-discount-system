<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'stock' => ['sometimes', 'required', 'integer', 'between:0,100'],
            'storage_duration' => ['sometimes', 'required', 'integer', 'between:0,180'],
            'monthly_sales' => ['sometimes', 'required', 'integer', 'between:0,100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama produk',
            'stock' => 'jumlah stok',
            'storage_duration' => 'lama penyimpanan',
            'monthly_sales' => 'tingkat penjualan',
        ];
    }
}
