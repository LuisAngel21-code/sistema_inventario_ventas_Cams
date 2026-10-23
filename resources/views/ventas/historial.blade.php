@extends('layouts.app')

@section('titulo', 'Historial de ventas')
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Historial de ventas</h1>
            <p class="text-slate-500 text-sm mt-1">Control de operaciones realizadas</p>
        </div>
        <a href="{{ route('ventas.create') }}" class="px-4 py-2.5 rounded-lg bg-[#1e3a5f] text-white text-sm font-medium hover:bg-[#16304f] transition-colors">+ Nueva venta</a>
    </div>

    <form class="flex gap-2 mb-6">
        <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-[#1e3a5f] outline-none">
        <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-[#1e3a5f] outline-none">
        <button class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-600 hover:bg-slate-50">Filtrar</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 border-b border-slate-200 bg-slate-50">
                    <th class="py-3 px-4">N°</th>
                    <th class="py-3 px-4">Fecha</th>
                    <th class="py-3 px-4">Sucursal</th>
                    <th class="py-3 px-4">Cliente</th>
                    <th class="py-3 px-4">Productos</th>
                    <th class="py-3 px-4 text-right">Total</th>
                    <th class="py-3 px-4 text-right">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ventas as $venta)
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="py-3 px-4 text-slate-500">#{{ $venta->id }}</td>
                    <td class="py-3 px-4 text-slate-600">{{ $venta->fecha->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-4 text-slate-600">{{ $venta->sucursal->nombre }}</td>
                    <td class="py-3 px-4 text-slate-600">{{ $venta->cliente_nombre ?? '—' }}</td>
                    <td class="py-3 px-4 text-slate-600">{{ $venta->detalleVentas->pluck('producto.nombre')->join(', ') }}</td>
                    <td class="py-3 px-4 text-right font-medium text-slate-800">S/ {{ number_format($venta->total, 2) }}</td>
                    <td class="py-3 px-4 text-right">
                        <a href="{{ route('ventas.detalle', $venta) }}" class="text-[#1e3a5f] hover:underline text-xs">Ver</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-8 text-center text-slate-500">Sin ventas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $ventas->links() }}</div>
</div>
@endsection