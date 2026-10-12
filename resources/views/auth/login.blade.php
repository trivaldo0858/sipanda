@extends('layouts.auth')

@section('title', 'Login Posyandu-In')

@section('content')
    <div class="min-h-screen bg-slate-50 flex flex-col justify-center items-center p-6 font-sans">

        {{-- Header --}}
        <div class="text-center mb-10">
    <h1 class="text-5xl font-black text-blue-600 tracking-tighter">
        Posyandu-In
    </h1>
    <p class="text-slate-500 font-medium text-sm mt-2">
        Sistem Posyandu Kabupaten Indramayu
    </p>
</div>

        {{-- Kartu Login --}}
        <div class="w-full max-w-[440px] bg-white p-12 rounded-[3rem]
            shadow-2xl shadow-slate-200/60 border border-slate-50">


{{-- Pilihan Role --}}
<div class="flex flex-row items-center w-full gap-1
            p-1 bg-slate-100 rounded-full mb-8">

    <button type="button"
        data-role="Bidan"
        class="role-button flex-1 min-w-0 h-9 rounded-full
               flex items-center justify-center whitespace-nowrap
               text-xs font-medium text-slate-500
               transition-all duration-200">
        Bidan
    </button>

    <button type="button"
        data-role="Posyandu"
        class="role-button flex-1 min-w-0 h-9 rounded-full
               flex items-center justify-center whitespace-nowrap
               text-xs font-medium text-slate-500
               transition-all duration-200">
        Posyandu
    </button>

    <button type="button"
        data-role="OrangTua"
        class="role-button flex-1 min-w-0 h-9 rounded-full
               flex items-center justify-center whitespace-nowrap
               text-xs font-medium text-slate-500
               transition-all duration-200">
        Orang Tua
    </button>

</div>

            {{-- Form Login --}}
            <form id="loginForm" action="{{ url('/login/bidan') }}" method="POST" class="space-y-6">
                @csrf

                <input type="hidden" name="role" id="role" value="Bidan">

                {{-- Username Bidan --}}
                <div id="usernameField">
                   <label class="block text-[11px] font-bold text-slate-400
              uppercase tracking-[2px] ml-1">
                        Username
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                        placeholder="Masukkan username" class="w-full border-b-2 border-slate-200 focus:border-blue-600
                               outline-none py-3 text-slate-700">
                </div>

                {{-- Pilihan Posyandu --}}
                <div id="posyanduField" class="hidden">
<label class="block text-[11px] font-bold text-slate-400
              uppercase tracking-[2px] ml-1">
                        Lokasi Posyandu
                    </label>
                    <select name="id_posyandu" id="id_posyandu" class="w-full border-b-2 border-slate-200 focus:border-blue-600
                               outline-none py-3 bg-white text-slate-700">
                        <option value="">Pilih Posyandu</option>
                        @foreach ($posyandu as $item)
                            <option value="{{ $item->id_posyandu }}" {{ old('id_posyandu') == $item->id_posyandu ? 'selected' : '' }}>
                                {{ $item->nama_posyandu }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- NIK Anak --}}
                <div id="nikField" class="hidden">
                    <label class="block text-[11px] font-bold text-slate-400
              uppercase tracking-[2px] ml-1">
                        NIK Anak
                    </label>
                    <input type="text" name="nik_anak" id="nik_anak" inputmode="numeric" maxlength="16"
                        placeholder="Masukkan NIK anak" class="w-full border-b-2 border-slate-200 focus:border-blue-600
                               outline-none py-3 text-slate-700">
                </div>

                {{-- Tanggal Lahir Anak --}}
                <div id="tanggalField" class="hidden">
                    <label class="block text-[11px] font-bold text-slate-400
              uppercase tracking-[2px] ml-1">
                        Tanggal Lahir Anak
                    </label>
                    <input type="date" name="tgl_lahir" id="tgl_lahir" class="w-full border-b-2 border-slate-200 focus:border-blue-600
                               outline-none py-3 text-slate-700">
                </div>

                {{-- Password --}}
                <div id="passwordField">
                    <label class="block text-[11px] font-bold text-slate-400
              uppercase tracking-[2px] ml-1">
                        Kata Sandi
                    </label>
                    <input type="password" name="password" id="password" placeholder="Masukkan kata sandi" class="w-full border-b-2 border-slate-200 focus:border-blue-600
                               outline-none py-3 text-slate-700">
                </div>

                {{-- Pesan error --}}
                @if ($errors->any())
                    <div class="text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit"
    class="w-full bg-blue-600 text-white py-5 rounded-2xl
           font-bold shadow-lg shadow-blue-200 hover:bg-blue-700
           hover:scale-[1.02] active:scale-[0.98]
           transition-all duration-300 text-base">
    Masuk
</button>
            </form>
        </div>
    </div>

    <script>
        const roleButtons = document.querySelectorAll('.role-button');
        const form = document.getElementById('loginForm');
        const roleInput = document.getElementById('role');

        const usernameField = document.getElementById('usernameField');
        const posyanduField = document.getElementById('posyanduField');
        const nikField = document.getElementById('nikField');
        const tanggalField = document.getElementById('tanggalField');
        const passwordField = document.getElementById('passwordField');

        const username = document.getElementById('username');
        const posyandu = document.getElementById('id_posyandu');
        const nik = document.getElementById('nik_anak');
        const tanggal = document.getElementById('tgl_lahir');
        const password = document.getElementById('password');

        function pilihRole(role) {
            roleInput.value = role;

            usernameField.classList.toggle('hidden', role !== 'Bidan');
            posyanduField.classList.toggle('hidden', role !== 'Posyandu');
            nikField.classList.toggle('hidden', role !== 'OrangTua');
            tanggalField.classList.toggle('hidden', role !== 'OrangTua');
            passwordField.classList.toggle('hidden', role === 'OrangTua');

            username.required = role === 'Bidan';
            posyandu.required = role === 'Posyandu';
            nik.required = role === 'OrangTua';
            tanggal.required = role === 'OrangTua';
            password.required = role !== 'OrangTua';

            if (role === 'Bidan') {
                form.action = "{{ url('/login/bidan') }}";
            } else if (role === 'Posyandu') {
                form.action = "{{ url('/login/posyandu') }}";
            } else {
                form.action = "{{ url('/login/orang-tua') }}";
            }

            roleButtons.forEach(button => {
    const aktif = button.dataset.role === role;

    button.classList.toggle('bg-white', aktif);
    button.classList.toggle('text-blue-600', aktif);
    button.classList.toggle('text-slate-500', !aktif);
    button.classList.toggle('shadow-sm', aktif);
});
        }

        roleButtons.forEach(button => {
            button.addEventListener('click', () => {
                pilihRole(button.dataset.role);
            });
        });

        pilihRole('Bidan');
    </script>
@endsection