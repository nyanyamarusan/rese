@props([
    'buttonText' => '',
    'route' => '',
    'params' => [],
])
<div class="box-height d-flex justify-content-center align-items-center rounded-1 shadow-right-bottom bg-white mt-2 mt-lg-5 col-7 col-md-4">
    <div>
        {{ $slot }}
        <div class="text-center mt-3 mt-md-4 mt-xl-5">
            <a href="{{ route($route, $params ?? []) }}"
                class="bg-primary text-white px-2 px-xl-3 py-1 btn-sm rounded-1 text-decoration-none fs-0_98vw">
                {{ $buttonText }}
            </a>
        </div>
    </div>
</div>