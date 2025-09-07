@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center py-5">
    <x-form title="Login (Admin)" route="admin-login" class="col-10 col-sm-8 col-md-6 col-lg-4 mt-md-2 mt-lg-5" buttonText="ログイン">
        <x-input label="<img src='{{ asset('icon/mail.svg') }}' class='w-100'>"
            type="text" name="email" placeholder="Email" labelClass="col-1" inputClass="col-10" />
        <x-input label="<img src='{{ asset('icon/lock.svg') }}'>"
            type="password" name="password" placeholder="Password" labelClass="col-1" inputClass="col-10" />
    </x-form>
</div>
@endsection
