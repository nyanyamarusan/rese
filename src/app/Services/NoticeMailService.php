<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NoticeMail;

class NoticeMailService
{
    // 本体処理
    public function send(array $recipients, string $subject, string $body)
    {
        foreach ($recipients as $user) {
            Mail::to($user->email)->send(new NoticeMail($subject, $body));
        }
    }

    // サイト管理者向け対象取得
    public function getRecipientsForAdmin(): array
    {
        return User::all()->toArray(); // 全ユーザー
    }

    // 店舗代表者向け対象取得
    public function getRecipientsForShop(int $shopId): array
    {
        return User::where('shop_id', $shopId)->get()->toArray(); // 自分の店舗の利用者
    }
}
