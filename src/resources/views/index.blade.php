@extends('layouts.app')

@section('content')
<div class="container mb-5">
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-2">
        @foreach ($shops as $shop)
        <x-card :shop="$shop" :userLikes="$userLikes" />
        @endforeach
    </div>
</div>
@endsection
