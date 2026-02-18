<?php

namespace App\Listeners;

use App\Events\NewJuryComment;
use App\Models\Notification; // Добавить этот импорт
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyParticipantAboutJuryComment implements ShouldQueue
{
    public function handle(NewJuryComment $event): void
    {
        Notification::create([ // Теперь работает
            'user_id' => $event->submission->user_id,
            'type' => 'jury_comment_added',
            'data' => json_encode([
                'submission_id' => $event->submission->id,
                'submission_title' => $event->submission->title,
                'comment_id' => $event->comment->id,
                'comment_excerpt' => substr($event->comment->body, 0, 100),
                'jury_name' => $event->comment->user->name,
            ]),
        ]);
    }
}