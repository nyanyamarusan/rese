<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    public function checkin($checkin_token)
    {
        $reservation = Reservation::where('checkin_token', $checkin_token)->firstOrFail();

        if (Auth::user()->id !== $reservation->shop->owner_id) {
            abort(403, 'この店舗の予約ではありません');
        }

        if ($reservation->visited) {
            return redirect()->route('owner-index')
                ->with('warning', 'すでにチェックイン済みです');
        }

        $reservation->update([
            'visited' => true,
        ]);

        return redirect()->route('owner-show')
            ->with('success', '来店確認が完了しました');
    }
}
