<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $submission = $this->route('submission');
        
        return $user && (
            $user->isJury() || 
            ($user->isParticipant() && $submission->user_id === $user->id)
        );
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|max:1000',
        ];
    }
}