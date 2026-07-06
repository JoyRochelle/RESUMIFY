<?php

return [

    'auth' => [

        'fields' => [
            'email' => 'EMAIL',
            'email_address' => 'ALAMAT EMAIL',
            'password' => 'PASSWORD',
            'confirm' => 'KONFIRMASI',
            'full_name' => 'NAMA LENGKAP',
        ],

        'processing' => 'Memproses...',
        'error_summary' => 'Mohon periksa kembali kolom yang ditandai.',
        'or_continue_with' => 'ATAU LANJUTKAN DENGAN',

        'tabs' => [
            'login' => 'Masuk',
            'sign_up' => 'Daftar',
        ],

        'legal' => [
            'terms' => 'SYARAT & KETENTUAN',
            'privacy' => 'KEBIJAKAN PRIVASI',
            'help' => 'PUSAT BANTUAN',
            'close' => 'Tutup',
        ],

        'marketing' => [
            'headline' => 'Satu Akun, Satu Karier yang Adaptif ✨',
            'subtitle' => 'Buat resume profesional dan tingkatkan skor ATS Anda dalam hitungan menit.',
            'ats_match' => 'KECOCOKAN ATS',
            'ai_optimization' => 'Optimasi AI Aktif',
        ],

        'login' => [
            'title' => 'Masuk | Resumify',
            'heading' => 'Masuk ke Akun Anda',
            'subtitle' => 'Selamat datang kembali di perjalanan karier Anda.',
            'forgot_password' => 'Lupa Password?',
            'remember_me' => 'Ingat saya selama 30 hari',
            'submit' => 'Masuk',
        ],

        'register' => [
            'title' => 'Daftar | Resumify',
            'heading' => 'Buat Akun Anda',
            'subtitle' => 'Mulai perjalanan profesional Anda dalam hitungan menit.',
            'terms_prefix' => 'Saya menyetujui',
            'terms_of_service' => 'Ketentuan Layanan',
            'and' => 'dan',
            'privacy_policy' => 'Kebijakan Privasi',
            'submit' => 'Daftar',
            'have_account' => 'Sudah punya akun?',
            'sign_in' => 'Masuk',
        ],

        'forgot_password' => [
            'title' => 'Lupa Password | Resumify',
            'heading' => 'Lupa Password',
            'subtitle' => 'Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.',
            'submit' => 'Kirim Tautan Reset Password',
            'back_to_login' => 'Kembali ke Halaman Masuk',
            'modal_heading' => 'Lupa Password?',
            'modal_subtitle' => 'Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.',
            'modal_placeholder' => 'Masukkan email Anda',
            'modal_submit' => 'Kirim Tautan',
        ],

        'reset_password' => [
            'title' => 'Atur Ulang Password | Resumify',
            'new_password' => 'Password Baru',
            'confirm_password' => 'Konfirmasi Password',
            'submit' => 'Perbarui Password',
        ],

        'verify_email' => [
            'title' => 'Verifikasi Email | Resumify',
            'heading' => 'Email Berhasil<br>Dikirim!',
            'body' => 'Kami telah mengirimkan tautan verifikasi ke alamat email Anda. Silakan periksa kotak masuk (dan folder spam) untuk melanjutkan.',
            'back_to_login' => 'Kembali ke Halaman Masuk',
            'no_email_question' => 'Belum menerima email?',
            'resend' => 'Kirim Ulang Tautan',
            'sending' => 'Mengirim...',
        ],

    ],

    'nav' => [
        'dashboard' => 'Dasbor',
        'manuscripts' => 'Manuskrip',
        'ats_analyzer' => 'Analisis ATS',
        'ats_short' => 'ATS',
        'interview' => 'Wawancara',
        'settings' => 'Pengaturan',
        'help' => 'Bantuan',
        'upgrade_quota' => 'Upgrade Kuota',
        'language' => 'Bahasa',
        'logout' => 'Keluar',
        'more' => 'Lainnya',
    ],

    'dashboard' => [
        'welcome' => 'Selamat datang,',
        'resume_quota' => ':used/:limit Resume Dibuat',
        'unlimited' => 'Tanpa Batas',
        'your_resumes' => 'Resume Anda',
        'untitled_resume' => 'Resume Tanpa Judul',
        'start_new_manuscript' => 'Buat Manuskrip Baru',
        'start_new_manuscript_desc' => 'Susun narasi karier profesional Anda dalam hitungan menit.',
        'resume_limit_reached' => 'Batas Resume Tercapai',
        'resume_limit_reached_desc' => 'Paket Basic mencakup 1 resume. Upgrade dari kartu paket di atas untuk resume tanpa batas.',
        'daily_tip_label' => 'TIPS HARIAN',
        'daily_tip' => 'Gunakan kata kerja aktif yang kuat untuk memberi bobot pada narasi profesional Anda.',
        'ats_insight_label' => 'Analisis ATS',
        'ats_insight_prefix' => 'Lihat seberapa cocok resume Anda dengan deskripsi pekerjaan menggunakan',
        'ats_insight_link' => 'Analisis ATS',
        'ats_insight_suffix' => 'kami — dapatkan skor kata kunci dan saran yang bisa langsung diterapkan dalam hitungan detik.',
        'delete_modal' => [
            'title' => 'Hapus Resume',
            'body' => 'Apakah Anda yakin ingin menghapus resume ini? Tindakan ini tidak dapat dibatalkan.',
            'cancel' => 'Batal',
            'confirm' => 'Ya, Hapus',
            'close_aria' => 'Tutup konfirmasi hapus',
        ],
        'rename_modal' => [
            'title' => 'Ganti Nama Resume',
            'label' => 'Judul Resume',
            'placeholder' => 'Masukkan judul resume...',
            'cancel' => 'Batal',
            'save' => 'Simpan',
            'close_aria' => 'Tutup dialog ganti nama',
        ],
    ],

];
