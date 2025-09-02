<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Genre;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::with(['area', 'genre'])->get();
        $areas = Area::all();
        $genres = Genre::all();

        $user = User::find(1);
        $userLikes = $user->likes()->pluck('shop_id')->toArray();
        return view('index', compact('shops', 'areas', 'genres', 'userLikes'));
    }

    public function search(Request $request)
    {
        $shops = Shop::with(['area', 'genre'])->areaSearch($request->area_id)
            ->genreSearch($request->genre_id)
            ->keywordSearch($request->keyword)
            ->get();
        $areas = Area::all();
        $genres = Genre::all();
        return view('index', compact('shops', 'areas', 'genres'));
    }

    public function detail(Request $request, $shop_id)
    {
        $shop = Shop::with(['area', 'genre',])->findOrFail($shop_id);

        $times = [];

        return view('show', compact('shop', 'times'));
    }

    public function getTimes(Request $request, $shop_id)
    {
        $shop = Shop::with('reservations')->findOrFail($shop_id);

        $selectedDate = $request->date;
        $selectedDateCarbon = Carbon::createFromFormat('Y-m-d', $selectedDate);
        $now = Carbon::now();

        $bookedTimes = $shop->reservations()
            ->where('date', $selectedDateCarbon->toDateString())
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
}
