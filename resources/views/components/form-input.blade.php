@props(['isUpdate' => false])

<input {{  $isUpdate ? 'readonly'  : ''  }} {{ $attributes->merge(["class" => "form-control"]) }} autocomplete />