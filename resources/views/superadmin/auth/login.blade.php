@extends('layouts.auth')

@section('content')
<div class="login-container">
    <div class="brand-section">
        <h1 class="brand-name">SIPANDA</h1>
        <p class="brand-tagline">Sistem Posyandu Anak Digital</p>
    </div>

    <div class="login-card">
        <form action="{{ route('superadmin.login.post') }}" method="POST">
            @csrf
            
            <div class="input-group">
                <label>NAMA PENGGUNA</label>
                <div class="input-wrapper">
                    <input type="text" name="username" placeholder="Masukkan nama pengguna" required>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon-main"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
            </div>

            <div class="input-group">
                <label>KATA SANDI</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="passwordInput" placeholder="Masukkan kata sandi" required>
                    <div id="togglePassword" style="cursor: pointer; display: flex; align-items: center;">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path id="eyePath" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                            <circle id="eyeCircle" cx="12" cy="12" r="3"></circle>
                            <line id="eyeLine" x1="1" y1="1" x2="23" y2="23" style="display:none;"></line>
                        </svg>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#passwordInput');
    const eyeLine = document.querySelector('#eyeLine');
    const eyeCircle = document.querySelector('#eyeCircle');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        if (type === 'text') {
            eyeLine.style.display = 'block'; 
            eyeCircle.style.opacity = '0.5';
        } else {
            eyeLine.style.display = 'none'; 
            eyeCircle.style.opacity = '1';
        }
    });
</script>
@endsection