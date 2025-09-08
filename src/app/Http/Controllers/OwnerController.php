<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Genre;
use App\Models\Owner;
use App\Models\Reservation;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class OwnerController extends Controller
{
    public function index()
    {
        //$owner = Auth::guard('owner')->user();
        $owner = Owner::find(1);
        $shops = Shop::where('owner_id', $owner->id)->get();
        $areas = Area::all();
        $genres = Genre::all();

        return view('owner-index', compact('shops', 'areas', 'genres'));
    }

    public function store(Request $request)
    {
        $owner = Owner::find(1);
        //$owner = Auth::guard('owner')->user();
        $shop = $request->only([
            'name',
            'area_id',
            'genre_id',
            'detail',
        ]);

        $image = $request->file('image')
            ->store('shop-img', 'public');
        $shop['image'] = basename($image);
        $shop['owner_id'] = $owner->id;

        Shop::create($shop);

        return redirect()->route('owner-index')
            ->with('message', '店舗を登録しました');
    }

    public function show($shop_id)
    {
        $shop = Shop::with(['area', 'genre'])->findOrFail($shop_id);

        $reservations = Reservation::where('shop_id', $shop_id)
            ->where('visited', false)
            ->with('user')
            ->orderBy('date')
            ->orderBy('time')
            ->paginate(6)
            ->appends(['tab' => 'reservation']);

        $areas = Area::all();
        $genres = Genre::all();

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

        return view('owner-show', compact('shop', 'reservations', 'areas', 'genres', 'checkouts'));
    }

    public function update(Request $request, $shop_id)
    {
        $shop = Shop::findOrFail($shop_id);

        $shopData = $request->only([
            'name',
            'area_id',
            'genre_id',
            'detail',
        ]);

        $image = $request->file('image')
            ->store('shop-img', 'public');
        $shopData['image'] = basename($image);
        if ($shop->image) {
            $image = 'shop-img/' . $shop->image;
            Storage::disk('public')->delete($image);
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
        $reservation = Reservation::where('checkin_token', $checkin_token)->firstOrFail();

        if (Auth::guard('owner')->user()->id !== $reservation->shop->owner_id) {
            abort(403, 'この店舗の予約ではありません');
        }

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
        $reservation = Reservation::findOrFail($request->reservation_id);
        //if ($reservation->shop->owner_id !== Auth::id()) {
            //abort(403, 'この予約にはアクセスできません');
        //}
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
            'success_url' => route('checkout-success', ['reservation' => $reservation->id]),
            'cancel_url' => route('checkout-cancel', ['reservation' => $reservation->id]),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $reservation = Reservation::findOrFail($request->reservation);
        $reservation->update([
            'paid' => true,
        ]);

        return redirect()->route('owner-show', [
            'shop_id' => $reservation->shop_id,
            'tab' => 'checkout',
        ]);
    }

    public function cancel(Request $request)
    {
        $reservation = Reservation::findOrFail($request->reservation_id);

        return redirect()->route('owner-show', [
            'shop_id' => $reservation->shop_id,
            'tab' => 'checkout',
        ]);
    }
}
