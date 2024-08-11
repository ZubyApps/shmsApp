@extends('layout')

@section('content')
{{-- @vite(['resources/js/admin.js']) --}}

    <div class="container mt-5">
        <div class="container px-4 py-5" id="hanging-icons">
            <h2 class="pb-2 border-bottom text-primary">Administration</h2>
            <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
              <div class="col d-flex align-items-start">
                <div class="icon-square text-body-emphasis d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                  <i class="text-primary bi bi-people-fill"></i>
                </div>
                <div>
                  <h3 class="fs-2 text-body-emphasis">Staff/Users</h3>
                  <p>Create new staff/users. Manage Staff/Users permissions and roles. Access and Evaluate staff/users report </p>
                  <a href="/users" class="btn btn-primary">
                    Staff/Users
                    <i class="bi bi-people-fill"></i>
                  </a>
                </div>
              </div>
              <div class="col d-flex align-items-start">
                <div class="icon-square text-body-emphasis d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                  <i class="text-primary bi bi-gear"></i>
                </div>
                <div>
                  <h3 class="fs-2 text-body-emphasis">Admin Settings</h3>
                  <p>Create and Manage Administrative settings. Like Sponsor settings, Billing Settings and other relvant settings</p>
                  <a href="admin/settings" class="btn btn-primary">
                    Settings
                    <i class="bi bi-gear"></i>
                  </a>
                </div>
              </div>
              <div class="col d-flex align-items-start">
                <div class="icon-square text-body-emphasis d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                  <i class="text-primary bi bi-clipboard2-pulse"></i>
                </div>
                <div>
                  <h3 class="fs-2 text-body-emphasis">Reports</h3>
                  <p>Generate patient level reports, medical services reports, investigation reports, medication/drug reports etc.</p>
                  <a href="reports" class="btn btn-primary">
                    Reports
                    <i class="bi bi-clipboard2-pulse"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
    </div>

@endsection