<div {{ $attributes->merge(["class" => "overflow-auto"]) }}>
    <div class="chart-container" style="position: relative; height:70vh; width:65vw">
        {{ $slot }}
    </div>
</div>
