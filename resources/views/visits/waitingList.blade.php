@vite(['resources/js/visits.js'])
<div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasWithBothOptionsLabel">List of Waiting Patients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="py-4 ">
                    <table id="waitingListTable" class="table table-hover align-middle table-sm bg-primary">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Age</th>
                                <th>Sponsor</th>
                                <th>Consult</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>