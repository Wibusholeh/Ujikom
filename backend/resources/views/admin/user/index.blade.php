@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', 'Manajemen Pengguna Sistem')

@section('content')

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Error Validasi --}}
    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">

            <h3 class="text-lg font-bold text-gray-800">
                Daftar Pengguna
            </h3>

            <div class="flex items-center gap-3 w-full md:w-auto">

                {{-- Search --}}
                <form action="{{ route('admin.user.index') }}" method="GET" class="flex w-full md:w-80">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, email, role..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <button
                        type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                        Cari
                    </button>

                    @if(request('search'))
                        <a
                            href="{{ route('admin.user.index') }}"
                            class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                            Reset
                        </a>
                    @endif

                </form>

                {{-- Tambah User --}}
                <a
                    href="{{ route('admin.user.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                    + Tambah User
                </a>

            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">

            <table class="w-full text-left border-collapse">

                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">

                        <th class="py-3 px-4 border-b w-16 text-center">
                            No
                        </th>

                        <th class="py-3 px-4 border-b w-20 text-center">
                            Foto
                        </th>

                        <th class="py-3 px-4 border-b">
                            Nama
                        </th>

                        <th class="py-3 px-4 border-b">
                            Email
                        </th>

                        <th class="py-3 px-4 border-b">
                            Role
                        </th>

                        <th class="py-3 px-4 border-b">
                            No. HP
                        </th>

                        <th class="py-3 px-4 border-b w-48">
                            Aksi
                        </th>

                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">

                    @forelse($users as $index => $user)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="py-3 px-4 border-b text-center">
                                {{ method_exists($users, 'firstItem') ? $users->firstItem() + $index : $index + 1 }}
                            </td>

                            {{-- Kolom Foto Profil --}}
                            <td class="py-3 px-4 border-b text-center">
                                @if($user->foto && file_exists(public_path($user->foto)))
                                    <img src="{{ asset($user->foto) }}" alt="Foto" class="w-10 h-10 rounded-full object-cover mx-auto shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs mx-auto shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3 px-4 border-b font-medium text-gray-900">
                                {{ $user->name }}
                            </td>

                            <td class="py-3 px-4 border-b">
                                {{ $user->email }}
                            </td>

                            <td class="py-3 px-4 border-b">

                                @if($user->role === 'admin')

                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">
                                        Admin
                                    </span>

                                @elseif($user->role === 'petugas')

                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                                        Petugas
                                    </span>

                                @else

                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                        {{ ucfirst($user->role) }}
                                    </span>

                                @endif

                            </td>

                            <td class="py-3 px-4 border-b">
                                {{ $user->no_hp ?? '-' }}
                            </td>

                                                        <td class="py-3 px-4 border-b">

                                @php
                                    $isSelf = $user->id === auth()->id();
                                    $bolehEdit = !$user->is_super_admin || $isSelf;
                                    $bolehHapus = !$isSelf && !$user->is_super_admin;
                                @endphp

                                <div class="flex items-center space-x-2">

                                    @if($user->is_super_admin)
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            👑 Admin Utama
                                        </span>
                                    @endif

                                    {{-- Edit --}}
                                    @if($bolehEdit)
                                        <a
                                            href="{{ route('admin.user.edit', $user->id) }}"
                                            class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                            Edit
                                        </a>
                                    @endif

                                    {{-- Hapus --}}
                                    @if($bolehHapus)
                                        <form
                                            action="{{ route('admin.user.destroy', $user->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold transition">
                                                Hapus
                                            </button>

                                        </form>
                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="py-4 text-center text-gray-500">
                                @if(request('search'))
                                    Data pengguna dengan pencarian "{{ request('search') }}" tidak ditemukan.
                                @else
                                    Belum ada data pengguna.
                                @endif
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Pagination jika ada --}}
        @if(method_exists($users, 'links'))
            <div class="p-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif

    </div>

@endsection