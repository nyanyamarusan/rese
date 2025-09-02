@extends('layouts.app')

@section('content')
<div class="container text-center d-flex justify-content-center mb-5">
    <div class="card text-white bg-primary shadow-right-bottom rounded-1 col-md-8 col-lg-6">
        <div class="card-body d-flex flex-column align-items-center">
            <h2 class="fs-5 my-3">来店用QRコード</h2>
            <div class="d-flex justify-content-center text-start card-body table-bg text-white rounded-1 col-10 col-md-8">
                <table class="w-100">
                    <tr>
                        <th class="col-6 col-md-5 py-1 show-text">Shop</th>
                        <td class="col-6 col-md-7 py-1 show-text">{{ $reservation->shop->name }}</td>
                    </tr>
                    <tr>
                        <th class="col-6 col-md-5 py-1 show-text">Date</th>
                        <td class="col-6 col-md-7 py-1 show-text">{{ $reservation->date->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th class="col-6 col-md-5 py-1 show-text">Time</th>
                        <td class="col-6 col-md-7 py-1 show-text">{{ $reservation->time->format('H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="col-6 col-md-5 py-1 show-text">Number</th>
                        <td class="col-6 col-md-7 py-1 show-text">{{ $reservation->number }}人</td>
                    </tr>
                </table>
            </div>
            <div class="my-4">
                <img src="{{ $dataUri }}" class="w-75">
            </div>
            <p>このQRコードを店舗スタッフに<br>読み取ってもらってください</p>
        </div>
    </div>
</div>
@endsection
