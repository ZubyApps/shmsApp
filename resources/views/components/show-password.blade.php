@props(['element' => 'password'])

<i class="bi bi-eye-fill text-primary float-end" id="showPassword" onclick="(function(){console.log(this.{{ $element }}.type)
this.{{ $element }}.type == 'password' ? this.{{ $element }}.type = 'text' : this.{{ $element }}.type = 'password'})()"></i>