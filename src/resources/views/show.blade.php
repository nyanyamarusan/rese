@extends('layouts.app')

@section('content')
<div class="container mb-5">
    <div class="d-flex flex-md-row flex-column gap-5p">
        <div class="col">
            <div class="d-flex align-items-center gap-3 mt-3 p-0">
                <div class="back-btn shadow-right-bottom rounded-1 d-flex align-items-center justify-content-center">
                    <a href="{{ route('index') }}" class="text-black text-decoration-none fw-bold fs-6">＜</a>
                </div>
                <h2 class="m-0 fw-bold fs-5">{{ $shop->name }}</h2>
            </div>
            <img src="{{ asset('storage/shop-img/' . $shop->image) }}" class="w-100 my-sm-4 my-3">
            <p class="mb-0 show-text">#{{ $shop->area->name }}
                <span>#{{ $shop->genre->name }}</span>
            </p>
            <p class="show-text my-sm-4 my-3">{{ $shop->detail }}</p>
        </div>

        <div class="col position-relative mt-5 mt-md-0">
            <div class="card bg-primary text-white position-absolute w-100 card-position">
            <form action="{{ route('reservation') }}" method="post">
                @csrf
                <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                <input type="hidden" name="user_id" value="{{ Auth::check() ? Auth::user()->id : '' }}">
                <div class="card-body p-lg-4">
                    <h2 class="card-title fs-5 fw-bold mb-3 mb-lg-4">予約</h2>
                    <div class="mb-3">
                        <div class="col-6">
                            <input type="date" class="form-control show-text py-1 px-2 px-xl-3 rounded-1" id="date" name="date"
                                value="{{ old('date') }}" min="{{ \Carbon\Carbon::today()->toDateString() }}">
                        </div>
                        @error('date')
                            <p class="text-danger text-outline">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="custom-dropdown p-2 rounded-1 text-black py-1 px-2 px-xl-3">
                            <div class="custom-btn d-flex align-items-center justify-content-between" id="timeDropdownBtn">
                                <p class="show-text m-0 select__placeholder" id="time">時間</p>
                                <svg class="arrow" viewBox="0 0 12 16">
                                    <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                                </svg>
                            </div>
                            <ul class="custom-menu-show" id="timeList">
                                @forelse ($times as $time)
                                <li data-value="{{ $time }}">{{ $time }}</li>
                                @empty
                                <li class="disabled px-2">先に日付を選択してください</li>
                                @endforelse
                            </ul>
                            <input type="hidden" name="time" id="timeInput">
                        </div>
                        @error('time')
                        <p class="text-danger text-outline">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="custom-dropdown p-2 rounded-1 text-black py-1 px-2 px-xl-3">
                            <div class="custom-btn d-flex align-items-center justify-content-between" id="numberDropdownBtn">
                                <p class="show-text m-0 select__placeholder" id="number">人数</p>
                                <svg class="arrow" viewBox="0 0 12 16">
                                    <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                                </svg>
                            </div>
                            <ul class="custom-menu-show">
                                @foreach (range(1, 10) as $number)
                                <li class="px-2" data-value="{{ $number }}">{{ $number }}人</li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="number" id="numberInput">
                        </div>
                        @error('number')
                        <p class="text-danger text-outline">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="p-2 p-xl-3 rounded-1 table-bg card-body-mb">
                        <table class="w-100">
                            <tr>
                                <th class="col-4 col-lg-3 py-1 show-text">Shop</th>
                                <td class="col-8 col-lg-9 py-1 show-text">{{ $shop->name }}</td>
                            </tr>
                            <tr>
                                <th class="col-4 col-lg-3 py-1 show-text">Date</th>
                                <td class="col-8 col-lg-9 py-1 show-text" id="dateText"></td>
                            </tr>
                            <tr>
                                <th class="col-4 col-lg-3 py-1 show-text">Time</th>
                                <td class="col-8 col-lg-9 py-1 show-text" id="timeText"></td>
                            </tr>
                            <tr>
                                <th class="col-4 col-lg-3 py-1 show-text">Number</th>
                                <td class="col-8 col-lg-9 py-1 show-text" id="numberText"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center bg-blue p-3">
                    <button type="submit" class="border-0 text-white bg-blue reserve-text w-100">予約する</button>
                </div>
            </form>
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

    document.getElementById('date').addEventListener('input', function() {
        document.getElementById('dateText').textContent = this.value;
    });

    const numberDropdown = document.getElementById('numberDropdownBtn');
    const numberText = document.getElementById('numberText');
    const numberInput = document.getElementById('numberInput');

    numberDropdown.nextElementSibling.querySelectorAll('li').forEach(li => {
        li.addEventListener('click', function() {
            const val = this.dataset.value;
            numberInput.value = val;
            numberText.textContent = val + '人';
        });
    });

    const dateInput = document.getElementById('date');
    const timeList = document.getElementById('timeList');
    const timeInput = document.getElementById('timeInput');
    const dropdownBtnText = document.querySelector('#timeDropdownBtn .show-text');

    dateInput.addEventListener('change', function() {
        const selectedDate = this.value;

        if (!selectedDate) {
            timeList.innerHTML = '';
            dropdownBtnText.textContent = '時間';
            timeInput.value = '';
            return;
        }

        fetch(`/detail/{{ $shop->id }}/times?date=${selectedDate}`)
            .then(res => res.json())
            .then(times => {
                timeList.innerHTML = '';
                dropdownBtnText.textContent = '時間';
                timeInput.value = '';

                times.forEach(t => {
                    const li = document.createElement('li');
                    li.textContent = t;
                    li.dataset.value = t;
                    li.classList.add('px-2');
                    li.style.cursor = 'pointer';
                    li.addEventListener('click', () => {
                        timeInput.value = t;
                        dropdownBtnText.textContent = t;
                        document.getElementById('timeText').textContent = t;
                        timeList.style.display = 'none';
                    });
                    timeList.appendChild(li);
                });
            })
            .catch(err => {
                console.error(err);
                timeList.innerHTML = '<li>時間を取得できません</li>';
            });
    });
</script>
@endsection
