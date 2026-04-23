@extends('layouts.superadmin')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-2xl mr-4">
                📍
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Posyandu</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $countPosyandu ?? 0 }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 bg-pink-100 text-pink-600 rounded-xl flex items-center justify-center text-2xl mr-4">
                👩‍⚕️
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Bidan/Kader</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $countUser ?? 0 }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
            <div class="w-14 h-14 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-2xl mr-4">
                📊
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Laporan Aktif</p>
                <h3 class="text-2xl font-bold text-gray-800">12</h3>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Posyandu Baru Terdaftar</h3>
            <a href="{{ route('superadmin.posyandu.index') }}"
                class="text-pink-600 text-sm font-semibold hover:underline">Lihat Semua</a>
        </div>
        <div class="p-0">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 font-medium">
                    <tr>
                        <th class="px-6 py-4">Nama Posyandu</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-700">Posyandu Mawar</td>
                        <td class="px-6 py-4 text-gray-500">Lohbener Timur</td>
                        <td class="px-6 py-4"><span
                                class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Aktif</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection