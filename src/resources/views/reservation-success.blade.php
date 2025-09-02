@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <x-success-message buttonText="戻る" route="detail" :params="['shop_id' => $shop->id]">
        <p class="m-0 fs-1_56vw fw-medium">ご予約ありがとうございます</p>
    </x-success-message>
</div>
@endsection