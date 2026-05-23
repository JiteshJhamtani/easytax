<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EasyTax') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    @stack('styles')
</head>

<body>
    <x-front.header />

    <main class="main-content">
        @yield('content')
        
    </main>

    <x-front.footer />

    <script src="{{ asset('assets/js/app.js') }}"></script>
    @stack('scripts')

<!-- GLOBAL RESPONSIVE TABLE SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function applyTableLabels() {
            document.querySelectorAll('table.responsive-card-table').forEach(function(table) {
                var headers = [];
                table.querySelectorAll('thead th').forEach(function(th) {
                    headers.push(th.textContent.trim());
                });
                
                function applyLabels() {
                    table.querySelectorAll('tbody tr').forEach(function(tr) {
                        tr.querySelectorAll('td').forEach(function(td, index) {
                            if(headers[index] && !td.hasAttribute('data-label')) {
                                td.setAttribute('data-label', headers[index]);
                            }
                        });
                    });
                }
                
                applyLabels();
                
                if (window.jQuery && $(table).hasClass('dataTable')) {
                    $(table).on('draw.dt', applyLabels);
                }
            });
        }
        
        applyTableLabels();
        
        if (window.jQuery) {
            $(document).on('init.dt', function() {
                applyTableLabels();
            });
        }
    });
</script>
</body>

</html>
 