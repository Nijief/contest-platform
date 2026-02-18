<?php

namespace App\Listeners;

use App\Events\SubmissionStatusChanged;
use App\Jobs\NotifyStatusChangedJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStatusChangedNotification implements ShouldQueue
{
    public function handle(SubmissionStatusChanged $event): void
    {
        dispatch(new NotifyStatusChangedJob($event->submission, $event->changedBy));
    }
}