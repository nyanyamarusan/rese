@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('message'))
    <div class="alert alert-success text-success" role="alert">
        {{ session('message') }}
    </div>
    @endif
</div>
<div class="container d-flex justify-content-center align-items-center py-5">
    <x-form title="New Owner" route="admin-store" class="col-10 col-sm-8 col-md-6 col-lg-4 mt-md-2 mt-lg-5" buttonText="登録">
        <x-input label="<img src='{{ asset('icon/user.svg') }}'>"
            type="text" name="name" placeholder="Ownername" labelClass="col-1" inputClass="col-10" />
        <x-input label="<img src='{{ asset('icon/mail.svg') }}' class='w-100'>"
            type="text" name="email" placeholder="Email" labelClass="col-1" inputClass="col-10" />
        <x-input label="<img src='{{ asset('icon/lock.svg') }}'>"
            type="password" name="password" placeholder="Password" labelClass="col-1" inputClass="col-10" />
    </x-form>
</div>
@endsection
