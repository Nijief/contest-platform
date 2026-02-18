<?php

namespace App\Http\Requests;

use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatusChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isJury();
    }

    public function rules(): array
    {
        $submission = $this->route('submission');
        
        return [
            'status' => [
                'required',
                Rule::in($submission->getAvailableStatusTransitions()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Недопустимый переход статуса',
        ];
    }
}