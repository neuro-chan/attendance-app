@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
@endsection

@section('content')
    <div class="auth">
        <div class="email-verify__actions">
            <p class="email-verify__message">登録していただいたメールアドレスに認証メールを送付しました。
                <br>メール認証を完了してください。
            </p>
            <a href="https://mailtrap.io/inboxes/" target="_blank" class="email-verify__button">
                認証はこちらから
            </a>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="email-verify__resend">認証メールを再送する</button>
            </form>
            @if (session('status') == 'verification-link-sent')
                <p>認証メールを再送しました</p>
            @endif
        </div>

    </div>
@endsection
