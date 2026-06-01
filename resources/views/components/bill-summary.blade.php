@props(['number' => ''])
<div {{ $attributes->merge(["class" => "mb-2"]) }}>
    <button class="btn btn-primary my-2 viewBillSummaryBtn">View Bill Summary</button>
    <div class="mb-2 form-control d-none billSummaryDiv">
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
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </X-form-div>
    </div>
</div>