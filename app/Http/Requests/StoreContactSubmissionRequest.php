<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactSubmissionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'in:reparatie,diagnose,data-recovery,zakelijk,stage,anders'],
            'request_type' => ['required', 'string', 'in:reparatie,zakelijk,algemene-vraag,stage'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,txt,csv'],
            'privacy_consent' => ['required', 'accepted'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * Custom validation messages (Dutch).
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vul je naam in.',
            'email.required' => 'Vul je e-mailadres in.',
            'email.email' => 'Voer een geldig e-mailadres in.',
            'subject.required' => 'Kies een onderwerp.',
            'request_type.required' => 'Kies het type aanvraag.',
            'message.required' => 'Schrijf je bericht.',
            'message.min' => 'Je bericht moet minimaal 10 tekens bevatten.',
            'attachment.max' => 'Het bestand mag maximaal 10 MB zijn.',
            'attachment.mimes' => 'Dit bestandstype wordt niet ondersteund.',
            'privacy_consent.accepted' => 'Ga akkoord met de verwerking van je gegevens.',
        ];
    }
}