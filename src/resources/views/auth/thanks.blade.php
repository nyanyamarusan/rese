@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <x-success-message buttonText="ログインする" route="login">
        <p class="m-0 fs-1_56vw fw-medium">会員登録ありがとうございます</p>
    </x-success-message>
</div>
@endsection
