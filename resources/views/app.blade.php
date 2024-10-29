<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

  {{-- Company Logo --}}
  <link rel="icon" href="{{ asset('img/tei_logo.png') }}" type="image/png" sizes="16x16">

  <title>TEI Dashboard</title>

  @vite('resources/js/app.js')
  @inertiaHead
</head>

<body class="dark:bg-primary tei-fonts">
  @inertia
</body>

</html>
