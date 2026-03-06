<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', '勤怠管理アプリ')</title>

  <!-- CSS -->
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/header.css') }}">
  @yield('css')
</head>

<body>
  @include('components.header')

  <main class="l-main">
    @yield('content')
  </main>

  @yield('js')
</body>
</html>
