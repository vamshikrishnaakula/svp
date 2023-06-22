<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- csrf token for ajax --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <?php
    if(isset($_GET['access_token'])) {
        $access_token   = $_GET['access_token'];
    }
    ?>

    <!-- <title>{{ config('app.name') }}</title> -->

    {{-- jQyery (used by all pages)  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    {{-- jQyery Poper (used by all pages)  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    {{-- Bootstrap JS (used by all pages)  --}}
    <script src="{{ asset('js/bootstrap.min.js')}}"></script>


    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js">
    </script>

    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.js"
        integrity="sha256-2JRzNxMJiS0aHOJjG+liqsEOuBb6++9cY4dSOyiijX4=" crossorigin="anonymous"></script>

    {{-- Bootstrap DataTable --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
    <script src=https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js></script>
    <script src=https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.0/jquery.typeahead.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/gh/alumuko/vanilla-datetimerange-picker@latest/dist/vanilla-datetimerange-picker.css">

    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/alumuko/vanilla-datetimerange-picker@latest/dist/vanilla-datetimerange-picker.js"></script>

    {{-- Bootstrap CSS (used by all pages)  --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" type="text/css" />

    {{-- jQuery UI  --}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" type="text/css" />

    <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/gh/alumuko/vanilla-datetimerange-picker@latest/dist/vanilla-datetimerange-picker.css">

    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/alumuko/vanilla-datetimerange-picker@latest/dist/vanilla-datetimerange-picker.js"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    {{-- Custom CSS (used by all pages)  --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('css/combined.css') }}" type="text/css" />

    {{-- Includable CSS --}}
    @yield('styles')

    <script>
        // "global" vars, built using blade
        var appUrl = "{{ url('/') }}";
        var assetUrl = '{{ URL::asset('/') }}';
    </script>
</head>

<body>
