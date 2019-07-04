
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <div class="container">
        
        @guest
            <a class="navbar-brand js-scroll-trigger" href="{{ action('\App\Http\Controllers\HomeController@index') }}">Kalender</a>
            <div class=" " id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                        <li>
                            <a class="nav-link js-scroll-trigger" href="{{ action('\App\Http\Controllers\Auth\LoginController@showLoginForm') }}">Login</a>
                        </li>
                </ul>
            </div>
        @else
            <a class="navbar-brand js-scroll-trigger" href="{{ route('generate-season.index') }}">Kalender</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="{{ action('\App\Http\Controllers\SeasonsController@index') }}">Seizoenen</a>
                    </li>
                    @if(Auth::user()->role == 'Admin')
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="{{ action('\App\Http\Controllers\User\UserController@index') }}">Gebruikers</a>
                        </li>
                    @endif
                    @if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Editor')
                        <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="{{ action('\App\Http\Controllers\GroupsController@index') }}">Groepen</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="{{ action('\App\Http\Controllers\User\ProfileController@edit') }}">{{Auth::user()->firstname}}</a>
                    </li>

                    <li class="nav-item">
                            <a class="nav-link js-scroll-trigger" href="{{ action('\App\Http\Controllers\Auth\AuthenticationController@logout') }}">Logout</a>
                        </li>
                </ul>
            </div>
        @endguest
    </div>
</nav>
<br><br>