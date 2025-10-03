@props(['shop', 'userLikes'])
<div class="col">
    <div class="card shadow-right-bottom border-0 col">
        <img src="{{ $shop->image_url }}" class="card-img">
        <div class="card-body p-xl-4">
            <h5 class="fw-bold mb-1 fs-1_39vw">{{ $shop->name }}</h5>
            <p class="mb-0 fw-medium fs-0_98vw">#{{ $shop->area->name }}
                <span>#{{ $shop->genre->name }}</span>
            </p>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <a href="{{ route('detail', ['shop_id' => $shop->id]) }}" class="btn btn-primary px-2 px-xl-3 fs-1vw rounded-1 btn-sm">詳しく見る</a>
                <form action="{{ route('like', ['shop_id' => $shop->id]) }}" method="post">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="like__icon">
                        <img class="heart-icon" src="{{ in_array($shop->id, $userLikes) ? asset('icon/heart-red.svg') : asset('icon/heart.svg') }}">
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>