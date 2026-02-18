<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Filesystem\FilesystemManager; 

class AttachmentService
{
    use FilesystemManager;
    
    protected $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('s3');
    }

    public function upload(UploadedFile $file, Submission $submission, User $user): Attachment
    {
        if ($submission->attachments()->count() >= 3) {
            throw new \Exception('Maximum 3 files per submission');
        }

        $storageKey = 'submissions/' . $submission->id . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        return DB::transaction(function () use ($file, $submission, $user, $storageKey) {
            $this->disk->put($storageKey, file_get_contents($file));

            $attachment = Attachment::create([
                'submission_id' => $submission->id,
                'user_id' => $user->id,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'storage_key' => $storageKey,
                'status' => Attachment::STATUS_PENDING,
            ]);

            dispatch(new \App\Jobs\ScanAttachmentJob($attachment));

            return $attachment;
        });
    }

    public function markScanned(Attachment $attachment): Attachment
    {
        if (!$attachment->isPending()) {
            throw new \Exception('Attachment is not in pending status');
        }

        return DB::transaction(function () use ($attachment) {
            $attachment->update([
                'status' => Attachment::STATUS_SCANNED,
            ]);

            return $attachment->fresh();
        });
    }

    public function reject(Attachment $attachment, string $reason): Attachment
    {
        if (!$attachment->isPending()) {
            throw new \Exception('Attachment is not in pending status');
        }

        return DB::transaction(function () use ($attachment, $reason) {
            $attachment->update([
                'status' => Attachment::STATUS_REJECTED,
                'rejection_reason' => $reason,
            ]);

            $this->disk->delete($attachment->storage_key);

            return $attachment->fresh();
        });
    }

    public function getSignedUrl(Attachment $attachment, User $user): string
    {
        $submission = $attachment->submission;

        if (!$user->isJury() && $submission->user_id !== $user->id) {
            throw new \Exception('Unauthorized to download this file');
        }

        if ($attachment->isRejected()) {
            throw new \Exception('Attachment is rejected and not available');
        }

        return $this->disk->temporaryUrl(
            $attachment->storage_key,
            now()->addMinutes(30)
        );
    }

    public function delete(Attachment $attachment, User $user): bool
    {
        $submission = $attachment->submission;

        if ($submission->user_id !== $user->id || !$submission->canBeEdited()) {
            throw new \Exception('Cannot delete attachment in current state');
        }

        return DB::transaction(function () use ($attachment) {
            $this->disk->delete($attachment->storage_key);
            return $attachment->delete();
        });
    }
}