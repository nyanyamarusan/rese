<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NoticeMail;

class NoticeMailService
{
    public function send(array $recipients, string $subject, string $body)
    {
        foreach ($recipients as $user) {
            Mail::to($user->email)->send(new NoticeMail($subject, $body));
        }
    }

    public function getRecipientsForAdmin(): array
    {
        return User::all()->toArray();
    }
}
