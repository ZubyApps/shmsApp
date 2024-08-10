@extends('layout')


@section('content')
@vite(['resources/js/users.js'])

@include('auth.newstaffModal', ['title' => 'Register Staff', 'isUpdate' => false, 'id' => 'newStaffModal'])
@include('auth.newstaffModal', ['title' => 'Edit Staff', 'isUpdate' => true, 'id' => 'editStaffModal'])
@include('auth.designationModal', ['title' => 'Assign Designation', 'id' => 'designationModal'])

<div class="container mt-5 bg-white">
    <div class="container p-1 mt-5 bg-white">
        <div class="offcanvas offcanvas-start overflow-auto" data-bs-scroll="true" tabindex="-1" id="activeListOffcanvas2"
        aria-labelledby="activeListOffcanvasLabel" aria-expanded="false">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary" id="activeListOffcanvasLabel">List of currently logged in staff</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="py-4 ">
                <table id="activeStaffTable" class="table table-sm">
                    <thead>
                        <tr>
                            <th>Logged in</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="text-start mb-4">
        <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="activeUsersBtn" data-bs-target="#activeListOffcanvas2" aria-controls="activeListOffcanvas2">
            <i class="bi bi-list-check"></i>
            Active Staff
        </button>
    </div>

    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-allStaff-tab" data-bs-toggle="tab" data-bs-target="#nav-allStaff" 
                    type="button" role="tab" aria-controls="nav-allStaff" aria-selected="true">All Staff</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- all staff table -->
            <div class="tab-pane fade show active" id="nav-allStaff" role="tabpanel" aria-labelledby="nav-allStaff-tab" tabindex="0">
                <div class="text-start py-3">
                    <button type="button" id="newStaffBtn" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Staff
                    </button>
                </div>
                <div class="py-4">
                    <table id="allStaffTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Employed</th>
                                <th>Designation</th>
                                <th>Last Login</th>
                                <th>Last Logout</th>
                                <th>Qualification</th>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection