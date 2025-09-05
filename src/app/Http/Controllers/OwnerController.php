<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Genre;
use App\Models\Owner;
use App\Models\Reservation;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            ->paginate(6);

        $areas = Area::all();
        $genres = Genre::all();

        return view('owner-show', compact('shop', 'reservations', 'areas', 'genres'));
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

        return redirect()->route('owner-show', ['shop_id' => $shop_id], ['tab' => 'edit'])
            ->with('message', '店舗情報を更新しました');
    }

    public function checkin($checkin_token)
    {
        $reservation = Reservation::where('checkin_token', $checkin_token)->firstOrFail();

        if (Auth::guard('owner')->user()->id !== $reservation->shop->owner_id) {
            abort(403, 'この店舗の予約ではありません');
        }

        if ($reservation->visited) {
            return redirect()->route('owner-index')
                ->with('warning', 'すでにチェックイン済みです');
        }

        $reservation->update([
            'visited' => true,
        ]);

        return redirect()->route('owner-show', ['shop_id' => $reservation->shop_id])
            ->with('message', '来店確認が完了しました');
    }
}
