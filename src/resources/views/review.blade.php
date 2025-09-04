@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center py-5">
    <x-form title="Review" route="review-post" params="['reservation_id' => $reservation->id]"
        class="col-12 col-sm-10 col-md-6 col-xl-5 mt-md-2 mt-lg-5" buttonText="送信">
        <div class="card col-10 my-3">
            <div class="text-start card-body table-bg text-white rounded-1 shadow-right-bottom">
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
        </div>
        <div class="my-3">
            @for ($i = 1; $i <= 5; $i++)
            <div class="mb-1 form-check">
                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}"
                    class="border-secondary form-check-input pointer">
                <label for="star{{ $i }}" class="form-check-label fw-medium">
                    {{ $i }} -
                    @switch($i)
                        @case(1) とても悪い @break
                        @case(2) 悪い @break
                        @case(3) 普通 @break
                        @case(4) 良い @break
                        @case(5) とても良い @break
                    @endswitch
                </label>
            </div>
        @endfor
        </div>
        <div class="text-start form-group col-10">
            <label for="comment" class="d-block form-label fw-bold">Comment</label>
            <textarea name="comment" id="comment" rows="5" class="form-control" placeholder="Comment"></textarea>
        </div>
    </x-form>
</div>
@endsection
