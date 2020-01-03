<nav class="flex items-center justify-between flex-wrap py-6 px-16 fixed top-0 w-full z-10 {{ $light ?? 'bg-gray-300 shadow' }}">
    @if(isset($event))
    <a href="{{ Auth::guest() ? '/' : '/home' }}">
        @if(isset($event->logo_dark))
        <img src="{{ Storage::url($event->logo_dark) }}" alt="{{ $event->title }} Logo" class="w-40 mx-auto block" width="265px">
        @else
        {{ $event->title }}
        @endif
    </a>
    @else
    <a href="{{ Auth::guest() ? '/' : '/home' }}">
        @if(isset($light) && $light)
        <img src="{{ asset('img/sgdinstitute-logo-white.png') }}" class="w-40 mx-auto block" alt="Institute Logo">
        @else
        <img src="{{ asset('img/logo.png') }}" class="w-40 mx-auto block" alt="Institute Logo">
        @endif
    </a>
    @endif
    <div class="block lg:hidden">
        <button class="flex items-center px-3 py-2 border focus:outline-none rounded {{ isset($light) ? 'text-white hover:text-black hover:bg-white transistion' : 'text-gray-800 hover:bg-gray-800 hover:text-white transition border-gray-800' }}" id="menu">
            <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <title>Menu</title>
                <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
            </svg>
        </button>
    </div>
    <div class="text-center mt-6 lg:mt-0 lg:text-left w-full hidden lg:flex lg:items-center lg:w-auto {{ isset($light) ? 'rounded shadow lg:shadow-none lg:rounded-none bg-gray-200 lg:bg-transparent' : '' }}" id="nav">
        <a class=" mt-4 p-4 lg:p-0 inline-block lg:mt-0 {{ isset($light) ? 'lg:text-gray-400 lg:hover:text-white' : 'text-gray-800 hover:text-gray-900' }} hover:underline mr-4" href="{{ Auth::guest() ? '/' : '/home' }}">Home</a>
        <a class=" mt-4 p-4 lg:p-0 inline-block lg:mt-0 {{ isset($light) ? 'lg:text-gray-400 lg:hover:text-white' : 'text-gray-800 hover:text-gray-900' }} hover:underline mr-4" href="/donations/create">Donate</a>
        @if(isset($event))
        <a class=" mt-4 p-4 lg:p-0 inline-block lg:mt-0 {{ isset($light) ? 'lg:text-gray-400 lg:hover:text-white' : 'text-gray-800 hover:text-gray-900' }} hover:underline mr-4" target="_blank" href="{{ collect($event->links)->where('icon', 'website')->first()['link'] }}">Event Website</a>
        @endif

        @guest
        <a class="mt-4 p-4 inline-block lg:mt-0 {{ isset($light) ? 'lg:text-gray-400 lg:hover:text-white' : 'text-gray-800 hover:text-gray-900' }} hover:underline mr-4" href="{{ route('login') }}">Login</a>
        <a class="mt-4 p-4 inline-block lg:mt-0 {{ isset($light) ? 'lg:text-gray-400 lg:hover:text-white' : 'text-gray-800 hover:text-gray-900' }} hover:underline" href="{{ route('register') }}">Create an Account</a>
        @else
        @can('view_dashboard')
        <a class="mt-4 p-4 inline-block lg:mt-0 {{ isset($light) ? 'lg:text-gray-400 lg:hover:text-white' : 'text-gray-800 hover:text-gray-900' }} hover:underline mr-4" href="/nova">Nova</a>
        @endcan
        @if (session('sgdinstitute:impersonator'))--}}
        <a class="mt-4 p-4 inline-block lg:mt-0 {{ isset($light) ? 'lg:text-gray-400 lg:hover:text-white' : 'text-gray-800 hover:text-gray-900' }} hover:underline mr-4" href="/users/stop-impersonating">
            <i class="fa fa-fw fa-btn fa-user-secret"></i>Back To My Account
        </a>
        @endif
        <a class="mt-4 p-4 inline-block lg:mt-0 {{ isset($light) ? 'lg:text-gray-400 lg:hover:text-white' : 'text-gray-800 hover:text-gray-900' }} hover:underline" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
        @endguest
    </div>
</nav>