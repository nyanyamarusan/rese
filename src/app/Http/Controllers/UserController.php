<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{
    public function like($shop_id)
    {
        $user = Auth::user()->load('likes');
        $shop = Shop::findOrFail($shop_id);

        if ($shop->isLikedBy($user)) {
            $user->likes()->detach($shop);
        } else {
            $user->likes()->attach($shop);
        }

        return redirect()->back();
    }

    public function reservation(ReservationRequest $request)
    {
        $user = Auth::user();
        $shop = Shop::findOrFail($request->shop_id);
        $reservationData = $request->only([
            'date',
            'time',
        ]);

        $reservationData['user_id'] = $user->id;
        $reservationData['shop_id'] = $shop->id;
        $reservationData['checkin_token'] = Str::uuid();

        Reservation::create($reservationData);

        return view('reservation-success', compact('shop'));
    }

    public function mypage()
    {
        $user = Auth::user();
        $reservations = $user->reservations()->with('shop', 'review')->get();
        $userLikes = $user->likes()->pluck('shop_id')->toArray();
        $times = [];

        return view('mypage', compact('user', 'reservations', 'userLikes', 'times'));
    }

    public function update(ReservationRequest $request, $reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);

        $reservationData = $request->only([
            'date',
            'time',
            'number',
        ]);

        $reservationData['checkin_token'] = Str::uuid();

        $reservation->update($reservationData);

        return view('update-success');
    }

    public function getTimesForUpdate(Request $request, $reservation_id)
    {
        $reservation = Reservation::with('shop')->findOrFail($reservation_id);
        $shop = $reservation->shop;

        $selectedDate = $request->date;
        $selectedDateCarbon = Carbon::createFromFormat('Y-m-d', $selectedDate);
        $now = Carbon::now();

        $bookedTimes = $shop->reservations()
            ->where('date', $selectedDateCarbon->toDateString())
            ->where('id', '!=', $reservation_id)
            ->pluck('time')
            ->toArray();

        $start = $shop->open_time->copy();
        $end   = $shop->close_time->copy();
        $times = [];

        while ($start->lt($end)) {
            $timeStr = $start->format('H:i');

            if ($selectedDateCarbon->isSameDay($now) && $start->lte($now)) {
                $start->addHour();
                continue;
            }

            if (in_array($timeStr, $bookedTimes)) {
                $start->addHour();
                continue;
            }

            $times[] = $timeStr;
            $start->addHour();
        }

        return response()->json($times);
    }

    public function destroy($reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);
        $reservation->delete();

        return view('delete-success');
    }

    public function create($reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);

        return view('review', compact('reservation'));
    }

    public function store(Request $request, $reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);

        $reviewData = $request->only([
            'rating',
            'comment',
        ]);

        $reviewData['reservation_id'] = $reservation->id;

        Review::create($reviewData);

        return view('review-success');
    }

    public function showQr($reservation_id)
    {
        $reservation = Reservation::findOrFail($reservation_id);
        $qrCode = new QrCode(route('checkin', ['checkin_token' => $reservation->checkin_token]));
        $writer = new SvgWriter();
        $result = $writer->write($qrCode);

        $dataUri = $result->getDataUri();

        return view('qr', compact('reservation', 'dataUri'));
    }
}
