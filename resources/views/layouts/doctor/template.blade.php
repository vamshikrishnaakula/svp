@section('styles')
    {{-- <link rel="stylesheet" href="{{ asset('css/faculty-dashboard.css') }}" type="text/css" /> --}}
@endsection

@include('layouts.header')

<div id="wrapper">
    @include('layouts.doctor.aside')
    @include('layouts.topbar')
</div>
<div id="subwrapper">
    {{-- Content --}}
    @yield('content')

    @include('layouts.common-modals')
</div>

@section('scripts')
    {{-- <script src="{{ asset('js/faculty-dashboard.js') }}" type="text/javascript"></script> --}}
@endsection

@include('layouts.footer')
