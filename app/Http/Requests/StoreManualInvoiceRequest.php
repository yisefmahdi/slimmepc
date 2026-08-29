<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'device_info' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'subtotal' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'tax_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'total' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vul de naam van de klant in.',
            'email.required' => 'Vul het e-mailadres in.',
            'email.email' => 'Vul een geldig e-mailadres in.',
            'subtotal.required' => 'Vul het subtotaal in.',
            'subtotal.numeric' => 'Subtotaal moet een bedrag zijn.',
            'tax_percentage.required' => 'Vul het BTW percentage in.',
        ];
    }
}
