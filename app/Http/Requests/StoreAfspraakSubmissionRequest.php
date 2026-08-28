<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAfspraakSubmissionRequest extends FormRequest
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
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255'],
            'street'         => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:50'],
            'postcode'       => ['required', 'string', 'max:10'],
            'house_number'   => ['required', 'string', 'max:10'],
            'city'           => ['required', 'string', 'max:255'],
            // Device is plain text (no FK to the CMS device list)
            'device'         => ['required', 'string', 'max:255'],
            'problem'        => ['required', 'string', 'max:5000'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['required', 'string', 'in:09:00 - 11:00,11:00 - 13:00,13:00 - 15:00,15:00 - 17:00,17:00 - 19:00'],
        ];
    }

    /**
     * Get custom validation messages (Dutch).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'           => 'Vul uw naam in.',
            'email.required'          => 'Vul uw e-mailadres in.',
            'email.email'             => 'Vul een geldig e-mailadres in.',
            'street.required'         => 'Vul uw straatnaam in.',
            'phone.required'          => 'Vul uw telefoonnummer in.',
            'postcode.required'       => 'Vul uw postcode in.',
            'house_number.required'    => 'Vul uw huisnummer in.',
            'city.required'           => 'Vul uw stad in.',
            'device.required'         => 'Kies een apparaat.',
            'problem.required'        => 'Beschrijf kort uw probleem.',
            'preferred_date.required' => 'Kies een gewenste datum.',
            'preferred_date.after_or_equal' => 'Kies een datum in de toekomst.',
            'preferred_time.required' => 'Kies een gewenst tijdstip.',
            'preferred_time.in'       => 'Kies een geldig tijdstip.',
        ];
    }
}
