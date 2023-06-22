{{-- Jquery UI  --}}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" type="text/javascript"></script>

{{-- Custom JS (used by all pages)  --}}
<script src="{{ asset('js/common.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/combined.js') }}" type="text/javascript"></script>

{{-- Includable JS --}}
@yield('scripts')

</body>

</html>
