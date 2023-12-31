<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" def />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" href="{{ asset('icons/logo.svg') }}" type="image/x-icon">
    <script src="{{ asset('js/admin-js/jQuery.js') }}"></script>
    <script src="{{ asset('js/admin-js/sideBar.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
    <script src='{{ asset('js/admin-js/adminAccount.js') }}'></script>
    @stack('livewire:scripts')

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"
        integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    <script src="{{ asset('js/jQuery.js') }}"></script>
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.3/dist/cdn.min.js"></script> --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <title>@yield('title', 'Thesis Kiosk')</title>
    @vite('resources/css/app.css')
    @livewireStyles
    @livewireScripts
</head>
<style>
    #nprogress {
        height: 0.2rem;
        width: $glow-width;
        max-width: 100%;
        float: right;

    }

    #nprogress::before,
    #nprogress::after {
        content: '';
        display: block;
        position: relative;
        border-radius: 0px 2px 2px 0px;
    }

    #nprogress::before {
        background: transparent;
        box-shadow: 0px 0px $glow-radius $bar-color, 0px 0px $glow-radius $glow-color;
        z-index: -5;
    }

    #nprogress::after {
        background: linear-gradient(to right, $background-color 0%, transparent 100%);
        height:calc(100% + #{$glow-radius} + #{$glow-radius});
        width:calc(100% + #{$glow-radius});
        top: (-$glow-radius);
        left: (-$glow-radius);
        z-index: -3;
    }
</style>

<body class="gradient-bg-light custom-scrollbar m-0 flex p-0 font-poppins">

    {{-- sidebar --}}
    <x-admin.admin_SideBar />
    <style>
        #nprogress .bar {
            height: 0.2rem;
            width: $glow-width;
            max-width: 100%;
            float: right;

        }

        #nprogress::before,
        #nprogress::after {
            content: '';
            display: block;
            position: relative;
            border-radius: 0px 2px 2px 0px;
        }

        #nprogress::before {
            background: transparent;
            box-shadow: 0px 0px $glow-radius $bar-color, 0px 0px $glow-radius $glow-color;
            z-index: -5;
        }

        #nprogress::after {
            background: linear-gradient(to right, $background-color 0%, transparent 100%);
            height:calc(100% + #{$glow-radius} + #{$glow-radius});
            width:calc(100% + #{$glow-radius});
            top: (-$glow-radius);
            left: (-$glow-radius);
            z-index: -3;
        }
    </style>
    <div class="flex w-full max-w-full flex-col">
        {{-- navbar --}}
        <x-admin.admin_navbar />
        <x-session_flash />

        {{ $slot }}


    </div>

</body>

</html>
