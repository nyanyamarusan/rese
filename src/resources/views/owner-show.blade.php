@extends('layouts.app')

@section('content')
@php
    $activeTab = request('tab', 'reservation');
@endphp
<div class="container px-4 mb-5">
    @if (session('message'))
    <div class="alert alert-success text-success" role="alert">
        {{ session('message') }}
    </div>
    @endif
    <h2 class="fs-3 fw-bold mb-4 mt-2">{{ $shop->name }}</h2>
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'reservation' ? 'active bg-primary text-white' : 'text-black' }}"
                href="{{ route('owner-show', ['shop_id' => $shop->id, 'tab' => 'reservation']) }}">
                予約一覧
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'edit' ? 'active bg-primary text-white' : 'text-black' }}"
                href="{{ route('owner-show', ['shop_id' => $shop->id, 'tab' => 'edit']) }}">
                店舗情報更新
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade {{ $activeTab === 'reservation' ? 'show active' : '' }}" id="reservation">
            <div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-3 mb-5 mt-3">
                @foreach ($reservations as $index => $reservation)
                <div class="col">
                    <div class="card bg-primary text-white shadow-right-bottom rounded-1 p-lg-2" id="reservation-box-{{ $reservation->id }}">
                        <div class="card-body">
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
                                    <th class="col-4 col-md-5 py-1 py-xl-2 show-text fw-normal">Name</th>
                                    <td class="col-8 col-md-7 py-1 py-xl-2 show-text fw-normal">{{ $reservation->user->name }}</td>
                                </tr>
                                <tr>
                                    <th class="col-4 col-md-5 py-1 py-xl-2 show-text fw-normal">Date</th>
                                    <td class="col-8 col-md-7 py-1 py-xl-2">
                                        {{ $reservation->date->format('Y-m-d') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="col-4 col-md-5 py-1 py-xl-2 show-text fw-normal">Time</th>
                                    <td class="col-8 col-md-7 py-1 py-xl-2">
                                        {{ $reservation->time->format('H:i') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="col-4 col-md-5 py-1 py-xl-2 show-text fw-normal">Number</th>
                                    <td class="col-8 col-md-7 py-1 py-xl-2">
                                        {{ $reservation->number }}人
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @php
                $current = $reservations->currentPage();
                $last = $reservations->lastPage();
            @endphp
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item {{ $current == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $reservations->previousPageUrl() }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    @for ($i = 1; $i <= $last; $i++)
                        <li class="page-item {{ $current == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $reservations->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $current == $last ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $reservations->nextPageUrl() }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="tab-pane fade {{ $activeTab === 'edit' ? 'show active' : '' }} d-flex justify-content-center" id="edit">
            <x-form title="Edit Shop" enctype="enctype='multipart/form-data'" route="owner-update" params="['shop_id' => $shop->id]" buttonText="更新" class="mt-4 col-md-8 col-12">
                @method('PATCH')
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="name" class="font-bold col-3">Name</label>
                    <input type="text" name="name" id="name" placeholder="Shop Name" class="border-0 border-bottom border-black col-9"
                        value="{{ $shop->name }}">
                </div>
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="area" class="col-3">Area</label>
                    <div class="custom-dropdown px-2 py-1 rounded-1 border col-6 col-md-4">
                        <div class="custom-btn d-flex align-items-center justify-content-between">
                            <p class="show-text fw-normal m-0 select__placeholder text-secondary">
                                {{ $shop->area->name }}
                            </p>
                            <svg class="arrow" viewBox="0 0 12 16">
                                <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                            </svg>
                        </div>
                        <ul class="custom-menu-show">
                            @foreach ($areas as $area)
                            <li data-value="{{ $area->id }}" class="px-2">{{ $area->name }}</li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="area_id" id="area_id">
                    </div>
                </div>
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="genre" class="col-3">Genre</label>
                    <div class="custom-dropdown px-2 py-1 rounded-1 border col-6 col-md-4">
                        <div class="custom-btn d-flex align-items-center justify-content-between">
                            <p class="show-text fw-normal m-0 select__placeholder text-secondary">
                                {{ $shop->genre->name }}
                            </p>
                            <svg class="arrow" viewBox="0 0 12 16">
                                <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                            </svg>
                        </div>
                        <ul class="custom-menu-show">
                            @foreach ($genres as $genre)
                                <li data-value="{{ $genre->id }}" class="px-2">{{ $genre->name }}</li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="genre_id" id="genre_id">
                    </div>
                </div>
                <div class="d-flex col-10 mt-3 flex-column gap-2">
                    <label for="image" class="col-3">Image</label>
                    <img src="{{ asset('storage/shop-img/' . $shop->image) }}" class="w-100 my-sm-4 my-3" id="imagePreview">
                    <input type="file" name="image" id="image" class="show-text fw-normal" accept="image/*">
                </div>
                <div class="d-flex col-10 mt-3 flex-column gap-2">
                    <label for="detail" class="col-3">Detail</label>
                    <textarea name="detail" id="detail" cols="30" rows="8"
                        class="show-text fw-normal py-1 px-2" placeholder="Detail">{{ trim($shop->detail) }}</textarea>
                </div>
            </x-form>
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

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
</script>
@endsection