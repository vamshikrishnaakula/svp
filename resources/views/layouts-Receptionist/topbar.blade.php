{{-- Top bar --}}

<section id="topbar">
    <nav class="navbar fixed-top navbar-light bg-light  row no-gutters">
        <div class="col-md-7">
            <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ asset('images/svpnpa.png') }}" alt="dashboard" /></i>
                <span>Sardar Vallabhbhai Patel <br> National Police Academy</span>
            </a>
        </div>

                <div class="col-md-5 username">
                    <div class="row receptionhead no-gutters">
                        <div class="col-md-8">
                            <h5 class="mb-0 text-center">Reception</h5>

                        </div>

                        <div class="col-md-4 text-center">
        <div class="dropdown">
            <a href="#" class="" data-toggle="dropdown"><img src="{{ asset('images/adminuser.png') }}" /><span>{{ Auth::user()->name }}</span></a>
<div class="dropdown-menu">
<a class="dropdown-item" href="#"><form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-jet-dropdown-link  href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                        {{ __('Logout') }}
                    </x-jet-dropdown-link>
                </form></a>
</div>
</div>
                        </div>
                </div>
             {{-- <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-jet-dropdown-link href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                        {{ __('Logout') }}
                    </x-jet-dropdown-link>
                </form> --}}
      </nav>
</section>
