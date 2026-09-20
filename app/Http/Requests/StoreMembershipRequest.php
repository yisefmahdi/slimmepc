<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMembershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Lidmaatschap is ook zonder login mogelijk.
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_type' => 'required|in:particulier,zakelijk',
            'customer_gender' => 'required|in:man,vrouw,anders,niet-zeggen',
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-]+$/u'],
            'customer_email' => 'required|email:rfc|max:255|unique:memberships,customer_email',
            'customer_phone' => ['required', 'string', 'max:25', 'regex:/^\+?[0-9\s\-\/().]{6,25}$/'],
            'customer_address' => 'required|string|max:255',
            'postcode' => ['required', 'string', 'max:20', 'regex:/^[1-9][0-9]{3}\s?[a-zA-Z]{2}$/'],
            'city' => 'required|string|max:100',
            'terms' => 'accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_type.required' => 'Kies hoe je lid wilt worden.',
            'customer_gender.required' => 'Kies je geslacht.',
            'name.required' => 'Vul je naam in.',
            'name.regex' => 'De naam mag alleen letters bevatten.',
            'customer_email.required' => 'Vul je e-mailadres in.',
            'customer_email.email' => 'Vul een geldig e-mailadres in.',
            'customer_email.unique' => 'Dit e-mailadres is al geregistreerd.',
            'customer_phone.required' => 'Vul je telefoonnummer in.',
            'customer_phone.regex' => 'Vul een geldig telefoonnummer in.',
            'customer_address.required' => 'Vul je adres in.',
            'postcode.required' => 'Vul je postcode in.',
            'postcode.regex' => 'Vul een geldige postcode in (bijv. 1234 AB).',
            'city.required' => 'Vul je woonplaats in.',
            'terms.accepted' => 'Ga akkoord met de voorwaarden.',
        ];
    }
}
