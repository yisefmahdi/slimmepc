<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKlantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('klant')->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'klantnummer' => ['nullable', 'string', 'max:50', Rule::unique('users', 'klantnummer')->ignore($this->route('klant')->id)],
            'street' => ['nullable', 'string', 'max:255'],
            'house_number' => ['nullable', 'string', 'max:20'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'role' => ['sometimes', Rule::in(['user', 'technician', 'admin'])],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }

    /**
     * Custom validation messages (Dutch).
     */
    public function messages(): array
    {
        return [
            'name.required' => 'De naam is verplicht.',
            'email.required' => 'Het e-mailadres is verplicht.',
            'email.email' => 'Voer een geldig e-mailadres in.',
            'email.unique' => 'Dit e-mailadres is al in gebruik.',
            'klantnummer.unique' => 'Dit klantnummer is al in gebruik.',
            'password.min' => 'Het wachtwoord moet minimaal 8 tekens bevatten.',
        ];
    }
}
