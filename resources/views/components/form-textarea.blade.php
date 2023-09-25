@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => 'form-control']) }} spellcheck="true" autocorrect="on"></textarea>