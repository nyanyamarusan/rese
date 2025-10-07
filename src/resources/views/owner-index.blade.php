@extends('layouts.app')

@section('content')
<div class="container px-4 mb-5">
    @if (session('message'))
    <div class="alert alert-success text-success" role="alert">
        {{ session('message') }}
    </div>
    @endif
    <div class="d-flex justify-content-between flex-column flex-md-row">
        <div class="col-md-4">
            <h3 class="fs-5 fw-bold mb-3">店舗選択</h3>
            <div class="card shadow-right-bottom bg-info">
                <div class="card-body">
                    <div class="custom-dropdown px-2 py-1 rounded-1">
                        <div class="custom-btn d-flex align-items-center justify-content-between">
                            <p class="show-text fw-normal m-0 select__placeholder">店舗を選択してください</p>
                            <input type="hidden" name="shop_id" id="shop_id">
                            <svg class="arrow" viewBox="0 0 12 16">
                                <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                            </svg>
                        </div>
                        <ul class="custom-menu-show">
                            @forelse ($shops as $shop)
                            <li data-value="{{ $shop->id }}" class="px-2">{{ $shop->name }}</li>
                            @empty
                            <li class="px-2">店舗がありません</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="text-end mt-2">
                        @if (isset($shop))
                            <a href="{{ route('owner-show', ['shop_id' => $shop->id]) }}" class="btn btn-primary rounded-1 px-2 px-xl-3 btn-sm">選択</a>
                        @else
                            <span class="btn btn-secondary rounded-1 px-2 px-xl-3 btn-sm disabled">店舗がまだ登録されていません</span>
                        @endif
                    </div>
                    <p class="form-text mt-4 mb-0">店舗選択後に、店舗情報の更新、予約情報の確認が可能です</p>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <h3 class="fs-5 fw-bold mb-3 mt-md-0 mt-5">新規店舗情報作成</h3>
            <x-form title="New Shop" enctype="multipart/form-data" route="owner-store" buttonText="作成">
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="name" class="font-bold col-3">Name</label>
                    <input type="text" name="name" id="name" placeholder="Shop Name" class="border-0 border-bottom border-black col-9">
                </div>
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="area" class="col-3">Area</label>
                    <div class="custom-dropdown px-2 py-1 rounded-1 border col-6 col-md-4">
                        <div class="custom-btn d-flex align-items-center justify-content-between">
                            <p class="show-text fw-normal m-0 select__placeholder">
                                Area
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
                            <p class="show-text fw-normal m-0 select__placeholder">
                                Genre
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
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="open_time" class="col-3">Open Time</label>
                    <div class="custom-dropdown px-2 py-1 rounded-1 border col-6 col-md-4">
                        <div class="custom-btn d-flex align-items-center justify-content-between">
                            <p class="show-text fw-normal m-0 select__placeholder">
                                Open Time
                            </p>
                            <svg class="arrow" viewBox="0 0 12 16">
                                <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                            </svg>
                        </div>
                        <ul class="custom-menu-show">
                            @foreach ($times as $time)
                                <li data-value="{{ $time }}" class="px-2">{{ $time }}</li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="open_time" id="open_time">
                    </div>
                </div>
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="close_time" class="col-3">Close Time</label>
                    <div class="custom-dropdown px-2 py-1 rounded-1 border col-6 col-md-4">
                        <div class="custom-btn d-flex align-items-center justify-content-between">
                            <p class="show-text fw-normal m-0 select__placeholder">
                                Close Time
                            </p>
                            <svg class="arrow" viewBox="0 0 12 16">
                                <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                            </svg>
                        </div>
                        <ul class="custom-menu-show">
                            @foreach ($times as $time)
                                <li data-value="{{ $time }}" class="px-2">{{ $time }}</li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="close_time" id="close_time">
                    </div>
                </div>
                <div class="d-flex col-10 mt-3 flex-column gap-2">
                    <label for="image" class="col-3">Image</label>
                    <img src="" class="w-100 my-sm-4 my-3 d-none" id="imagePreview">
                    <input type="file" name="image" id="image" class="show-text fw-normal">
                </div>
                <div class="d-flex col-10 mt-3 flex-column gap-2">
                    <label for="detail" class="col-3">Detail</label>
                    <textarea name="detail" id="detail" cols="30" rows="8" class="show-text fw-normal py-1 px-2" placeholder="Detail"></textarea>
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
                window.getSelection().removeAllRanges();
                document.activeElement.blur();
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
                    imagePreview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    }
</script>
@endsection