<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contest;
use App\Models\Submission;
use App\Models\Attachment;
use App\Models\SubmissionComment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $jury = User::create([
            'name' => 'Jury User',
            'email' => 'jury@example.com',
            'password' => Hash::make('password'),
            'role' => 'jury',
        ]);

        $participant1 = User::create([
            'name' => 'Participant One',
            'email' => 'participant1@example.com',
            'password' => Hash::make('password'),
            'role' => 'participant',
        ]);

        $participant2 = User::create([
            'name' => 'Participant Two',
            'email' => 'participant2@example.com',
            'password' => Hash::make('password'),
            'role' => 'participant',
        ]);

        $contest1 = Contest::create([
            'title' => 'Science Fair 2024',
            'description' => 'Annual science fair for young researchers',
            'deadline_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        $contest2 = Contest::create([
            'title' => 'Art Competition',
            'description' => 'Digital art and design competition',
            'deadline_at' => now()->addDays(15),
            'is_active' => true,
        ]);

        $contest3 = Contest::create([
            'title' => 'Past Contest',
            'description' => 'Already finished contest',
            'deadline_at' => now()->subDays(10),
            'is_active' => false,
        ]);

        $submission1 = Submission::create([
            'contest_id' => $contest1->id,
            'user_id' => $participant1->id,
            'title' => 'AI in Healthcare',
            'description' => 'Research paper about AI applications in medicine',
            'status' => Submission::STATUS_DRAFT,
        ]);

        $submission2 = Submission::create([
            'contest_id' => $contest2->id,
            'user_id' => $participant1->id,
            'title' => 'Digital Landscape',
            'description' => 'Digital painting of mountain landscape',
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        $submission3 = Submission::create([
            'contest_id' => $contest1->id,
            'user_id' => $participant2->id,
            'title' => 'Renewable Energy',
            'description' => 'Study on solar panel efficiency',
            'status' => Submission::STATUS_NEEDS_FIX,
        ]);

        $submission4 = Submission::create([
            'contest_id' => $contest2->id,
            'user_id' => $participant2->id,
            'title' => 'Abstract Portrait',
            'description' => 'Abstract portrait in digital medium',
            'status' => Submission::STATUS_ACCEPTED,
        ]);

        SubmissionComment::create([
            'submission_id' => $submission2->id,
            'user_id' => $jury->id,
            'body' => 'Great work! Could you provide more details about your process?',
        ]);

        SubmissionComment::create([
            'submission_id' => $submission3->id,
            'user_id' => $jury->id,
            'body' => 'Please fix the formatting and add more references.',
        ]);

        SubmissionComment::create([
            'submission_id' => $submission3->id,
            'user_id' => $participant2->id,
            'body' => 'Thank you for the feedback. I will update it soon.',
        ]);
    }
}