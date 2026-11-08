@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header-title', 'Profil Saya')

@section('content')
<div class="max-w-xl bg-white shadow rounded-xl overflow-hidden border border-gray-100">
    
    <!-- Banner Atas -->
    <div class="bg-gray-900 h-24"></div>

    <div class="px-6 pb-6">
        <!-- Foto Profil & Nama -->
        <div class="relative flex items-end gap-4 -mt-12 mb-6">
            @php
                $punyaFoto = $user->foto && file_exists(public_path($user->foto));
            @endphp
            
            <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 border-4 border-white shadow flex items-center justify-center">
                @if($punyaFoto)
                    <img src="{{ asset(implode('/', array_map('rawurlencode', explode('/', $user->foto)))) }}" 
                         alt="Foto {{ $user->name }}" 
                         class="w-full h-full object-cover">
                @else
                    <span class="text-3xl font-bold text-gray-600">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                @endif
            </div>

            <div class="pb-1">
                <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded capitalize">
                    {{ $user->role }}
                </span>
            </div>
        </div>

        <!-- Detail Informasi Akun (Read-Only) -->
        <div class="border-t border-gray-100 pt-4 space-y-4">
            <div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Nama User</div>
                <div class="text-base font-medium text-gray-800 mt-1">{{ $user->name }}</div>
            </div>

            <div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Alamat Email</div>
                <div class="text-base font-medium text-gray-800 mt-1">{{ $user->email }}</div>
            </div>

            <div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Tanggal Dibuat Akun</div>
                <div class="text-base font-medium text-gray-800 mt-1">
                    {{ $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') : '-' }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection