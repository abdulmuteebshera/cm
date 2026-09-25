<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $general->site_name ?? 'Crownmaire Capital' }} — {{ $pageTitle ?? 'Email Campaign' }}</title>
    <link rel="shortcut icon" href="{{ asset(getImage(getFilePath('logoIcon') . '/favicon.png')) }}">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('assets/crm/css/crm.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('assets/ec/css/ec.css') }}?v=1">
    @stack('style')
</head>
<body class="crm-body @yield('body-class')">
    @yield('content')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @include('partials.notify')
    @stack('script')
</body>
</html>
