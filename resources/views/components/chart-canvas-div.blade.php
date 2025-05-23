<div {{ $attributes->merge(["class" => "overflow-auto"]) }}>
    <div class="overflow-auto">
        <div class="chart-container" style="position: relative; height:80vh; width:70vw">
            {{ $slot }}
        </div>
    </div>
</div>
