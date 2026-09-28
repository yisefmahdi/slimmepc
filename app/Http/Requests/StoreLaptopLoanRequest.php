<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaptopLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'customer_email' => ['required', 'email', 'max:255'],
            'repair_number' => ['nullable', 'string', 'max:255'],
            'laptop_type' => ['required', 'string', 'max:255'],
            'given_at' => ['required', 'date'],
            'photos' => ['nullable', 'array', 'max:20'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vul de naam van de klant in.',
            'address.required' => 'Vul het adres in.',
            'postcode.required' => 'Vul de postcode in.',
            'city.required' => 'Vul de plaats in.',
            'phone.required' => 'Vul het telefoonnummer in.',
            'customer_email.required' => 'Vul het e-mailadres in.',
            'customer_email.email' => 'Vul een geldig e-mailadres in.',
            'laptop_type.required' => 'Vul het type laptop in.',
            'given_at.required' => 'Vul datum & tijd van uitgifte in.',
            'photos.max' => 'Maximaal 20 foto\'s per uitgifte.',
            'photos.*.image' => 'Elke foto moet een afbeelding zijn.',
            'photos.*.mimes' => 'Alleen JPG, PNG, WEBP of AVIF toegestaan.',
            'photos.*.max' => 'Elke foto mag maximaal 10MB zijn.',
        ];
    }
}
