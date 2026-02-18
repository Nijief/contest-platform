<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $submission = $this->route('submission');
        
        return $user && $submission->user_id === $user->id && $submission->canBeEdited();
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,zip,png,jpg,jpeg',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.max' => 'Файл не должен превышать 10MB',
            'file.mimes' => 'Разрешены только файлы форматов: pdf, zip, png, jpg',
        ];
    }
}