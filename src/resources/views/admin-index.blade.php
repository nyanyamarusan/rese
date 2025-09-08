@extends('layouts.app')

@section('content')
@php
    $activeTab = request('tab', 'store');
@endphp
<div class="container">
    @if (session('message'))
    <div class="alert alert-success text-success" role="alert">
        {{ session('message') }}
    </div>
    @endif
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item show-text fw-normal">
            <a class="nav-link p-2 {{ $activeTab === 'store' ? 'active bg-primary text-white' : 'text-black' }}"
                href="{{ route('admin-index', ['tab' => 'store']) }}">
                新規店舗代表者作成
            </a>
        </li>
        <li class="nav-item show-text fw-normal">
            <a class="nav-link p-2 {{ $activeTab === 'send' ? 'active bg-primary text-white' : 'text-black' }}"
                href="{{ route('admin-index', ['tab' => 'send']) }}">
                メール送信
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade {{ $activeTab === 'store' ? 'show active' : '' }}" id="store">
            <div class="container d-flex justify-content-center align-items-center py-5">
                <x-form title="New Owner" route="admin-store" class="col-10 col-sm-8 col-md-6 col-lg-4 mt-md-2 mt-lg-5" buttonText="作成">
                    <x-input label="<img src='{{ asset('icon/user.svg') }}'>"
                        type="text" name="name" placeholder="Ownername" labelClass="col-1" inputClass="col-10" />
                    <x-input label="<img src='{{ asset('icon/mail.svg') }}' class='w-100'>"
                        type="text" name="email" placeholder="Email" labelClass="col-1" inputClass="col-10" />
                    <x-input label="<img src='{{ asset('icon/lock.svg') }}'>"
                        type="password" name="password" placeholder="Password" labelClass="col-1" inputClass="col-10" />
                </x-form>
            </div>
        </div>
        <div class="tab-pane fade {{ $activeTab === 'send' ? 'show active' : '' }}" id="send">
            <div class="container d-flex justify-content-center align-items-center py-5">
                <form action="{{ route('send') }}" method="POST"
                    class="col-11 col-md-8 col-xl-6 table-bg p-3 rounded-1 shadow-right-bottom">
                    @csrf
                    <div class="form-group mb-4 col-md-8">
                        <label for="subject" class="form-label text-white">件名</label>
                        <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}">
                    </div>
                    <div class="form-group mb-4">
                        <label for="body" class="form-label text-white">本文</label>
                        <textarea name="body" id="body" rows="8" class="form-control">{{ old('body') }}</textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">送信</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
