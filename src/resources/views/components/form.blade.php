@props([
    'class' => '',
    'title' => '',
    'route' => '',
    'params' => [],
    'buttonText' => '',
])
<div class="rounded-1 shadow-right-bottom bg-white mt-md-2 mt-lg-5 {{ $class }}">
    <p class="bg-primary text-white p-3 fs-5 rounded-top-1 mb-2">{{ $title }}</p>
    <form action="{{ route($route, $params) }}" method="POST" class="d-flex flex-column align-items-center">
        @csrf
        {{ $slot }}
        <div class="text-end px-2 py-4 col-11">
            <button type="submit" class="btn btn-primary rounded-1 px-3 btn-sm">{{ $buttonText }}</button>
        </div>
    </form>
</div>