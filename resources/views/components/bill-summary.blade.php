@props(['number' => ''])
<div {{ $attributes->merge(["class" => "mb-2"]) }}>
    <div class="mb-2 form-control">
        <x-form-label>Patient's Bill Details</x-form-label>
        <X-form-div class="my-4">
            <table id="billingTable{{ $number }}" class="table align-middle">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </X-form-div>
    </div>
</div>