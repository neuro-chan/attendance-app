@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
@endsection

        {{-- 管理者ログインページかどうかで表示内容を出し分け --}}
@section('content')
    @php
        $isAdminLogin = request()->is('admin/login');
        $postUrl = $isAdminLogin ? url('/admin/login') : url('/login');
        $buttonText = $isAdminLogin ? '管理者ログインする' : 'ログインする';
        $titleText = $isAdminLogin ? '管理者ログイン' : 'ログイン';
    @endphp

    <div class="auth">
        <h1 class="auth__title">{{ $titleText }}</h1>

        <form class="auth__form" method="POST" action="{{ $postUrl }}" novalidate>
            @csrf

            {{-- メールアドレス --}}
            <div class="auth__field">
                <label class="auth__label" for="email">メールアドレス</label>
                <input
                    class="auth__input"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                >
                @error('email')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            {{-- パスワード --}}
            <div class="auth__field">
                <label class="auth__label" for="password">パスワード</label>
                <input
                    class="auth__input"
                    id="password"
                    type="password"
                    name="password"
                >
                @error('password')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <button class="auth__button" type="submit">{{ $buttonText }}</button>
        </form>

        {{-- 会員登録リンクはスタッフログインページのみ表示 --}}
        @unless ($isAdminLogin)
            <a class="auth__link" href="{{ url('/register') }}">会員登録はこちら</a>
        @endunless
    </div>
@endsection
