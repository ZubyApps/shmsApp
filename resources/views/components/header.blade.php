@vite(['resources/css/header.scss', 'resources/js/app.js'])
<div class="container bg-white">
    <header class="border-bottom lh-1 py-3">
        <div class="row flex-nowrap justify-content-between align-items-center">
            <div class="col-6 text-start">
                <a href="/" class="blog-header-logo text-decoration-none fs-1 text-black"><i
                        class="bi bi-hospital text-primary"></i><span class="fw-bold pl-4"> S</span>andra <span
                        class="fw-bold pl-4">H</span>ospital</a>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center">
                <div class="btn-group mx-2">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                    </button>
                    <ul class="dropdown-menu p-0">
                        <div>
                            <table class="table table-striped table-hover table-responsive p-0">
                                <thead>
                                    <tr>
                                        <th>Notifications</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </ul>
                </div>
                <div class="dropdown user-dropdown-menu pb-2">
                    <a href="#"
                        class="text-decoration-none d-flex align-items-center mt-2 btn btn-outline-primary"
                        id="userDropDownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-fill"></i>
                        <span>{{ Auth::user()?->name }}</span>
                    </a>
                    <ul class="dropdown-menu p-0" aria-labelledby="userDropDownMenu" style="width: 1.5rem">
                        <li>
                            <a class="btn btn-outline-none w-100 text-start text-primary"
                                href="{{ route('profile.edit') }}">
                                {{ __('Profile') }}
                            </a>
                        </li>
                        <li class="px-0">
                            <div x-data>
                                <form class="border-none" method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <a class="btn btn-outline-none w-100 text-start text-primary"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </a>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
    </header>

    <div class="nav-scroller py-1 mb-3 border-bottom">
        <nav class="nav nav-underline justify-content-between fs-5 fw-semibold">
            <a href="/patients"
                class="nav-item nav-link link-body-emphasis {{ request()->routeIs('patients') ? 'active text-primary' : '' }}">Patients</a>
            <a href="/doctors"
                class="nav-item nav-link link-body-emphasis {{ request()->routeIs('doctors') ? 'active text-primary' : '' }}">Doctors</a>
            <a href="/nurses" 
                class="nav-item nav-link link-body-emphasis {{ request()->routeIs('nurses') ? 'active text-primary' : '' }}">Nurses</a>
            <a class="nav-item nav-link link-body-emphasis" href="#">Laboratory</a>
            <a class="nav-item nav-link link-body-emphasis" href="#">Pharmacy</a>
            <a class="nav-item nav-link link-body-emphasis" href="#">HMO Desk</a>
            <a class="nav-item nav-link link-body-emphasis" href="#">Billing</a>
            {{-- <a class="nav-item nav-link link-body-emphasis" href="#">Resources</a>
            <a class="nav-item nav-link link-body-emphasis" href="#">Admin</a>
            <a class="nav-item nav-link link-body-emphasis" href="#">Reports</a>
            <a class="nav-item nav-link link-body-emphasis" href="#">More</a> --}}
        </nav>
    </div>
</div>
