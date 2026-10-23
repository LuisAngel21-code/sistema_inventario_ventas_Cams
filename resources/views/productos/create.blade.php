@extends('layouts.app')

@section('titulo', 'Nuevo producto')
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h1 class="text-xl font-bold text-slate-800 mb-6">Registrar producto</h1>
        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Codigo</label>
                    <input name="codigo" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Categoria</label>
                    <select name="categoria" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-slate-600 bg-white focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                        <option value="Camas">Camas</option>
                        <option value="Colchones">Colchones</option>
                        <option value="Bases">Bases</option>
                        <option value="Accesorios">Accesorios</option>
                    </select>
                </div>
            </div>
<div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Nombre</label>
                <input name="nombre" required class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-[#1e3a5f] outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Descripción</label>
                <textarea name="descripcion" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-[#1e3a5f] outline-none" placeholder="Madera sólida, medidas..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Marca</label>
                    <input name="marca" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-[#1e3a5f] outline-none" placeholder="Cams, Rosen...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Proveedor</label>
                    <input name="proveedor" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-[#1e3a5f] outline-none" placeholder="Proveedor Cams">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-600 mb-1">Imagen del producto</label>
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-600 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[#1e3a5f] file:text-white file:text-xs file:font-medium hover:file:bg-[#16304f]">
                <p class="text-xs text-slate-400 mt-1">JPG, PNG o WEBP. Máximo 2 MB.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Costo (S/)</label>
                    <input name="costo" type="number" step="0.01" min="0" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Precio base (S/)</label>
                    <input name="precio_base" type="number" step="0.01" min="0" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Stock inicial</label>
                    <input name="stock" type="number" min="0" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Stock minimo</label>
                    <input name="stock_minimo" type="number" min="0" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#1e3a5f] focus:ring-2 focus:ring-[#1e3a5f]/10 outline-none transition-all">
                </div>
            </div>
            <button class="w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl bg-[#1e3a5f] text-white font-medium hover:bg-[#16304f] hover:shadow-md transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/></svg>
                Registrar producto
            </button>
        </form>
    </div>
</div>
@endsection