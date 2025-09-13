<?php

namespace App\Services;

use App\Mail\NoticeMail;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class NoticeMailService
{
    public function send(Collection $recipients, string $subject, string $body)
    {
        foreach ($recipients as $user) {
            Mail::to($user->email)->send(new NoticeMail($subject, $body));
        }
    }

    public function getRecipientsForAdmin()
    {
        return User::all();
    }
}
