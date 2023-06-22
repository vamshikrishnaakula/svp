<x-guest-layout>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/style.css" type="text/css" />
    <link rel="stylesheet" href="./css/bootstrap.min.css" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <x-jet-validation-errors class="mb-4" />

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @elseif ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @php
        $inst_logo = env('INSTITUTE_LOGO', 'images/svpnpalogo.png');
        $samssuite_logo = env('INSTITUTE_LOGO', 'images/sams-logo-min.png');
        $inst_name = env('INSTITUTE_NAME', 'Sardar Vallabhbhai Patel<br />National Police Academy');
        $outdoor_name = env('INSTITUTE_NAME', 'SPORTS AND ACTIVITY MANAGEMENT SOFTWARE');
    @endphp

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="loginpage">
            <div class="row h-100 no-gutters">
                <div class="col-md-8  db-bg">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="logo">
                                <img src="{{ asset($inst_logo) }}" />
                            </div>
                        </div>
                        <div class="col-md-8 mt-8">
                            <h3 class="mt-5 text-center font-weight-bold">{!! $outdoor_name !!}</h3>
                        </div>

                        <div class="col-md-2">
                            <div class="samslogo">
                                <img src="{{ asset($samssuite_logo) }}" />
                            </div>
                        </div>
                    </div>
                    <div class="row">

                    </div>
                    <div class="row outdoor_name">
                        <div class="col-md-12 text-center">
                            <div class="waviy">
                                <span style="--i:1">O</span>
                                <span style="--i:2">U</span>
                                <span style="--i:3">T</span>
                                <span style="--i:4">D</span>
                                <span style="--i:5">O</span>
                                <span style="--i:6">O</span>
                                <span style="--i:7">R</span>
                                <span> </span>
                                <span style="--i:8">T</span>
                                <span style="--i:9">R</span>
                                <span style="--i:10">A</span>
                                <span style="--i:11">I</span>
                                <span style="--i:12">N</span>
                                <span style="--i:13">I</span>
                                <span style="--i:14">N</span>
                                <span style="--i:15">G</span>
                                <span> </span>
                                <span style="--i:16">D</span>
                                <span style="--i:17">A</span>
                                <span style="--i:18">S</span>
                                <span style="--i:19">H</span>
                                <span style="--i:20">B</span>
                                <span style="--i:21">O</span>
                                <span style="--i:22">A</span>
                                <span style="--i:23">R</span>
                                <span style="--i:24">D</span>

                            </div>
                        </div>
                    </div>
                    <div class="row mt-40">
                        <div class="col-md-12">
                            <h5 class="mt-5 text-center font-weight-bold text-uppercase">{!! $inst_name !!}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="login">
                        <h3 class="mb-5">SIGN IN</h3>
                        <div class="row">
                            <input type="input" placeholder="username" class="username" id="email"
                                name="email" />
                            <input type="password" placeholder="password" class="password" id="password"
                                name="password" />
                            <button type="submit" class="loginBtn mt-3">Login</button>

                        </div>
                    </div>
                    <div class="row">
                        <div class="footer-cp">
                            <div class="col-md-12">
                                <p class="text-center">&#169; 2022 Timing Technologies India &#x2502; All Rights
                                    Reserved <br /> Version 1.0</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</x-guest-layout>
