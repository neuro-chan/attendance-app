<header class="header">
    <div class="header__inner">

        {{-- ロゴ --}}
        @auth
            @if (auth()->user()->isAdmin())
                {{-- 管理者 --}}
                <a href="{{ route('admin.attendance.index') }}" class="header__link">
                    <img class="header__logo" src="{{ asset('img/logo.png') }}" alt="COACHTECHロゴ">
                </a>
            @else
                {{-- スタッフ --}}
                <a href="{{ route('attendance.record') }}" class="header__link">
                    <img class="header__logo" src="{{ asset('img/logo.png') }}" alt="COACHTECHロゴ">
                </a>
            @endif
        @else
            {{-- 未ログイン --}}
            <a href="{{ route('attendance.record') }}" class="header__link">
                <img class="header__logo" src="{{ asset('img/logo.png') }}" alt="COACHTECHロゴ">
            </a>
        @endauth

        {{-- ナビゲーション --}}
        @auth
            <nav class="header__nav" aria-label="Primary">
                <ul class="header__list">
                    {{-- メニュー --}}
                    @if (auth()->user()->isAdmin())
                        {{-- 管理者 --}}
                        <li class="header__item">
                            <a href="{{ route('admin.attendance.index') }}" class="header__link">勤怠一覧</a>
                        </li>
                        <li class="header__item">
                            <a href="{{ route('admin.staff.index') }}" class="header__link">スタッフ一覧</a>
                        </li>
                        <li class="header__item">
                            <a class="header__link" href="{{ route('request.index') }}">申請一覧</a>
                        </li>
                    @else
                        {{-- スタッフ --}}
                        <li class="header__item">
                            <a href="{{ route('attendance.record') }}" class="header__link">勤怠</a>
                        </li>
                        <li class="header__item">
                            <a href="{{ route('staff.index') }}" class="header__link">勤怠一覧</a>
                        </li>
                        <li class="header__item">
                            <a href="{{ route('request.index') }}" class="header__link">申請</a>
                        </li>
                    @endif

                    {{-- ログアウト --}}
                    <li class="header__item">
                        @if (auth()->user()->isAdmin())
                            {{-- 管理者 --}}
                            <form class="header__logout" method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="header__link header__link--button">ログアウト</button>
                            </form>
                        @else
                            {{-- スタッフ --}}
                            <form class="header__logout" method="POST" action="{{ url('/logout') }}">
                                @csrf
                                <button type="submit" class="header__link header__link--button">ログアウト</button>
                            </form>
                        @endif
                    </li>
                </ul>
            </nav>
        @endauth

    </div>
</header>
