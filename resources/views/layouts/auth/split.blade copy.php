<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-neutral-950">
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col p-12 text-white lg:flex border-r border-white/10 overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/login_bg.png') }}" class="h-full w-full object-cover opacity-40"
                    alt="Background">
                <div class="absolute inset-0 bg-gradient-to-b from-neutral-900/60 via-neutral-950/80 to-neutral-950">
                </div>
            </div>

            <div class="relative z-10 flex items-center gap-2 text-2xl font-bold tracking-tight">
                <x-app-logo-icon class="h-8 w-8 fill-current text-white" />
                <span>{{ config('app.name', 'LinceB') }}</span>
            </div>

            <div class="relative z-10 mt-auto mb-20 max-w-lg">
                <h1 class="text-5xl font-bold leading-tight tracking-tight text-white mb-6">
                    Impulsa tu gestión empresarial .
                </h1>
                <p class="text-xl text-white/60 leading-relaxed">
                    Optimiza procesos, automatiza flujos de trabajo y toma decisiones basadas en datos con la plataforma
                    SaaS líder en el sector.
                </p>
            </div>

            <div class="relative z-10 mt-auto text-sm text-white/40">
                &copy; {{ date('Y') }} LinceB Technologies Inc. Todos los derechos reservados.
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