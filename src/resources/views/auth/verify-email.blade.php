@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center">
    <div class="box-height d-flex justify-content-center align-items-center
        rounded-1 shadow-right-bottom bg-white mt-2 mt-lg-5 col-10 col-md-6">
        <div>
            <p class="m-0 fs-1_56vw fw-medium">
                ご登録いただいたメールアドレスに<br>
                認証メールを送信しました<br>
                メール内のリンクから認証してください
            </p>
            <div class="text-center mt-3 mt-md-4 mt-xl-5">
                <a href="{{ URL::temporarySignedRoute(
                            'verification.verify',
                            now()->addMinutes(60),
                            ['id' => Auth::user()->id, 'hash' => sha1(Auth::user()->email)]
                        ) }}" target="_blank"
                    class="bg-primary text-white px-2 px-xl-3 py-1 btn-sm rounded-1 text-decoration-none fs-0_98vw">
                    認証する
                </a>
            </div>
            <form action="{{ route('verification.send') }}" method="post" class="text-center">
                @csrf
                <button type="submit" class="fw-normal border-0 text-decoration-none mt-3 fs-0_98vw text-primary bg-white">
                    認証メールを再送する
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
