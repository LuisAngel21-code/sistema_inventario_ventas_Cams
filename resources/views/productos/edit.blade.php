@extends('layouts.app')

@section('titulo', 'Editar producto')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h1 class="text-xl font-bold text-slate-800 mb-6">Editar {{ $producto->nombre }}</h1>
        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
            <ul class="list-disc pl-4">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif
        <form method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Nombre</label>
                    <input name="nombre" value="{{ $producto->nombre }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Categoria</label>
                    <select name="categoria" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-slate-600 bg-white focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                        @foreach (['Camas','Colchones','Bases','Accesorios'] as $cat)
                        <option value="{{ $cat }}" @selected($producto->categoria === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Descripción</label>
                <textarea name="descripcion" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">{{ $producto->descripcion }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Marca</label>
                    <input name="marca" value="{{ $producto->marca }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Proveedor</label>
                    <input name="proveedor" value="{{ $producto->proveedor }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Imagen del producto</label>
                @if ($producto->imagen)
                <div class="flex items-center gap-3 mb-2">
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200">
                    <span class="text-xs text-slate-500">Imagen actual</span>
                </div>
                @endif
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-600 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[#1e3a5f] file:text-white file:text-xs file:font-medium hover:file:bg-[#16304f]">
                <p class="text-xs text-slate-400 mt-1">JPG, PNG o WEBP. Máximo 2 MB. Déjalo vacío para conservar la actual.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Costo (S/)</label>
                    <input name="costo" type="number" step="0.01" value="{{ $producto->costo }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Precio base (S/)</label>
                    <input name="precio_base" type="number" step="0.01" value="{{ $producto->precio_base }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Stock minimo</label>
                <input name="stock_minimo" type="number" value="{{ $producto->stock_minimo }}" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
            </div>
            <button class="w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl bg-[#1e3a5f] text-white font-medium hover:bg-[#16304f] hover:shadow-md transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/></svg>
                Guardar cambios
            </button>
        </form>
    </div>
</div>
@endsection