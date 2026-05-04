<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
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
            'product_id' => 'required|integer|exists:products,id',
            'customer_name' => 'required|string|min:3|max:255',
            'customer_email' => 'required|string|email|max:255',
            'customer_phone' => ['required', 'string', 'max:16', 'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'],
            'customer_company' => 'nullable|string|max:255',
            'customer_zip_code' => ['required', 'string', 'size:9', 'regex:/^\d{5}-\d{3}$/'],
            'customer_city' => 'required|string|min:2|max:255',
            'customer_street' => 'required|string|min:3|max:255',
            'customer_number' => ['required', 'string', 'max:10', 'regex:/^[0-9A-Za-z\-\/ ]+$/'],
            'customer_reference' => 'required|string|min:3|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'message' => 'nullable|string|max:1000',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Informe um telefone valido no formato (00) 00000-0000.',
            'customer_zip_code.regex' => 'Informe um CEP valido no formato 00000-000.',
            'customer_number.regex' => 'O numero deve conter apenas letras, numeros, espaco, barra ou hifen.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'produto',
            'customer_name' => 'nome',
            'customer_email' => 'e-mail',
            'customer_phone' => 'telefone',
            'customer_company' => 'empresa',
            'customer_zip_code' => 'CEP',
            'customer_city' => 'cidade',
            'customer_street' => 'rua',
            'customer_number' => 'numero',
            'customer_reference' => 'ponto de referencia',
            'start_date' => 'data de inicio',
            'end_date' => 'data de termino',
            'message' => 'mensagem',
        ];
    }

    protected function prepareForValidation(): void
    {
        $fields = [
            'customer_name',
            'customer_email',
            'customer_phone',
            'customer_company',
            'customer_zip_code',
            'customer_city',
            'customer_street',
            'customer_number',
            'customer_reference',
            'message',
        ];

        $normalized = [];

        foreach ($fields as $field) {
            $value = $this->input($field);

            if (is_string($value)) {
                $normalized[$field] = trim($value);
            }
        }

        if (isset($normalized['customer_email'])) {
            $normalized['customer_email'] = mb_strtolower($normalized['customer_email']);
        }

        $this->merge($normalized);
    }
}
