<!-- Header-->
<header id="header" class="header">


    <div class="top-right">



        <div class="header-menu">
            <div class="navbar-brand" style="text-align:center">
                <img src="images/home_dsh.jpeg" alt="Logo" style="width:48px;height:32px;">

            </div> <label style="margin-top: 0.4%; margin-right: 40%; font-weight: 900; font-size: 24px;">Research Grants
                Portal</label>
            @guest
                <a class="nav-link " href="{{ route('login') }}">Login</a>

                <a class="nav-link" href="{{ route('register') }}">Register</a>

            @endguest

            @auth
                <a class="nav-link" href="{{ route('pages.dashboard') }}">Dashboard</a>

            @endauth
        </div>
    </div>
</header>
