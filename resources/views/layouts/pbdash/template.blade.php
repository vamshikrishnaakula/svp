@section('styles')
    <link rel="stylesheet" href="{{ asset('css/probationer-dashboard.css') }}" type="text/css" />
@endsection

@include('layouts.header')

<div id="wrapper">

        @include('layouts.pbdash.aside')
        @include('layouts.topbar')
</div>
<div id="subwrapper">
    {{-- Content --}}
    @yield('content')

    @include('layouts.common-modals')
</div>

    @section('scripts')
        <script src="{{ asset('js/probationer-dashboard.js') }}" type="text/javascript"></script>
    @endsection
@include('layouts.footer')
