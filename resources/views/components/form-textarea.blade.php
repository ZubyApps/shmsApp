@props(['readonly' => false])

<textarea {{ $readonly ? 'readonly' : '' }} {{ $attributes->merge(['class' => 'form-control']) }}></textarea>