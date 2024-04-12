<x-guest-layout>
    <div class="b-example-divider"></div>

    <div class="container col-xl-10 col-xxl-8 px-4 py-3">
        <div class="row align-items-center g-lg-5 py-5">
            <div class="col-lg-7 text-center text-lg-start">
                <h1 class="display-4 fw-bold lh-1 mb-3 text-primary blog-header-logo fs-1"><span><i
                            class="bi bi-hospital"></i></span> SHMS Login</h1>
                <p class="col-lg-10 fs-4">All the hard work you put into bettering the lives of others is surely seen
                    by the Managment, your Colleagues, the Patients and God Himself. Keep up the good work! </p>
            </div>
            <div class="col-md-10 mx-auto col-lg-5">
                <form class="p-4 p-md-5 border rounded-3 bg-body-tertiary" method="POST" action="{{ route('login') }}">

                    @csrf

                    <div class="form-floating mb-3">
                        <input type="text" name="phone_number" class="form-control" id="floatingInput"
                            autocomplete="phone" autofocus required>
                        <label for="floatingInput">Phone</label>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" id="floatingPassword" autocomplete="password" autofocus required>    
                        <label for="floatingPassword">Password</label>
                        <i class="bi bi-eye-fill text-primary float-end" id="showPassword" onclick="(function(){this.floatingPassword.type == 'password' ? this.floatingPassword.type = 'text' : this.floatingPassword.type = 'password'})()"></i>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    <div class="checkbox mb-3">
                        <label>
                            <input type="checkbox" value="remember-me"> Remember me
                        </label>
                    </div>
                    <button class="w-100 btn btn-lg btn-primary text-white" type="submit">Sign in</button>
                    <hr class="my-4">
                    <small class="text-body-secondary">By signing in, you are considered to be on duty.</small>
                </form>
            </div>
        </div>
    </div>

    <div class="b-example-divider"></div>
</x-guest-layout>
