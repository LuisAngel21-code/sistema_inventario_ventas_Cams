@extends('layouts.app')

@section('titulo', 'Inventario')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#1e293b]">Inventario</h1>
            <p class="text-slate-500 text-sm mt-1">Stock disponible por sucursal</p>
        </div>
    </div>

    <form class="flex flex-wrap gap-2 mb-6" method="GET" action="{{ route('inventario.index') }}">
        <select name="categoria" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 bg-white focus:border-[#1e3a5f] outline-none">
            <option value="">Todas las categorias</option>
            @foreach ($categorias as $categoria)
            <option value="{{ $categoria }}" @selected(request('categoria') === $categoria)>{{ $categoria }}</option>
            @endforeach
        </select>
        <select name="sucursal_id" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 bg-white focus:border-[#1e3a5f] outline-none">
            <option value="">Todas las sucursales</option>
            @foreach ($sucursales as $sucursal)
            <option value="{{ $sucursal->id }}" @selected((string) request('sucursal_id') === (string) $sucursal->id)>{{ $sucursal->nombre }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Filtrar</button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 text-xs uppercase tracking-wide border-b border-slate-100 bg-slate-50/50">
                    <th class="py-3.5 px-5">Producto</th>
                    <th class="py-3.5 px-5">Categoria</th>
                    <th class="py-3.5 px-5">Sucursal</th>
                    <th class="py-3.5 px-5 text-right">Cantidad disponible</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stocks as $stock)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                    <td class="py-3.5 px-5 font-medium text-[#1e293b]">{{ $stock->producto->nombre }}</td>
                    <td class="py-3.5 px-5">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-[#1e3a5f]/5 text-[#1e3a5f]">
                            {{ $stock->producto->categoria }}
                        </span>
                    </td>
                    <td class="py-3.5 px-5 text-slate-600">{{ $stock->sucursal->nombre }}</td>
                    <td class="py-3.5 px-5 text-right">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $stock->cantidad <= $stock->producto->stock_minimo ? 'bg-amber-50 border border-amber-200 text-amber-700' : 'bg-emerald-50 border border-emerald-200 text-emerald-700' }}">
                            {{ $stock->cantidad }} unid.
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-10 text-center text-slate-500">No hay stock registrado para los filtros seleccionados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
