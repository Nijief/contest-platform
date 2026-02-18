<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_NEEDS_FIX = 'needs_fix';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'contest_id',
        'user_id',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function comments()
    {
        return $this->hasMany(SubmissionComment::class);
    }

    public function scannedAttachments()
    {
        return $this->attachments()->where('status', 'scanned');
    }

    public function hasMinimumAttachments(): bool
    {
        return $this->scannedAttachments()->count() >= 1;
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_NEEDS_FIX]);
    }

    public function getAvailableStatusTransitions(): array
    {
        return match($this->status) {
            self::STATUS_DRAFT => [self::STATUS_SUBMITTED],
            self::STATUS_SUBMITTED => [self::STATUS_ACCEPTED, self::STATUS_NEEDS_FIX, self::STATUS_REJECTED],
            self::STATUS_NEEDS_FIX => [self::STATUS_SUBMITTED],
            default => [],
        };
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, $this->getAvailableStatusTransitions());
    }
}