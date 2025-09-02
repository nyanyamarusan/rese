@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <x-success-message buttonText="戻る" route="mypage">
        <p class="m-0 fs-1_56vw fw-medium">レビューを送信しました</p>
    </x-success-message>
</div>
@endsection