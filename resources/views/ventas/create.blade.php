@extends('layouts.app')

@section('titulo', 'Nueva venta')
@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <h1 class="text-xl font-bold text-slate-800 mb-6">Registrar venta</h1>
        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
            <ul class="list-disc pl-4">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('ventas.store') }}" x-data="{ items: [ { producto_id: '', cantidad: 1 } ] }">
            @csrf
            <input type="hidden" name="sucursal_id" value="{{ $sucursalId }}">

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Cliente</label>
                    <input name="cliente_nombre" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-[#1e3a5f] outline-none" placeholder="Nombre del cliente">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Vendedor</label>
                    <input name="vendedor_nombre" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-[#1e3a5f] outline-none" placeholder="Nombre del vendedor">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Tipo de pago</label>
                    <select name="tipo_pago" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-slate-600 bg-white focus:border-[#1e3a5f] outline-none">
                        <option value="efectivo">Efectivo</option>
                        <option value="yape">Yape</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Comprobante</label>
                    <select name="comprobante_tipo" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-slate-600 bg-white focus:border-[#1e3a5f] outline-none">
                        <option value="boleta">Boleta</option>
                        <option value="factura">Factura</option>
                    </select>
                </div>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex gap-3 items-end border border-slate-200 rounded-lg p-3">
                        <div class="flex-1">
                            <label class="block text-xs text-slate-500 mb-1">Producto</label>
                            <select :name="'items['+index+'][producto_id]'" x-model="item.producto_id" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-slate-600 bg-white text-sm focus:border-[#1e3a5f] outline-none">
                                <option value="">Seleccionar...</option>
                                @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}">{{ $producto->nombre }} (stock: {{ $producto->stock_sucursal }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-24">
                            <label class="block text-xs text-slate-500 mb-1">Cantidad</label>
                            <input type="number" :name="'items['+index+'][cantidad]'" x-model="item.cantidad" min="1" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-[#1e3a5f] outline-none">
                        </div>
                        <button type="button" @click="items.splice(index, 1)" x-show="items.length > 1" class="px-3 py-2 text-slate-400 hover:text-red-500 text-sm">✕</button>
                    </div>
                </template>
            </div>

            <button type="button" @click="items.push({ producto_id: '', cantidad: 1 })" class="mt-3 text-sm text-[#1e3a5f] hover:underline">+ Agregar producto</button>

            <button class="w-full mt-6 py-3 rounded-lg bg-[#1e3a5f] text-white font-medium hover:bg-[#16304f] transition-colors">Registrar venta</button>
        </form>
    </div>
</div>
@endsection