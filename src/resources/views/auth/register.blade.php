@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
@endsection

@section('content')
    <div class="auth auth--register">
        <h1 class="auth__title">会員登録</h1>

        <form class="auth__form" method="POST" action="{{ url('/register') }}" novalidate>
            @csrf

            <div class="auth__field">
                <label class="auth__label" for="name">名前</label>
                <input
                    class="auth__input"
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                >
                @error('name')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

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

            <div class="auth__field">
                <label class="auth__label" for="password_confirmation">パスワード確認</label>
                <input
                    class="auth__input"
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                >
                @error('password_confirmation')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <button class="auth__button" type="submit">登録する</button>
        </form>

        <a class="auth__link" href="{{ url('/login') }}">ログインはこちら</a>
    </div>
@endsection
