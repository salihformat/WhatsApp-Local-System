<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __(config('app.name', 'Laravel')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap');

            body {
                font-family: 'Cairo', sans-serif !important;
            }

            .glass-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 24px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            }

            /* Custom WhatsApp Styles (No Build Required) */
            .wa-bg-gradient {
                background: linear-gradient(to bottom right, #075E54, #128C7E, #25D366);
            }
            .wa-btn-gradient {
                background: linear-gradient(to right, #25D366, #128C7E);
                color: #ffffff !important;
            }
            .wa-btn-gradient:hover {
                background: linear-gradient(to right, #128C7E, #075E54);
            }
            .wa-text-primary {
                color: #128C7E !important;
            }
            .wa-text-primary:hover {
                color: #075E54 !important;
            }
            .wa-focus:focus {
                border-color: #25D366 !important;
                box-shadow: 0 0 0 2px rgba(37, 211, 102, 0.2) !important;
            }
            .wa-checkbox {
                color: #25D366 !important;
            }
            .wa-checkbox:focus {
                box-shadow: 0 0 0 2px rgba(37, 211, 102, 0.2) !important;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 wa-bg-gradient relative overflow-hidden">
            <!-- Decorative WhatsApp-like background elements -->
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            
            <div class="mb-8 transform hover:scale-110 transition-transform duration-300 relative z-10 text-center">
                <a href="/" class="flex flex-col items-center justify-center gap-3">
                    <i class="fa-brands fa-whatsapp text-6xl text-white drop-shadow-lg"></i>
                    <h1 class="text-white text-3xl font-bold tracking-wider drop-shadow-md">رسائلي النظام المحلي</h1>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-4 px-8 py-10 glass-card relative z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
