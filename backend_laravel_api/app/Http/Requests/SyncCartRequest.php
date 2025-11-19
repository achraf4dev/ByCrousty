<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncCartRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Los items del carrito son requeridos',
            'items.array' => 'Los items deben ser un array',
            'items.*.product_id.required' => 'El ID del producto es requerido',
            'items.*.product_id.exists' => 'Uno o más productos no existen',
            'items.*.quantity.required' => 'La cantidad es requerida',
            'items.*.quantity.min' => 'La cantidad mínima es 1',
            'items.*.quantity.max' => 'La cantidad máxima es 99',
        ];
    }
}
