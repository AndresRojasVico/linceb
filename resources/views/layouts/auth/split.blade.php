<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-neutral-950">
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col items-center justify-center lg:flex border-r border-slate-200 bg-white overflow-hidden">

            <!-- Logo centrado -->
            <div class="flex flex-1 flex-col items-center justify-center px-16 gap-8">
                <img src="{{ asset('images/integra_logo.png') }}" alt="Integra Informática"
                    style="width: 420px; max-width: 100%;">


            </div>

            <!-- Copyright -->
            <div class="pb-8 text-xs text-slate-400 text-center px-8">
                &copy; {{ date('Y') }} Integra Informática Técnica Granada S.L.<br>Todos los derechos reservados.
            </div>
        </div>

        <div class="w-full lg:p-8 flex items-center justify-center bg-white dark:bg-transparent">
            <div class="mx-auto flex w-full flex-col justify-center space-y-8 sm:w-[420px]">
                <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden mb-6"
                    wire:navigate>
                    <x-app-logo-icon class="size-10 fill-current text-black dark:text-white" />
                    <span class="text-2xl font-bold">{{ config('app.name', 'LinceB') }}</span>
                </a>

                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>