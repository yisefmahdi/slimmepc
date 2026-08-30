<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'device_type' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'received_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', 'string', 'in:laptop,ipad_iphone,playstation_xbox'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vul de naam van de klant in.',
            'customer_email.required' => 'Vul het e-mailadres in.',
            'customer_email.email' => 'Vul een geldig e-mailadres in.',
            'device_type.required' => 'Vul het apparaattype in.',
            'phone_number.required' => 'Vul het telefoonnummer in.',
            'received_at.required' => 'Vul datum & tijd van ontvangst in.',
            'type.in' => 'Ongeldig type.',
        ];
    }
}
