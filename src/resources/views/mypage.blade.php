@extends('layouts.app')

@section('content')
<div class="container px-4 mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between">
        <h2 class="fs-3 fw-bold mb-4 mt-2 col-md-4 d-none d-md-block"></h2>
        <h2 class="fs-3 fw-bold mb-4 mt-2 col-md-7">{{ $user->name }}さん</h2>
    </div>
    <div class="d-flex justify-content-between flex-column flex-md-row">
        <div class="col-md-4">
            <h3 class="fs-5 fw-bold mb-3">予約状況</h3>
            @if ($reservations?->isNotEmpty())
                @foreach ($reservations->where('visited', false) as $index => $reservation)
                    <div class="card bg-primary text-white shadow-right-bottom rounded-1 p-lg-2 mb-3" id="reservation-box-{{ $reservation->id }}">
                        <div class="card-body">
                            <form action="{{ route('mypage-update', ['reservation_id' => $reservation->id]) }}" method="post">
                                @csrf
                                @method('PATCH')
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-4">
                                        <img src="{{ asset('icon/clock.svg') }}" class="clock-icon">
                                        <p class="m-0 show-text fw-normal">予約{{ $index + 1 }}</p>
                                    </div>
                                    <button type="button" onclick="document.getElementById('reservation-box-{{ $reservation->id }}').style.display='none'"
                                        class="border-0 bg-primary" aria-label="閉じる">
                                        <img src="{{ asset('icon/close.svg') }}" class="close-icon">
                                    </button>
                                </div>
                                <table class="w-100">
                                    <tr>
                                        <th class="col-4 col-md-5 col-lg-4 col-xl-3 py-1 py-xl-2 show-text fw-normal">Shop</th>
                                        <td class="col-8 col-md-7 col-lg-8 col-xl-9 py-1 py-xl-2 show-text fw-normal">{{ $reservation->shop->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="col-4 col-md-5 col-lg-4 col-xl-3 py-1 py-xl-2 show-text fw-normal">Date</th>
                                        <td class="col-8 col-md-7 col-lg-8 col-xl-9 py-1 py-xl-2">
                                            <input type="date" value="{{ $reservation->date->format('Y-m-d') }}" id="date" name="date"
                                                class="border-0 show-text fw-normal bg-primary text-white dateInput" min="{{ \Carbon\Carbon::today()->toDateString() }}">
                                        </td>
                                        @error('date')
                                            <p class="text-danger text-outline">{{ $message }}</p>
                                        @enderror
                                    </tr>
                                    <tr>
                                        <th class="col-4 col-md-5 col-lg-4 col-xl-3 py-1 py-xl-2 show-text fw-normal">Time</th>
                                        <td class="col-8 col-md-7 col-lg-8 col-xl-9 py-1 py-xl-2">
                                            <div class="custom-dropdown bg-primary">
                                                <div class="custom-btn d-flex align-items-center justify-content-between timeDropdownBtn"
                                                    data-reservation-id="{{ $reservation->id }}">
                                                    <p class="show-text fw-normal m-0 select__placeholder" id="time">{{ $reservation->time->format('H:i') }}</p>
                                                    <svg class="arrow" viewBox="0 0 12 16">
                                                        <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                                                    </svg>
                                                </div>
                                                <ul class="custom-menu-show timeList">
                                                    @foreach ($times as $time)
                                                    <li data-value="{{ $time }}">{{ $time->format('H:i') }}</li>
                                                    @endforeach
                                                </ul>
                                                <input type="hidden" name="time" class="timeInput">
                                            </div>
                                        </td>
                                        @error('time')
                                            <p class="text-danger text-outline">{{ $message }}</p>
                                        @enderror
                                    </tr>
                                    <tr>
                                        <th class="col-4 col-md-5 col-lg-4 col-xl-3 py-1 py-xl-2 show-text fw-normal">Number</th>
                                        <td class="col-8 col-md-7 col-lg-8 col-xl-9 py-1 py-xl-2">
                                            <div class="custom-dropdown bg-primary">
                                                <div class="custom-btn d-flex align-items-center justify-content-between numberDropdownBtn">
                                                    <p class="show-text fw-normal m-0 select__placeholder" id="number">{{ $reservation->number }}人</p>
                                                    <svg class="arrow" viewBox="0 0 12 16">
                                                        <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                                                    </svg>
                                                </div>
                                                <ul class="custom-menu-show numberList">
                                                    @foreach (range(1, 10) as $number)
                                                    <li class="px-2 text-black" data-value="{{ $number }}">{{ $number }}人</li>
                                                    @endforeach
                                                </ul>
                                                <input type="hidden" name="number" class="numberInput">
                                            </div>
                                        </td>
                                        @error('number')
                                            <p class="text-danger text-outline">{{ $message }}</p>
                                        @enderror
                                    </tr>
                                </table>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('reservation-qr', ['reservation_id' => $reservation->id]) }}" class="btn btn-success btn-sm mt-3 px-3">QRコード</a>
                                    <button type="submit" class="btn btn-warning btn-sm mt-3 px-3">変更する</button>
                                </div>
                            </form>
                            <form action="{{ route('mypage-delete', ['reservation_id' => $reservation->id]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger btn-sm mt-3 px-3">キャンセルする</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="col-md-7">
            <h3 class="fs-5 fw-bold mb-3 mt-md-0 mt-5">お気に入り店舗</h3>
            <div class="row row-cols-2 g-3 g-md-4 g-lg-5">
                @if ($user->likes->isNotEmpty())
                    @foreach ($user->likes as $shop)
                        <div class="col">
                            <x-card :shop="$shop" :userLikes="$userLikes" />
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div>
        <h3 class="fs-5 fw-bold mb-3 mt-5">来店履歴</h3>
        <div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-3">
            @if ($reservations?->isNotEmpty())
                @foreach ($reservations->where('visited', true) as $reservation)
                    <div class="col">
                        <div class="card" id="visited-box-{{ $reservation->id }}">
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
                                <div class="text-center mt-3">
                                    @if ($reservation->review?->reservation_id === $reservation->id)
                                        <p class="btn btn-secondary btn-sm">レビュー済み</p>
                                    @else
                                        <a href="{{ route('review', ['reservation_id' => $reservation->id]) }}" class="btn btn-primary btn-sm">レビューする</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
        const btn = dropdown.querySelector('.custom-btn');
        const menu = dropdown.querySelector('.custom-menu-show');
        const input = dropdown.querySelector('input[type="hidden"]');

        btn.addEventListener('click', () => {
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });
        menu.querySelectorAll('li').forEach(item => {
            item.addEventListener('click', () => {
                btn.querySelector('.select__placeholder').textContent = item.textContent;
                btn.querySelector('.select__placeholder').style.fontSize = '';
                btn.querySelector('.select__placeholder').style.color = '';
                input.value = item.dataset.value;
                menu.style.display = 'none';
            });
        });
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.numberDropdown').forEach(dropdown => {
        const numberDropdownBtn = dropdown.querySelector('.numberDropdownBtn');
        const numberInput = dropdown.querySelector('.numberInput');
        const numberList = dropdown.querySelector('.numberList');

        numberDropdownBtn.nextElementSibling.querySelectorAll('li').forEach(li => {
            li.addEventListener('click', function() {
                const val = this.dataset.value;
                numberInput.value = val;
                numberText.textContent = val + '人';
            });
        });
    });

    document.querySelectorAll('.timeDropdownBtn').forEach(dropdown => {
        const dropdownBtnText = dropdown.querySelector('.show-text');
        const reservationId = dropdown.dataset.reservationId;
        const tr = dropdown.closest('tr');
        const dateInput = tr.previousElementSibling.querySelector('.dateInput');
        const timeList = dropdown.parentElement.querySelector('.timeList');
        const timeInput = dropdown.parentElement.querySelector('.timeInput');

        const updateTimeList = (selectedDate) => {
            if (!selectedDate) {
                timeList.innerHTML = '';
                timeInput.value = '';
                return;
            }

            fetch(`/mypage/update/${reservationId}/times?date=${selectedDate}`)
                .then(res => res.json())
                .then(times => {
                    timeList.innerHTML = '';
                    timeInput.value = '';

                    times.forEach(t => {
                        const li = document.createElement('li');
                        li.textContent = t;
                        li.dataset.value = t;
                        li.classList.add('px-2');
                        li.style.cursor = 'pointer';
                        li.style.color = '#000';
                        li.addEventListener('click', () => {
                            timeInput.value = t;
                            dropdownBtnText.textContent = t;
                            timeList.style.display = 'none';
                        });
                        timeList.appendChild(li);
                    });
                })
                .catch(err => {
                    console.error(err);
                    timeList.innerHTML = '<li>時間を取得できません</li>';
                });
        };

        if (dateInput.value) {
            updateTimeList(dateInput.value);
        }

        dateInput.addEventListener('change', () => {
            updateTimeList(dateInput.value);
        });
    });
</script>

@endsection
