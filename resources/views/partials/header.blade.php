<nav class="navbar  navbar-expand-lg navbar-light "
    style="background-color:#88CDFF; padding-top:0px; padding-bottom:0px;">
    <div class="container-fluid">
        @auth
            <button class="btn btn-primary d-md-none me-2" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
        @endauth
        <a class="navbar-brand d-flex align-items-center" href="{{ url(config('app.url')) }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <span class="d-none d-lg-inline text-light">University of Kabianga</span>
        </a>
        <div class="mx-auto text-center">
            <h5 class="navbar-text text-light" style="padding:0px;margin:0px;">Uok Call for Annual Grants</h5>
        </div>
        <span style="width:80px">
            @auth
                <a class="btn btn-sm btn-outline-light ms-auto d-flex align-items-center"
                    href="{{ route('route.logout') }}">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            @endauth
        </span>
    </div>
</nav>
