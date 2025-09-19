<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Genre;
use App\Models\Reservation;
use App\Models\Shop;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class OwnerController extends Controller
{
    public function index()
    {
        $owner = Auth::guard('owner')->user();
        $shops = Shop::where('owner_id', $owner->id)->get();
        $areas = Area::all();
        $genres = Genre::all();

        $period = CarbonPeriod::create('00:00', '60 minutes', '23:00');
        $times = [];
        foreach ($period as $time) {
            $times[] = $time->format('H:i');
        }

        return view('owner-index', compact('shops', 'areas', 'genres', 'times'));
    }

    public function store(Request $request)
    {
        $owner = Auth::guard('owner')->user();
        $shop = $request->only([
            'name',
            'area_id',
            'genre_id',
            'detail',
            'open_time',
            'close_time',
        ]);

        $image = $request->file('image')
            ->store('shop-img', env('FILESYSTEM_DISK', 'public'));
        $shop['image'] = basename($image);
        $shop['owner_id'] = $owner->id;

        Shop::create($shop);

        return redirect()->route('owner-index')
            ->with('message', '店舗を登録しました');
    }

    public function show($shop_id)
    {
        $owner = Auth::guard('owner')->user();
        $shop = Shop::with(['area', 'genre'])
            ->where('owner_id', $owner->id)
            ->findOrFail($shop_id);

        $reservations = Reservation::where('shop_id', $shop_id)
            ->where('visited', false)
            ->with('user')
            ->orderBy('date')
            ->orderBy('time')
            ->paginate(6)
            ->appends(['tab' => 'reservation']);

        $areas = Area::all();
        $genres = Genre::all();

        $period = CarbonPeriod::create('00:00', '60 minutes', '23:00');
        $times = [];
        foreach ($period as $time) {
            $times[] = $time->format('H:i');
        }

        $today = Carbon::today();

        $checkouts = Reservation::where('shop_id', $shop_id)
            ->where('date', $today)
            ->where('visited', true)
            ->where('paid', false)
            ->with('user')
            ->orderBy('date')
            ->orderBy('time')
            ->paginate(6)
            ->appends(['tab' => 'checkout']);

        return view('owner-show', compact('shop', 'reservations', 'areas', 'genres', 'checkouts', 'times'));
    }

    public function update(Request $request, $shop_id)
    {
        $owner = Auth::guard('owner')->user();
        $shop = Shop::where('owner_id', $owner->id)->findOrFail($shop_id);

        $shopData = $request->only([
            'name',
            'area_id',
            'genre_id',
            'detail',
            'open_time',
            'close_time',
        ]);

        $image = $request->file('image')
            ->store('shop-img', env('FILESYSTEM_DISK', 'public'));
        $shopData['image'] = basename($image);
        if ($shop->image) {
            $image = 'shop-img/' . $shop->image;
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($image);
        }

        $shop->update($shopData);

        return redirect()->route('owner-show', [
            'shop_id' => $shop_id,
            'tab' => 'edit',
        ])->with([
            'status' => 'success',
            'message' => '店舗情報を更新しました',
        ]);
    }

    public function checkin($checkin_token)
    {
        $owner = Auth::guard('owner')->user();
        $shop = Shop::where('owner_id', $owner->id)->firstOrFail();
        $reservation = Reservation::where('checkin_token', $checkin_token)
            ->where('shop_id', $shop->id)
            ->firstOrFail();

        if ($reservation->visited) {
            return redirect()->route('owner-show', [
                'shop_id' => $reservation->shop_id,
                'tab' => 'reservation',
            ])->with([
                'status' => 'error',
                'message' => 'すでにチェックイン済みです',
            ]);
        }

        $reservation->update([
            'visited' => true,
        ]);

        return redirect()->route('owner-show', [
            'shop_id' => $reservation->shop_id,
            'tab' => 'reservation',
        ])->with([
            'status' => 'success',
            'message' => '来店確認が完了しました',
        ]);
    }

    public function checkout(Request $request)
    {
        $owner = Auth::guard('owner')->user();
        $shop = Shop::where('owner_id', $owner->id)->firstOrFail();
        $reservation = Reservation::where('shop_id', $shop->id)->findOrFail($request->reservation_id);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => 'Reservation #' . $reservation->id,
                    ],
                    'unit_amount' => $request->amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_email' => $reservation->user->email,
            'success_url' => route('checkout-success', ['session_id' => '{CHECKOUT_SESSION_ID}']),
            'metadata' => [
                'reservation_id' => $reservation->id,
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $owner = Auth::guard('owner')->user();
        $shop = Shop::where('owner_id', $owner->id)->firstOrFail();

        $session_id = $request->session_id;

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = Session::retrieve($session_id);
        $reservation_id = $session->metadata->reservation_id;

        if ($session->payment_status !== 'paid') {
            return redirect()->route('owner-show', [
                'shop_id' => $shop->id,
                'tab' => 'checkout',
            ])->with([
                'status' => 'error',
                'message' => '支払いが完了していません',
            ]);
        }

        $reservation = Reservation::where('shop_id', $shop->id)
            ->findOrFail($reservation_id);

        $reservation->update(['paid' => true]);

        return redirect()->route('owner-show', [
            'shop_id' => $shop->id,
            'tab' => 'checkout',
        ])->with([
            'status' => 'success',
            'message' => '支払いが完了しました',
        ]);
    }
}
