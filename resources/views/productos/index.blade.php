@extends('layouts.app')

@section('titulo', 'Productos')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#1e293b]">Productos</h1>
            <p class="text-slate-500 text-sm mt-1">Catalogo y control de stock</p>
        </div>
        <a href="{{ route('productos.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1e3a5f] text-white text-sm font-medium hover:bg-[#16304f] transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            Nuevo producto
        </a>
    </div>

    <!-- Filtro -->
    <form class="flex gap-2 mb-6">
        <div class="relative">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input name="busqueda" value="{{ request('busqueda') }}" placeholder="Buscar por nombre..." class="pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none w-64 transition-all">
        </div>
        <select name="categoria" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 bg-white focus:border-[#1e3a5f] outline-none">
            <option value="">Todas las categorias</option>
            @foreach ($categorias as $categoria)
            <option value="{{ $categoria }}" @selected(request('categoria') === $categoria)>{{ $categoria }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Filtrar</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 text-xs uppercase tracking-wide border-b border-slate-100 bg-slate-50/50">
                    <th class="py-3.5 px-5">Codigo</th>
                    <th class="py-3.5 px-5">Nombre</th>
                    <th class="py-3.5 px-5">Categoria</th>
                    <th class="py-3.5 px-5 text-right">Precio</th>
                    <th class="py-3.5 px-5 text-right">Stock</th>
                    <th class="py-3.5 px-5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3.5 px-5 text-slate-400 font-mono text-xs">{{ $producto->codigo }}</td>
                    <td class="py-3.5 px-5">
                        <div class="flex items-center gap-3">
                            @if ($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 flex-shrink-0">
                            @else
                            <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            </div>
                            @endif
                            <div>
                                <p class="font-medium text-[#1e293b]">{{ $producto->nombre }}</p>
                                <p class="text-xs text-slate-500 mt-0.5 max-w-[220px] truncate">{{ $producto->descripcion }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 px-5">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-[#1e3a5f]/5 text-[#1e3a5f]">{{ $producto->categoria }}</span>
                    </td>
                    <td class="py-3.5 px-5 text-right text-[#1e293b] font-medium">S/ {{ number_format($producto->precio_base, 2) }}</td>
                    <td class="py-3.5 px-5 text-right">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $producto->tieneStockBajo() ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-emerald-50 border border-emerald-200 text-emerald-700' }}">
                            {{ $producto->stock }} unid.
                        </span>
                    </td>
                    <td class="py-3.5 px-5 text-right space-x-1">
                        <a href="{{ route('productos.edit', $producto) }}" title="Editar" class="inline-flex p-2 rounded-lg text-slate-500 hover:text-[#1e3a5f] hover:bg-[#1e3a5f]/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                        </a>
                        <button title="Actualizar stock" onclick="document.getElementById('stock-{{ $producto->id }}').showModal()" class="inline-flex p-2 rounded-lg text-slate-500 hover:text-[#1e3a5f] hover:bg-[#1e3a5f]/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-10 text-center text-slate-500">No hay productos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $productos->links() }}</div>

    <!-- Modal actualizar stock (HU-05) -->
    @foreach ($productos as $producto)
    <dialog id="stock-{{ $producto->id }}" class="rounded-2xl p-6 shadow-xl w-80 border border-slate-200">
        <h3 class="font-semibold text-[#1e293b] mb-4">Actualizar stock</h3>
        <p class="text-sm text-slate-500 mb-4">{{ $producto->nombre }}</p>
        <form method="POST" action="{{ route('productos.stock', $producto) }}">
            @csrf @method('PUT')
            <label class="block text-sm text-slate-600 mb-1">Cantidad disponible</label>
            <input type="number" name="stock" value="{{ $producto->stock }}" min="0" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none mb-4 transition-all">
            <div class="flex gap-2">
                <button type="button" onclick="this.closest('dialog').close()" class="flex-1 px-3 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Cancelar</button>
                <button class="flex-1 px-3 py-2.5 rounded-xl bg-[#1e3a5f] text-white text-sm font-medium hover:bg-[#16304f] transition-colors">Guardar</button>
            </div>
        </form>
    </dialog>
    @endforeach
</div>
@endsection