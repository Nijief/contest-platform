<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        
        if ($this->isMethod('POST')) {
            return $user && $user->isParticipant();
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $submission = $this->route('submission');
            return $user && $submission->user_id === $user->id && $submission->canBeEdited();
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'contest_id' => 'required|exists:contests,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }
}