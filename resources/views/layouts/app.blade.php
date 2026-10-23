<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Tienda Cams')</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f8fafc] min-h-screen font-sans antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @php
            $esAdmin = auth()->user()->esAdmin();
            $esAlmacen = auth()->user()->esEncargadoAlmacen();
            $esTienda = auth()->user()->esEncargadoTienda();
            $activo = function($ruta) { return request()->routeIs($ruta) ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'; };
        @endphp
        <aside class="w-64 bg-[#1e3a5f] text-white flex flex-col fixed inset-y-0">
            <div class="px-5 py-5 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('logo.png') }}" alt="Logo CAMS" class="w-full h-full object-contain p-1" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <span class="w-full h-full items-center justify-center font-bold text-lg hidden" style="display:none">C</span>
                    </div>
                    <div>
                        <p class="font-bold text-sm">Tienda Cams</p>
                        <p class="text-xs text-white/60 capitalize">{{ auth()->user()->rol }}</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @if ($esAdmin)
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activo('dashboard') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                    Dashboard
                </a>
                @endif

                @if ($esAdmin || $esAlmacen)
                <p class="px-3 pt-3 pb-1 text-xs uppercase text-white/40">Catalogo</p>
                <a href="{{ route('productos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activo('productos.*') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg>
                    Productos
                </a>
                @endif

                @if ($esAdmin || $esAlmacen)
                <p class="px-3 pt-3 pb-1 text-xs uppercase text-white/40">Inventario</p>
                <a href="{{ route('inventario.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activo('inventario.index') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 3h18v4H3z"/><path d="M5 7v14h14V7"/><path d="M9 11h6"/><path d="M9 15h6"/></svg>
                    Inventario
                </a>
                <a href="{{ route('inventario.movimientos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activo('inventario.movimientos') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 12a9 9 0 1 1-9-9"/><path d="M21 3v6h-6"/></svg>
                    Movimientos
                </a>
                <a href="{{ route('inventario.ingreso') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activo('inventario.ingreso') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M16 3h5v5"/><path d="M21 3l-7 7"/><path d="M3 12v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3"/><path d="M3 12h4"/></svg>
                    Ingreso mercaderia
                </a>
                @endif

                @if ($esAdmin || $esTienda)
                <p class="px-3 pt-3 pb-1 text-xs uppercase text-white/40">Ventas</p>
                <a href="{{ route('ventas.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activo('ventas.create') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                    Nueva venta
                </a>
                <a href="{{ route('ventas.historial') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activo('ventas.historial') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13l2 2 4-4"/></svg>
                    Historial
                </a>
                @endif

                @if ($esAdmin)
                <p class="px-3 pt-3 pb-1 text-xs uppercase text-white/40">Gestion</p>
                <a href="{{ route('reportes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activo('reportes.*') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M8 17V9"/><path d="M13 17V5"/><path d="M18 17v-3"/></svg>
                    Reportes
                </a>
                @endif

                @if (auth()->user()->esAdministrador())
                <a href="{{ route('sucursales.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors {{ $activo('sucursales.*') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 9h1"/><path d="M9 13h1"/><path d="M9 17h1"/></svg>
                    Sucursales
                </a>
                @endif
            </nav>
            <div class="px-4 py-3 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                        Cerrar sesion
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenido -->
        <main class="flex-1 ml-64 p-6 lg:p-8">
            @if (session('status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm mb-6">
                {{ session('status') }}
            </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>