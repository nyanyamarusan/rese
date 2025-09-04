@extends('layouts.app')

@section('content')
<div class="container px-4 mb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between">
        <h2 class="fs-3 fw-bold mb-4 mt-2 col-md-4 d-none d-md-block"></h2>
        <h2 class="fs-3 fw-bold mb-4 mt-2 col-md-7">{{ $shop->name }}</h2>
    </div>
    <div class="d-flex justify-content-between flex-column flex-md-row">
        <div class="col-md-4">
            <h3 class="fs-5 fw-bold mb-3">店舗選択</h3>
            <div class="card shadow-right-bottom bg-info">
                <div class="card-body">
                    <div class="custom-dropdown px-2 py-1 rounded-1">
                        <div class="custom-btn d-flex align-items-center justify-content-between" id="shopDropdownBtn">
                            <p class="show-text fw-normal m-0 select__placeholder" id="shopInput">店舗を選択してください</p>
                            <svg class="arrow" viewBox="0 0 12 16">
                                <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                            </svg>
                        </div>
                        <ul class="custom-menu-show">
                            @foreach ($shops as $shop)
                            <li data-value="{{ $shop->id }}" class="px-2">{{ $shop->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="text-end mt-2">
                        <a href="{{ route('owner-show', ['shop_id' => $shop->id]) }}" class="btn btn-primary rounded-1 px-2 px-xl-3 btn-sm">選択</a>
                    </div>
                    <p class="form-text mt-4 mb-0">店舗選択後に、店舗情報の更新、予約情報の確認が可能です</p>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <h3 class="fs-5 fw-bold mb-3 mt-md-0 mt-5">新規店舗情報作成</h3>
            <x-form title="New Shop" enctype="enctype='multipart/form-data'" route="owner-store" buttonText="作成する">
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="name" class="font-bold col-3">Name</label>
                    <input type="text" name="name" id="name" placeholder="Shop Name" class="border-0 border-bottom border-black col-9">
                </div>
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="area" class="col-3">Area</label>
                    <div class="custom-dropdown px-2 py-1 rounded-1 border col-6 col-md-4">
                        <div class="custom-btn d-flex align-items-center justify-content-between" id="areaDropdownBtn">
                            <p class="show-text fw-normal m-0 select__placeholder text-secondary" id="area">Area</p>
                            <svg class="arrow" viewBox="0 0 12 16">
                                <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                            </svg>
                        </div>
                        <ul class="custom-menu-show">
                            @foreach ($areas as $area)
                            <li data-value="{{ $area->id }}" class="px-2">{{ $area->name }}</li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="area_id" id="areaInput">
                    </div>
                </div>
                <div class="d-flex align-items-center col-10 mt-3">
                    <label for="genre" class="col-3">Genre</label>
                    <div class="custom-dropdown px-2 py-1 rounded-1 border col-6 col-md-4">
                        <div class="custom-btn d-flex align-items-center justify-content-between" id="genreDropdownBtn">
                            <p class="show-text fw-normal m-0 select__placeholder text-secondary" id="genre">Genre</p>
                            <svg class="arrow" viewBox="0 0 12 16">
                                <path d="M6 10 L12 7 L6 16 L0 8 Z" fill="#d6dfff"/>
                            </svg>
                        </div>
                        <ul class="custom-menu-show">
                            @foreach ($genres as $genre)
                                <li data-value="{{ $genre->id }}" class="px-2">{{ $genre->name }}</li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="genre_id" id="genreInput">
                    </div>
                </div>
                <div class="d-flex col-10 mt-3 flex-column gap-2">
                    <label for="image" class="col-3">Image</label>
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
            });
        });
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    });

    const shopDropdown = document.getElementById('shopDropdownBtn');
    const shopInput = document.getElementById('shopInput');

    shopDropdown.nextElementSibling.querySelectorAll('li').forEach(li => {
        li.addEventListener('click', function() {
            const val = this.dataset.value;
            shopInput.value = val;
        });
    });

    const areaDropdown = document.getElementById('areaDropdownBtn');
    const areaInput = document.getElementById('areaInput');

    areaDropdown.nextElementSibling.querySelectorAll('li').forEach(li => {
        li.addEventListener('click', function() {
            const val = this.dataset.value;
            areaInput.value = val;
        });
    });

    const genreDropdown = document.getElementById('genreDropdownBtn');
    const genreInput = document.getElementById('genreInput');

    genreDropdown.nextElementSibling.querySelectorAll('li').forEach(li => {
        li.addEventListener('click', function() {
            const val = this.dataset.value;
            genreInput.value = val;
        });
    });
</script>
@endsection