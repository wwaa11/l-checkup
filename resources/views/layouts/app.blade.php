<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Praram9 CheckUP</title>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('font-awesome/css/all.min.css') }}">
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body>
    <div class="flex">
        <div class=""></div>
        <div class="text-center">
            <img height="150px" src="{{ asset('images/logo.png') }}">
        </div>
        <div class="">
            <div class="selector__container">
                <form>
                    <select id="langSelecter" onchange="langSelect()">
                        <option autocomplete="off" @if (session('langSelect') == 'TH') selected @endif value="TH">
                            TH</option>
                        <option autocomplete="off" @if (session('langSelect') == 'ENG') selected @endif value="ENG">
                            ENG</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div>
        @yield('content')
    </div>
</body>
<script>
    function langSelect() {
        lang = $('#langSelecter').val();
        const formData = new FormData();
        formData.append('lang', lang);
        const res = axios.post("{{ env('APP_URL') }}" + "/changeLang", formData, {
            "Content-Type": "multipart/form-data"
        }).then((res) => {
            window.location.reload()
        })
    }
</script>
@yield('scripts')

</html>
