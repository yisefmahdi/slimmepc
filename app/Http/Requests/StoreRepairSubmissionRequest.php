<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairSubmissionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'device' => ['required', 'string', 'max:255'],
            'problems' => ['required', 'array', 'min:1'],
            'problems.*' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'serial' => ['nullable', 'string', 'max:255'],
            'data_importance' => ['required', 'string', 'in:Ja, gegevens behouden,Nee,Weet ik niet'],
            'opened_before' => ['required', 'string', 'in:Ja,Nee,Weet ik niet'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'postcode' => ['required', 'string', 'max:20'],
            'delivery_method' => ['required', 'string', 'in:Naar de winkel brengen,Eerst telefonisch advies,Ophalen / bezorgen'],
            'contact_preference' => ['required', 'string', 'in:WhatsApp,Telefoon,E-mail'],
            'privacy' => ['required', 'accepted'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * Custom validation messages (Dutch).
     */
    public function messages(): array
    {
        return [
            'device.required' => 'Kies een apparaat.',
            'problems.required' => 'Kies minstens één probleem.',
            'problems.min' => 'Kies minstens één probleem.',
            'description.required' => 'Beschrijf het probleem.',
            'brand.required' => 'Vul het merk in.',
            'model.required' => 'Vul het model in.',
            'data_importance.required' => 'Maak een keuze bij "Belangrijke gegevens".',
            'opened_before.required' => 'Maak een keuze bij "Eerder geopend".',
            'name.required' => 'Vul je naam in.',
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Voer een geldig e-mailadres in.',
            'phone.required' => 'Vul je telefoonnummer in.',
            'postcode.required' => 'Vul je postcode in.',
            'delivery_method.required' => 'Kies hoe je verder wilt.',
            'contact_preference.required' => 'Kies je contactvoorkeur.',
            'privacy.accepted' => 'Ga akkoord met de verwerking van je gegevens.',
            'photos.max' => 'Je kunt maximaal 5 afbeeldingen toevoegen.',
            'photos.*.image' => 'Alleen afbeeldingen zijn toegestaan.',
            'photos.*.mimes' => 'Alleen JPG, PNG of WEBP afbeeldingen.',
            'photos.*.max' => 'Elke afbeelding mag maximaal 5 MB zijn.',
        ];
    }
}
