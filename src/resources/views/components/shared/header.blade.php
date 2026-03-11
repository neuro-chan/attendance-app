<header class="header">
    <div class="header__inner">
        <a href="{{ route('attendance.record') }}" class="header__link">
            <img class="header__logo" src="{{ asset('img/logo.png') }}" alt="COACHTECHロゴ">
        </a>

        @auth
            <nav class="header__nav" aria-label="Primary">
                <ul class="header__list">
                    @if (auth()->user()->isAdmin())
                        <li class="header__item">
                            <a class="header__link" href="#">勤怠一覧</a>
                        </li>
                        <li class="header__item">
                            <a class="header__link" href="#">スタッフ一覧</a>
                        </li>
                        <li class="header__item">
                            <a class="header__link" href="#">申請一覧</a>
                        </li>
                    @else
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

                    <li class="header__item">
                        <form class="header__logout" method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button type="submit" class="header__link header__link--button">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
        @endauth
    </div>
</header>
