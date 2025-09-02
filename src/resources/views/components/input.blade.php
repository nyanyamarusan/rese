@props([
    'type' => 'text',
    'name' => '',
    'label' => '',
    'labelText' => '',
    'placeholder' => '',
    'value' => '',
    'labelClass' => '',
    'inputClass' => '',
])

<div class="d-flex justify-content-center gap-2 align-items-center col-11 mt-3">
    <label for="{{ $name }}" class="fs-5 font-bold {{ $labelClass }}">
        {!! $label !!}
        {{ $labelText }}
    </label>

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
        class="border-0 border-bottom border-black {{ $inputClass }}"
    >

    @error($name)
        <p class="text-danger mt-2">{{ $message }}</p>
    @enderror
</div>