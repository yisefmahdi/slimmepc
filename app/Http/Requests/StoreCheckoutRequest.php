<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc|max:255',
            'first_name' => 'required|string|min:2|max:50',
            'last_name' => 'required|string|min:2|max:50',
            'street' => 'required_unless:shipping_method,pickup|string|max:100',
            'house_number' => 'required_unless:shipping_method,pickup|string|max:20',
            'addition' => 'nullable|string|max:20',
            'postcode' => ['required_unless:shipping_method,pickup', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
            'city' => 'required_unless:shipping_method,pickup|string|max:50',
            'country' => 'nullable|string|max:50',
            'phone' => 'required|string|min:6|max:20',
            'shipping_method' => 'required|in:delivery,pickup',
            'saved_address_id' => 'nullable|exists:addresses,id',
            'newsletter' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'E-mailadres is verplicht.',
            'email.email' => 'Vul een geldig e-mailadres in.',
            'first_name.required' => 'Voornaam is verplicht.',
            'last_name.required' => 'Achternaam is verplicht.',
            'street.required_unless' => 'Straat is verplicht bij verzending.',
            'house_number.required_unless' => 'Huisnummer is verplicht bij verzending.',
            'postcode.required_unless' => 'Postcode is verplicht bij verzending.',
            'postcode.regex' => 'Vul een geldige postcode in (bijv. 1234 AB).',
            'city.required_unless' => 'Plaats is verplicht bij verzending.',
            'phone.required' => 'Telefoonnummer is verplicht.',
            'shipping_method.required' => 'Kies een verzendmethode.',
            'shipping_method.in' => 'Ongeldige verzendmethode.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('postcode')) {
            $this->merge([
                'postcode' => strtoupper(trim((string) $this->input('postcode'))),
            ]);
        }
    }
}
