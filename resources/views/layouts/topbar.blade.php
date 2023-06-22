{{-- Top bar --}}

<section id="topbar">
    <nav class="navbar fixed-top navbar-light bg-light  row no-gutters">
        <div class="col-md-7 ">
            <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('images/svpnpa.png') }}" alt="dashboard" /></i>
                <span>Sardar Vallabhbhai Patel <br> National Police Academy</span>
            </a>
        </div>


        <?php
        $role   = Auth::user()->role;
        $dashboard_heading  = "Super Admin";
        if($role === 'faculty') {
            $dashboard_heading  = "Faculty";
        } elseif($role === 'probationer') {
            $dashboard_heading  = "Probationer";
        } elseif($role === 'doctor') {
            $dashboard_heading  = "Doctor";
        } elseif($role === 'superadmin') {
            $dashboard_heading  = "Admin";
        }
        ?>
        <div class="col-md-5  username">
            <div class="row receptionhead no-gutters">
                <div class="col-5 col-md-5">
                    <h5 class="mb-0 text-right">{{ $dashboard_heading }}</h5>

                </div>
                <div class="col-2 col-md-2 text-right">
                    <div class="topbar-notification-links">
                        <a href="{{ url('/notifications') }}">
                            <span class="notification-icon"><i style="color: #fff; font-size: 18px;" class="far fa-bell"></i></span>
                            <span class="notification-count"></span>
                        </a>
                    </div>
                </div>

                <?php
                        $user_role = Auth::user()->role;
                        $id = Auth::user()->id;
                        //echo $id;exit;
                ?>
                <div class="col-5 col-md-5 text-center">
                    <div class="dropdown">
                        <a href="#" class="" data-toggle="dropdown"><img src="{{ asset('images/adminuser2.png') }}" /><span>{{ Auth::user()->name }}</span></a>
                        <div class="dropdown-menu">
                            @if($user_role === 'probationer')

                            <a class="dropdown-item" href="/updateprobationerprofile/{{$id}}">Update Profile</a>
                            @endif

                            <a class="dropdown-item" href="#">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-jet-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                        {{ __('Logout') }}
                                    </x-jet-dropdown-link>
                                </form>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <form method="POST" action="{{ route('logout') }}">
            @csrf

            <x-jet-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                {{ __('Logout') }}
            </x-jet-dropdown-link>
            </form> --}}
        </div>
    </nav>
</section>
