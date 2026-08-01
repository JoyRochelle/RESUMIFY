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

        'social' => [
            'in_development' => 'Dalam pengembangan',
            'linkedin_unavailable' => 'Masuk dengan LinkedIn masih dalam tahap pengembangan. Silakan lanjutkan dengan Google atau email.',
        ],

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
            'headline' => 'Satu Akun, Satu Karier yang Adaptif',
            'subtitle' => 'Buat resume profesional dan tingkatkan skor ATS Anda dalam hitungan menit.',
            'ats_match' => 'KECOCOKAN ATS',
            'ai_optimization' => 'Optimasi AI Aktif',
            'points' => [
                'ats_friendly' => 'Template ramah ATS',
                'bilingual' => 'Bahasa Indonesia & Inggris',
                'free' => 'Gratis untuk memulai',
            ],
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

        'legal_modals' => [
            'privacy' => [
                'title' => 'Kebijakan Privasi',
                'html' => '<p class="mb-4">Di Resumify, kami sangat serius menjaga privasi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi Anda saat mengunjungi situs web kami.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">1. Informasi yang Kami Kumpulkan</h4>
                    <p class="mb-4">Kami dapat mengumpulkan informasi identifikasi pribadi dari Pengguna dengan berbagai cara, termasuk namun tidak terbatas pada, saat Pengguna mengunjungi situs kami, mendaftar di situs, melakukan pemesanan, dan sehubungan dengan aktivitas, layanan, fitur, atau sumber daya lain yang kami sediakan di Situs kami.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">2. Bagaimana Kami Menggunakan Informasi yang Dikumpulkan</h4>
                    <p class="mb-4">Resumify dapat mengumpulkan dan menggunakan informasi pribadi Pengguna untuk tujuan berikut:</p>
                    <ul class="list-disc pl-5 mb-4 space-y-1">
                        <li>Untuk meningkatkan layanan pelanggan</li>
                        <li>Untuk mempersonalisasi pengalaman pengguna</li>
                        <li>Untuk memproses pembayaran secara aman</li>
                        <li>Untuk mengirimkan email berkala terkait pembaruan manuskrip Anda</li>
                    </ul>
                    <h4 class="font-bold text-primary mb-2 mt-6">3. Keamanan Data</h4>
                    <p>Kami menerapkan praktik pengumpulan, penyimpanan, dan pemrosesan data yang sesuai serta langkah-langkah keamanan untuk melindungi dari akses tidak sah, perubahan, pengungkapan, atau perusakan informasi pribadi, nama pengguna, kata sandi, informasi transaksi, dan data yang tersimpan di Situs kami.</p>',
            ],
            'terms' => [
                'title' => 'Syarat & Ketentuan',
                'html' => '<p class="mb-4">Selamat datang di Resumify. Dengan mengakses situs web ini, kami menganggap Anda menerima syarat dan ketentuan ini. Jangan lanjutkan menggunakan Resumify jika Anda tidak setuju dengan seluruh syarat dan ketentuan yang tercantum di halaman ini.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">1. Lisensi</h4>
                    <p class="mb-4">Kecuali dinyatakan lain, Resumify dan/atau pemberi lisensinya memiliki hak kekayaan intelektual atas seluruh materi di Resumify. Semua hak kekayaan intelektual dilindungi. Anda dapat mengakses ini dari Resumify untuk penggunaan pribadi Anda sendiri dengan tunduk pada batasan yang ditetapkan dalam syarat dan ketentuan ini.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">2. Akun Pengguna</h4>
                    <p class="mb-4">Saat Anda membuat akun bersama kami, Anda harus memberikan informasi yang akurat, lengkap, dan terkini setiap saat. Kegagalan melakukan hal ini merupakan pelanggaran terhadap Ketentuan, yang dapat mengakibatkan penghentian segera akun Anda pada Layanan kami.</p>
                    <h4 class="font-bold text-primary mb-2 mt-6">3. Batasan Tanggung Jawab</h4>
                    <p>Dalam keadaan apa pun, Resumify, maupun pejabat, direktur, dan karyawannya, tidak akan bertanggung jawab atas segala sesuatu yang timbul dari atau terkait dengan penggunaan Situs Web ini, baik tanggung jawab tersebut berdasarkan kontrak.</p>',
            ],
            'help' => [
                'title' => 'Pusat Bantuan',
                'html' => '<p class="mb-6 text-[15px]">Kami senang mendengar dari Anda. Jika Anda memiliki pertanyaan, kekhawatiran, atau masukan mengenai Resumify, silakan hubungi tim dukungan kami.</p>

                    <div class="bg-primary/5 p-4 rounded-xl mb-4 border border-primary/10">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-secondary">mail</span>
                            <span class="font-bold text-primary">Dukungan Email</span>
                        </div>
                        <p class="text-primary/70 ml-9">hello@resumify.com<br>support@resumify.com</p>
                    </div>

                    <div class="bg-primary/5 p-4 rounded-xl mb-6 border border-primary/10">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="material-symbols-outlined text-secondary">location_on</span>
                            <span class="font-bold text-primary">Kantor Pusat</span>
                        </div>
                        <p class="text-primary/70 ml-9">123 Innovation Drive<br>Tech District, San Francisco<br>CA 94105, Amerika Serikat</p>
                    </div>

                    <p class="text-sm italic text-primary/60">Tim dukungan kami biasanya merespons dalam 24-48 jam kerja.</p>',
            ],
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
        'toggle_sidebar' => 'Tampilkan atau sembunyikan navigasi',
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
        'onboarding' => [
            'title' => 'Mulai manuskrip pertamamu',
            'subtitle' => 'Susun resume, cek skor ATS-nya, dan latih wawancaramu — semua dalam satu tempat.',
            'steps_label' => 'Cara kerja Resumify',
            'step1' => 'Susun resume',
            'step2' => 'Cek skor ATS',
            'step3' => 'Latihan interview',
        ],
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

    'common' => [
        'fix_following_fields' => 'Mohon perbaiki kolom berikut:',
        'footer_copyright' => '© 2026 Resumify - Curated with Integrity',
    ],

    'manuscripts_page' => [
        'title' => 'Manuskrip Anda',
        'subtitle' => 'Kelola resume yang sudah ada atau buat resume baru yang disesuaikan dengan pekerjaan target Anda.',
    ],

    'plan_badge' => [
        'premium' => 'Anggota Premium',
        'basic' => 'Anggota Basic',
    ],

    'quota_status' => [
        'upgrade' => 'Upgrade',
        'ai_credits' => 'Kredit AI',
        'used_suffix' => 'terpakai',
        'resumes' => 'Resume',
        'created_suffix' => 'dibuat',
    ],

    'btn_create' => [
        'label' => 'Buat Resume Baru',
        'unlimited_title' => 'Resume tanpa batas',
        'unlimited_desc' => 'Paket Basic mencakup 1 resume. Premium membuka resume tanpa batas untuk setiap peran yang Anda targetkan.',
    ],

    'resume_card' => [
        'edit_manuscript' => 'Ubah Manuskrip',
        'last_edited' => 'Terakhir diubah: :date',
        'edit' => 'Ubah',
        'edit_aria' => 'Ubah :title',
        'rename' => 'Ganti Nama Resume',
        'rename_aria' => 'Ganti nama :title',
        'duplicate' => 'Duplikat Resume',
        'duplicate_aria' => 'Duplikat :title',
        'delete' => 'Hapus Resume',
        'delete_aria' => 'Hapus :title',
    ],

    'resume' => [
        'index' => [
            'title' => 'Resume Saya',
            'new_resume' => 'Resume Baru',
            'updated' => 'Diperbarui',
            'no_template' => 'Tanpa template',
            'empty' => [
                'title' => 'Buat resume pertama Anda',
                'description' => 'Mulai dengan sebuah template, lalu sesuaikan kontennya dengan peran yang Anda inginkan.',
                'action' => 'Buat Resume',
            ],
            'view' => 'Lihat',
            'edit' => 'Ubah',
            'duplicate' => 'Duplikat',
            'duplicating' => 'Menduplikasi...',
            'delete' => 'Hapus',
            'cancel' => 'Batal',
            'confirm' => 'Konfirmasi',
            'deleting' => 'Menghapus...',
        ],
        'create' => [
            'title' => 'Buat Resume',
            'resume_title_label' => 'Judul Resume',
            'title_placeholder' => 'cth. Senior Product Designer',
            'title_hint' => 'Gunakan judul yang menunjukkan peran yang dituju resume ini.',
            'choose_template' => 'Pilih Template',
            'select_template' => 'Pilih sebuah template',
            'template_hint' => 'Anda dapat mengganti template nanti dari editor manuskrip.',
            'premium_suffix' => '(Premium)',
            'cancel' => 'Batal',
            'creating' => 'Membuat...',
            'submit' => 'Buat Resume',
        ],
        'edit' => [
            'title' => 'Ubah Resume',
            'details_heading' => 'Detail Resume',
            'title_label' => 'Judul',
            'make_public' => 'Jadikan Publik',
            'make_public_desc' => 'Izinkan resume ini tersedia melalui tautan pratinjau publik.',
            'saving' => 'Menyimpan...',
            'save_changes' => 'Simpan Perubahan',
            'sections_heading' => 'Bagian',
            'section_title_label' => 'Judul Bagian',
            'content_json_label' => 'Konten JSON',
            'updating' => 'Memperbarui...',
            'update_section' => 'Perbarui Bagian',
        ],
        'show' => [
            'edit' => 'Ubah',
            'template' => 'Template',
            'visibility' => 'Visibilitas',
            'public' => 'Publik',
            'private' => 'Privat',
            'updated' => 'Diperbarui',
            'duplicate' => 'Duplikat',
            'duplicating' => 'Menduplikasi...',
            'delete' => 'Hapus',
            'cancel' => 'Batal',
            'confirm' => 'Konfirmasi',
            'deleting' => 'Menghapus...',
            'no_content' => 'Belum ada konten.',
            'empty_title' => 'Belum ada bagian',
            'empty_description' => 'Buka editor untuk menambahkan bagian utama resume ini.',
            'open_editor' => 'Buka Editor',
        ],
    ],

    'editor' => [
        'title_prefix' => 'Editor: ',
        'tailor_cv' => 'Tailor',
        'preview' => 'Pratinjau',
        'download_pdf' => 'Unduh PDF',
        'premium_pdf_title' => 'Ekspor PDF premium',
        'premium_pdf_desc' => 'Ekspor PDF berkualitas tinggi untuk lamaran kerja dan berbagi dengan perekrut.',
        'tab_edit' => 'Ubah',
        'tab_preview' => 'Pratinjau',
        'add_optional_section' => 'Tambah Bagian Opsional',
        'add_certifications' => '+ Sertifikasi',
        'add_projects' => '+ Proyek',
        'add_languages' => '+ Bahasa',
        'ats_score' => 'Skor ATS',
        'minimize' => 'Kecilkan',
        'maximize' => 'Besarkan',
        'layout' => 'Tata Letak',
        'history' => 'Riwayat',
        'select_template_title' => 'Pilih Template',
        'close_template_selection' => 'Tutup pemilihan template',
        'premium_badge' => 'Premium',
        'locked_premium_template' => 'Template Premium Terkunci',
        'upgrade_to_apply_layout' => 'Upgrade untuk menerapkan tata letak ini pada resume Anda.',
        'upgrade_to_unlock' => 'Upgrade untuk Membuka',
        'use_template' => 'Gunakan Template',
        'use_x_template_aria' => 'Gunakan template :name',
        'refine_with_ai' => 'Perhalus dengan AI',
        'close_ai_refinement' => 'Tutup penghalusan AI',
        'generating_bullet_points' => 'Menghasilkan poin-poin yang dioptimalkan...',
        'tailored_cv_versions' => 'Versi CV yang Disesuaikan',
        'close_tailored_versions' => 'Tutup versi CV yang disesuaikan',
        'generate_tailored_versions' => 'Buat Versi yang Disesuaikan',
        'generate_versions_desc' => 'Kami akan membuat 3 versi CV berbeda yang disesuaikan dengan pekerjaan target Anda: sudut pandang Kepemimpinan, Teknis, dan Kepemilikan. Ini menggunakan 3 kredit AI.',
        'generate_versions_button' => 'Buat Versi',
        'crafting_versions' => 'Menyusun versi CV yang disesuaikan...',
        'crafting_versions_desc' => 'Biasanya membutuhkan waktu sekitar 10-20 detik.',
        'cv_history' => 'Riwayat CV',
        'close_cv_history' => 'Tutup riwayat CV',
        'loading_ellipsis' => 'Memuat…',
        'apply_version_title' => 'Terapkan versi ini?',
        'apply_version_desc' => 'Ini akan menimpa konten CV Anda saat ini. Cadangan konten Anda saat ini tersimpan di Riwayat, sehingga Anda bisa memulihkannya nanti.',
        'cancel' => 'Batal',
        'apply_and_overwrite' => 'Terapkan & Timpa',

        'js' => [
            'photo_too_large' => 'Ukuran foto maksimal 2MB. Silakan pilih file yang lebih kecil.',
            'generating' => 'Membuat...',
            'premium_required_pdf' => 'Premium diperlukan untuk ekspor PDF.',
            'download_pdf_failed' => 'Gagal mengunduh PDF. Silakan coba lagi.',
            'premium_required_template' => 'Premium diperlukan untuk template ini.',
            'change_template_failed' => 'Gagal mengganti template.',
            'no_resume_for_template' => 'Tidak ada resume yang tersedia untuk memperbarui template.',
            'saving' => 'Menyimpan…',
            'saved_at_prefix' => 'Tersimpan',
            'changes_saved' => 'Perubahan disimpan!',
            'fill_previous_entry' => 'Isi dulu entri sebelumnya sebelum menambahkan yang baru.',
            'save_failed' => 'Gagal menyimpan — silakan coba lagi.',
            'network_error_not_saved' => 'Kesalahan jaringan — perubahan tidak disimpan.',
            'confirm_remove_section' => 'Apakah Anda yakin ingin menghapus bagian ini sepenuhnya?',
            'delete_section_failed' => 'Gagal menghapus bagian.',
            'network_error' => 'Kesalahan jaringan.',
            'no_target_job' => 'Belum Ada Target Pekerjaan',
            'keyword_match' => 'Kecocokan Kata Kunci',
            'refine_min_length' => 'Silakan tulis beberapa kata sebelum menyempurnakan.',
            'refine_failed' => 'Gagal menyempurnakan poin.',
            'connection_error' => 'Terjadi kesalahan saat menghubungkan ke server.',
            'job_description_min_length' => 'Silakan isi Deskripsi Target Pekerjaan secara rinci (minimal 50 karakter) di bagian Target Pekerjaan terlebih dahulu.',
            'version_warning_prefix' => 'Harap periksa versi ini — beberapa detail tidak ditemukan di CV asli Anda: ',
            'angle_labels' => [
                'leadership' => 'Kepemimpinan',
                'technical' => 'Teknis',
                'ownership' => 'Kepemilikan',
            ],
            'angle_suffix' => '',
            'version_emphasizes' => 'Versi ini menekankan aspek :angle dari pengalaman Anda, disesuaikan khusus untuk deskripsi pekerjaan yang diberikan.',
            'apply_this_version' => 'Terapkan versi ini',
            'preview_button' => 'Pratinjau',
            'download_pdf_button' => 'Unduh PDF',
            'generate_versions_failed' => 'Gagal membuat versi.',
            'apply_version_warning_prefix' => 'Perhatian — beberapa detail tidak ditemukan di CV asli Anda: ',
            'apply_version_warning_suffix' => 'Tinjau sebelum menerapkan.',
            'version_applied' => 'Versi diterapkan! Memuat ulang...',
            'apply_version_failed' => 'Gagal menerapkan versi.',
            'version_not_applied' => 'Kesalahan jaringan — versi tidak diterapkan.',
            'history_reason_chameleon_apply' => 'Sebelum menerapkan versi yang disesuaikan',
            'history_reason_pre_restore' => 'Sebelum memulihkan versi sebelumnya',
            'history_reason_snapshot' => 'Snapshot',
            'loading' => 'Memuat…',
            'no_history' => 'Belum ada riwayat.',
            'restore' => 'Pulihkan',
            'history_load_failed' => 'Gagal memuat riwayat.',
            'confirm_restore' => 'Pulihkan versi ini? Konten Anda saat ini akan disimpan sebagai snapshot terlebih dahulu agar Anda juga bisa membatalkannya.',
            'restored' => 'Dipulihkan! Memuat ulang...',
            'restore_failed' => 'Gagal memulihkan.',
            'restore_network_error' => 'Kesalahan jaringan — pemulihan gagal.',
        ],

        'placeholder' => [
            'role_location' => 'Senior Product Designer • San Francisco, CA',
            'professional_summary_heading' => 'Ringkasan Profesional',
            'summary_body' => 'Product Designer berprestasi dengan pengalaman 8+ tahun menciptakan pengalaman digital yang intuitif untuk perusahaan teknologi yang berkembang pesat. Ahli dalam pemikiran sistem, desain yang mengutamakan aksesibilitas, dan menjembatani tim teknik serta desain.',
            'experience_heading' => 'Pengalaman',
            'job_title_1' => 'Senior Product Designer',
            'job_desc_1' => 'Memimpin sistem desain untuk tim perangkat lunak paling produktif di dunia. Menyusun komponen berfidelitas tinggi dan menjaga konsistensi visual di platform mobile dan desktop.',
            'job_title_2' => 'Product Designer',
            'job_desc_2' => 'Berfokus pada pengalaman pemesanan tamu dan internasionalisasi sistem desain. Mengurangi hambatan checkout sebesar 12% melalui pengujian iteratif dan pola UI yang aksesibel.',
            'education_heading' => 'Pendidikan',
            'degree' => 'BFA in Interaction Design',
            'expertise_heading' => 'Keahlian',
            'tag_design_systems' => 'Sistem Desain',
            'tag_accessibility' => 'Aksesibilitas',
            'tag_prototyping' => 'Prototyping',
            'page_of' => ':current dari :total',
        ],

        'sections' => [
            'target_job' => [
                'title' => 'Pekerjaan Target',
                'job_title' => 'Jabatan Target',
                'job_title_placeholder' => 'cth. Senior Software Engineer',
                'job_company' => 'Perusahaan Target',
                'job_company_placeholder' => 'cth. Acme Corp',
                'job_description' => 'Deskripsi Pekerjaan',
                'job_description_placeholder' => 'Tempel deskripsi pekerjaan di sini untuk melihat seberapa cocok resume Anda...',
            ],
            'personal_info' => [
                'title' => 'Info Pribadi',
                'full_name' => 'Nama Lengkap',
                'full_name_placeholder' => 'cth. John Doe',
                'professional_title' => 'Jabatan Profesional',
                'professional_title_placeholder' => 'cth. Senior Product Designer',
                'email' => 'Email',
                'phone_number' => 'Nomor Telepon',
                'phone_placeholder' => '812 xxxx xxxx',
                'location' => 'Lokasi',
                'professional_summary' => 'Ringkasan Profesional',
                'summary_placeholder' => 'Tulis 2–4 kalimat tentang latar belakang, keahlian utama, dan tujuan karier Anda...',
                'refine' => 'Perhalus',
                'profile_photo' => 'Foto Profil',
                'change_photo' => 'Ganti Foto',
                'upload_photo' => 'Unggah Foto',
                'remove_photo' => 'Hapus foto',
                'photo_hint' => 'JPG, PNG, WebP · Maks 2MB',
            ],
            'work_experience' => [
                'title' => 'Pengalaman Kerja',
                'job_title' => 'Jabatan',
                'job_title_placeholder' => 'cth. Software Engineer',
                'company' => 'Perusahaan',
                'company_placeholder' => 'cth. Acme Corp',
                'start_date' => 'Tanggal Mulai',
                'end_date' => 'Tanggal Selesai',
                'end_date_hint' => 'Kosongkan jika masih berlangsung',
                'description' => 'Deskripsi',
                'description_placeholder' => 'Jelaskan tanggung jawab dan pencapaian utama Anda...',
                'refine' => 'Perhalus',
                'add' => 'Tambah Pengalaman',
            ],
            'education' => [
                'title' => 'Pendidikan',
                'degree' => 'Gelar/Program',
                'degree_placeholder' => 'cth. Sarjana Sains',
                'school' => 'Sekolah/Universitas',
                'school_placeholder' => 'cth. Universitas Indonesia',
                'start_date' => 'Tanggal Mulai',
                'end_date' => 'Tanggal Selesai',
                'end_date_hint' => 'Kosongkan jika masih berlangsung',
                'additional_info' => 'Info Tambahan',
                'add' => 'Tambah Pendidikan',
            ],
            'skills' => [
                'title' => 'Keahlian',
                'skill_name' => 'Nama Keahlian',
                'proficiency_level' => 'Tingkat Kemahiran',
                'select_level' => 'Pilih tingkat',
                'add' => 'Tambah Keahlian',
                'levels' => [
                    'Beginner' => 'Pemula',
                    'Elementary' => 'Dasar',
                    'Intermediate' => 'Menengah',
                    'Advanced' => 'Mahir',
                    'Expert' => 'Ahli',
                ],
            ],
            'certifications' => [
                'title' => 'Sertifikasi',
                'name' => 'Nama Sertifikasi',
                'issuer' => 'Penerbit',
                'date' => 'Tanggal',
                'add' => 'Tambah Sertifikasi',
            ],
            'projects' => [
                'title' => 'Proyek',
                'name' => 'Nama Proyek',
                'url' => 'URL Proyek (Opsional)',
                'description' => 'Deskripsi',
                'refine' => 'Perhalus',
                'add' => 'Tambah Proyek',
            ],
            'languages' => [
                'title' => 'Bahasa',
                'language' => 'Bahasa',
                'proficiency' => 'Kemahiran',
                'select_level' => 'Pilih tingkat',
                'add' => 'Tambah Bahasa',
                'levels' => [
                    'Beginner' => 'Pemula',
                    'Conversational' => 'Percakapan Sehari-hari',
                    'Fluent' => 'Lancar',
                    'Native' => 'Bahasa Ibu',
                ],
            ],
        ],
    ],

    'ats' => [
        'analyze_match' => 'Analisis Kecocokan',
        'premium_badge' => 'Premium',
        'page_title' => 'ATS Analyzer',
        'how_it_works' => 'Cara Kerja',
        'trial' => [
            'banner' => 'Uji coba gratis: sisa :count analisis dari :limit.|Uji coba gratis: sisa :count analisis dari :limit.',
            'exhausted_message' => 'Anda telah menggunakan seluruh :limit analisis ATS gratis. Upgrade ke Premium untuk pemindaian tanpa batas.',
        ],
        'sections_aria_label' => 'Bagian ATS Analyzer',
        'tabs' => [
            'setup' => 'Setup',
            'results' => 'Hasil',
        ],
        'instructions_modal' => [
            'title' => 'Cara Kerja Penilaian ATS',
            'close_aria' => 'Tutup petunjuk penilaian ATS',
            'intro' => 'Analisis ATS kami meniru cara Applicant Tracking Systems mengevaluasi resume Anda terhadap deskripsi pekerjaan.',
            'keyword_match_label' => 'Kecocokan Kata Kunci (65%)',
            'keyword_match_desc' => '— Kami mengekstrak istilah kata tunggal dan multi-kata penting dari deskripsi pekerjaan dan memeriksa berapa banyak yang muncul di resume Anda.',
            'action_verbs_label' => 'Kata Kerja Aksi (15%)',
            'action_verbs_desc' => '— Kata kerja yang kuat dan berdampak menandakan kandidat yang berorientasi pada pencapaian kepada parser ATS.',
            'quantification_label' => 'Kuantifikasi (12%)',
            'quantification_desc' => '— Angka dan persentase secara signifikan meningkatkan skor relevansi di sebagian besar sistem ATS.',
            'length_format_label' => 'Panjang &amp; Format (8%)',
            'length_format_desc' => '— Resume antara 200–800 kata paling andal diproses oleh sistem otomatis.',
            'tip' => 'Tip: Semakin dekat bahasa resume Anda mencerminkan deskripsi pekerjaan, semakin tinggi skor kecocokan Anda.',
        ],
        'setup' => [
            'select_from_resumes' => 'Pilih dari Resume Anda',
            'choose_resume_placeholder' => '-- Pilih Resume --',
            'autofill_hint' => 'Terisi otomatis dari pekerjaan target resume Anda. Anda dapat mengubahnya sebelum menganalisis.',
            'analyzer_title' => 'Analisis ATS',
            'analyzer_desc' => 'Premium membuka pencocokan resume-ke-pekerjaan penuh, kata kunci yang hilang, dan panduan perbaikan berprioritas.',
            'scan_history' => 'Riwayat Pemindaian',
            'recent_scans_count' => ':count pemindaian terbaru|:count pemindaian terbaru',
            'untitled_scan' => 'Pemindaian Tanpa Judul',
            'no_resume_linked' => 'Tidak ada resume yang terhubung',
            'delete' => 'Hapus',
            'delete_scan_aria' => 'Hapus pemindaian :title',
            'load_scan_aria' => 'Muat pemindaian :title',
            'no_history_yet' => 'Belum ada riwayat pemindaian — jalankan analisis pertama Anda di atas.',
        ],
        'results' => [
            'select_analyze_heading' => 'Pilih & Analisis',
            'select_analyze_before' => 'Pilih resume di sebelah kiri, lalu klik',
            'select_analyze_after' => 'untuk melihat skor ATS dan rekomendasi yang bisa ditindaklanjuti.',
            'resume_preview' => 'Pratinjau Resume',
            'click_to_score' => 'Klik :action untuk mendapatkan skor',
            'missing_keywords' => 'Kata Kunci yang Hilang',
            'matched_keywords' => 'Kata Kunci yang Cocok',
            'action_verbs_detected' => 'Kata Kerja Aktif Terdeteksi',
            'consider_adding' => 'Pertimbangkan Menambahkan',
            'section_breakdown' => 'Rincian Bagian',
            'strategic_insights' => 'Wawasan Strategis',
            'reanalyze_before' => 'Perbarui teks Anda dan klik',
            'reanalyze_after' => 'lagi untuk melihat skor baru Anda.',
        ],
    ],

    'interview' => [
        'index' => [
            'page_title' => 'Wawancara Simulasi',
            'heading' => 'Wawancara Simulasi HRD',
            'greeting' => 'Halo! Saya Ms. Sarah',
            'intro' => 'Saya akan mewawancarai Anda berdasarkan konten CV asli Anda — bukan pertanyaan umum. Pilih CV dan posisi yang ingin Anda latih.',
            'trial_used_title' => 'Uji Coba Gratis Anda Telah Habis',
            'trial_used_body' => "Anda telah menggunakan seluruh :limit sesi wawancara gratis.<br>\nUpgrade ke Premium untuk sesi tanpa batas.",
            'trial_exhausted_message' => 'Anda telah menggunakan seluruh :limit sesi wawancara gratis. Upgrade ke Premium untuk sesi tanpa batas.',
            'upgrade_to_premium' => 'Upgrade ke Premium',
            'view_last_session' => 'Lihat Sesi Terakhir',
            'ai_credits_remaining' => 'Sisa Kredit AI',
            'select_cv' => 'Pilih CV',
            'no_cv_yet' => 'Anda belum memiliki CV.',
            'create_cv_first' => 'Buat CV terlebih dahulu',
            'position_applied_for' => 'Posisi yang Dilamar',
            'position_placeholder' => 'cth. Backend Engineer, Product Manager…',
            'trial_banner_before' => 'Anda punya sisa',
            'trial_banner_bold' => ':count sesi uji coba gratis|:count sesi uji coba gratis',
            'trial_banner_after' => 'dari total :limit sebagai pengguna Basic.',
            'start_interview' => 'Mulai Wawancara',
            'starting' => 'Memulai…',
            'select_cv_first_error' => 'Silakan pilih CV terlebih dahulu.',
            'enter_position_error' => 'Silakan masukkan posisi yang Anda lamar.',
            'start_failed_error' => 'Gagal memulai sesi. Silakan coba lagi.',
            'timeout_error' => 'Permintaan waktu habis. Silakan coba lagi.',
            'network_error' => 'Terjadi kesalahan jaringan. Silakan coba lagi.',
            'recent_interviews' => 'Wawancara Terbaru',
            'view_full_history' => 'Lihat Riwayat Lengkap',
            'cv_prefix' => 'CV:',
            'deleted_cv' => 'CV Terhapus',
            'score_label' => 'Skor: :score/100',
            'in_progress' => 'Sedang Berlangsung',
            'ended' => 'Selesai',
        ],
        'session' => [
            'page_title' => 'Wawancara dengan Ms. Sarah',
            'header_name' => 'Ms. Sarah · Pewawancara HRD',
            'end_session' => 'Akhiri Sesi',
            'session_ended_badge' => 'Sesi Berakhir',
            'ended_on' => 'Sesi ini berakhir pada :date.',
            'view_report' => 'Lihat Laporan',
            'generate_report' => 'Buat Laporan (1 kredit)',
            'input_placeholder' => 'Ketik jawaban Anda… (Enter untuk kirim, Shift+Enter untuk baris baru)',
            'end_modal_title' => 'Akhiri Sesi?',
            'end_modal_subtitle' => 'Sesi yang telah diakhiri tidak dapat dilanjutkan.',
            'end_modal_body' => 'Apakah Anda yakin ingin mengakhiri sesi wawancara ini sekarang?',
            'cancel' => 'Batal',
            'error_occurred' => 'Terjadi kesalahan. Silakan coba lagi.',
            'timeout_error' => 'Permintaan waktu habis. Silakan coba lagi.',
            'connection_lost' => 'Koneksi terputus. Periksa jaringan Anda dan coba lagi.',
        ],
        'feedback' => [
            'page_title' => 'Laporan Wawancara',
            'heading' => 'Laporan Wawancara',
            'badge_ready' => 'Siap Bekerja',
            'badge_almost_ready' => 'Hampir Siap',
            'badge_needs_practice' => 'Butuh Latihan Lagi',
            'position_label' => 'Posisi: :job',
            'overall_score' => 'Skor Keseluruhan',
            'score_excellent' => 'Performa wawancara Anda sangat baik. Pertahankan!',
            'score_good' => 'Performa Anda baik dengan beberapa area yang bisa ditingkatkan.',
            'score_needs_practice' => 'Perlu latihan lebih lanjut. Tinjau umpan balik di bawah ini dengan saksama.',
            'per_question_breakdown' => 'Rincian Per Pertanyaan',
            'star_situation' => 'Situasi',
            'star_task' => 'Tugas',
            'star_action' => 'Tindakan',
            'star_result' => 'Hasil',
            'start_new_interview' => 'Mulai Wawancara Baru',
            'view_conversation' => 'Lihat Percakapan',
            'want_more_practice' => 'Ingin latihan lebih banyak?',
            'upgrade_unlimited_desc' => 'Upgrade ke Premium untuk sesi wawancara tanpa batas + 50 kredit AI/bulan.',
        ],
        'history' => [
            'page_title' => 'Riwayat Wawancara',
            'heading' => 'Riwayat Wawancara',
            'all_resumes' => 'Semua Resume',
            'sort_label' => 'Urutkan:',
            'sort_date' => 'Tanggal',
            'sort_score' => 'Skor',
            'reset' => 'Reset',
            'no_sessions_yet' => 'Belum ada sesi wawancara',
            'complete_first_interview' => 'Selesaikan wawancara simulasi pertama Anda untuk melihat riwayat di sini.',
            'start_first_interview' => 'Mulai Wawancara Pertama Anda',
        ],
    ],

    'tour' => [
        'ui' => [
            'start' => 'Tutorial',
            'start_aria' => 'Mulai tutorial untuk halaman ini',
            'next' => 'Lanjut',
            'back' => 'Kembali',
            'skip' => 'Lewati tutorial',
            'done' => 'Selesai',
            'close' => 'Tutup tutorial',
            'step_label' => 'Langkah :current dari :total',
        ],

        'dashboard' => [
            'steps' => [
                'welcome' => [
                    'title' => 'Selamat datang di Resumify',
                    'body' => 'Tutorial singkat ini menunjuk langsung ke tombol asli di tiap halaman. Pakai Lanjut dan Kembali untuk berpindah, atau Lewati untuk keluar — Anda bisa mengulangnya kapan saja lewat tombol Tutorial.',
                ],
                'quota' => [
                    'title' => 'Paket dan kredit AI Anda',
                    'body' => 'Setiap aksi AI memakai kredit: 1 untuk memperhalus satu poin atau menjalankan pemindaian ATS, 3 untuk membuat versi CV yang disesuaikan. Pantau sisanya di sini.',
                ],
                'create' => [
                    'title' => 'Mulai resume baru di sini',
                    'body' => 'Klik ini untuk memberi nama resume dan memilih template. Akun Basic mendapat satu resume dan template gratis; Premium membuka resume tanpa batas dan seluruh desain.',
                ],
                'resumes' => [
                    'title' => 'Resume Anda tersimpan di sini',
                    'body' => 'Buka salah satunya untuk melanjutkan penyuntingan. Tiap kartu juga bisa dipakai untuk mengganti nama, menduplikasi, atau menghapus resume.',
                ],
                'ats_nav' => [
                    'title' => 'Analisis ATS',
                    'body' => 'Setelah resume terisi, buka menu ini untuk menilainya terhadap iklan lowongan asli dan melihat kata kunci apa saja yang belum ada.',
                ],
                'interview_nav' => [
                    'title' => 'Wawancara Simulasi',
                    'body' => 'Berlatih dengan pewawancara AI yang bertanya berdasarkan isi CV Anda sendiri, lalu baca laporan penilaiannya.',
                ],
            ],
        ],

        'manuscripts' => [
            'steps' => [
                'intro' => [
                    'title' => 'Manuskrip Anda',
                    'body' => 'Semua resume yang Anda buat ada di halaman ini. Buka salah satunya untuk menyunting, atau buat baru untuk pekerjaan target yang berbeda.',
                ],
                'quota' => [
                    'title' => 'Paket dan kredit',
                    'body' => 'Batas resume dan sisa kredit AI ditampilkan di sini. Basic mendapat satu resume; Premium tanpa batas.',
                ],
                'list' => [
                    'title' => 'Buka sebuah resume',
                    'body' => 'Klik sebuah kartu untuk masuk ke editor, tempat Anda mengisi tiap bagian sambil melihat pratinjaunya langsung di sebelahnya.',
                ],
                'create' => [
                    'title' => 'Buat resume baru',
                    'body' => 'Gunakan kartu ini untuk memulai dari awal: beri judul resume, pilih template, lalu editor terbuka dengan bagian-bagian yang siap diisi.',
                ],
            ],
        ],

        'editor' => [
            'steps' => [
                'intro' => [
                    'title' => 'Ini editor resume',
                    'body' => 'Sisi kiri berisi konten Anda, bagian demi bagian, dan sisi kanan menampilkan pratinjau langsung. Tiap bagian tersimpan sendiri sambil Anda mengetik.',
                ],
                'target_job' => [
                    'title' => '1. Target Job',
                    'body' => 'Isi ini lebih dulu. Masukkan judul posisi, perusahaan, dan tempelkan deskripsi pekerjaan lengkap dari iklan lowongan. Bagian inilah yang dibaca AI untuk menilai resume, menyesuaikannya, dan menyiapkan wawancara simulasi.',
                ],
                'personal_info' => [
                    'title' => '2. Informasi Pribadi',
                    'body' => 'Nama lengkap, gelar profesional, email, telepon, lokasi, dan ringkasan 2-4 kalimat. Tulis ringkasan mengarah ke posisi yang Anda inginkan, bukan posisi sekarang. Foto bersifat opsional (JPG, PNG, atau WebP, maksimal 2MB).',
                ],
                'work_experience' => [
                    'title' => '3. Pengalaman Kerja',
                    'body' => 'Jabatan, perusahaan, dan tanggal — kosongkan tanggal selesai bila Anda masih bekerja di sana. Tulis pencapaian, bukan daftar tugas, dan sertakan angka sejauh Anda bisa jujur: "Menangani 120 tiket per minggu dengan tingkat penyelesaian 94%" jauh lebih kuat daripada "Bertanggung jawab atas dukungan pelanggan".',
                ],
                'education' => [
                    'title' => '4. Pendidikan',
                    'body' => 'Gelar atau program, sekolah, dan tanggal. Pakai Informasi Tambahan untuk IPK, skripsi, atau penghargaan — buat ringkas begitu Anda punya pengalaman kerja.',
                ],
                'skills' => [
                    'title' => '5. Keahlian',
                    'body' => 'Tambahkan tiap keahlian beserta tingkat penguasaannya. Di sinilah kata kunci yang disebut hilang oleh Analisis ATS paling mudah diperbaiki — tapi cantumkan hanya yang benar-benar Anda kuasai, karena wawancara simulasi akan menanyakannya.',
                ],
                'optional' => [
                    'title' => 'Bagian opsional',
                    'body' => 'Tambahkan Sertifikasi, Proyek, atau Bahasa bila memperkuat posisi Anda. Proyek adalah bagian terkuat bagi fresh graduate dan yang sedang beralih karier.',
                ],
                'refine' => [
                    'title' => 'Perhalus kalimat dengan AI',
                    'body' => 'Refine menulis ulang ringkasan, pengalaman, atau deskripsi proyek Anda menjadi kalimat yang lebih tajam dan berorientasi pencapaian dengan biaya 1 kredit. AI hanya mengolah apa yang Anda tulis — tidak pernah mengarang perusahaan, tanggal, atau angka.',
                ],
                'ats_widget' => [
                    'title' => 'Skor ATS langsung',
                    'body' => 'Ini gambaran cepat seberapa cocok resume Anda dengan deskripsi pekerjaan di bagian Target Job. Untuk rincian lengkap beserta kata kunci yang hilang, buka halaman Analisis ATS.',
                ],
                'tailor' => [
                    'title' => 'Tailor CV',
                    'body' => 'Fitur ini membuat tiga versi utuh resume Anda yang diarahkan ke pekerjaan target, dengan biaya 3 kredit. Bandingkan ketiganya, lalu Apply and Overwrite versi yang paling pas. Isi bagian Target Job lebih dulu, kalau tidak AI tidak punya sasaran.',
                ],
                'toolbar' => [
                    'title' => 'Layout dan History',
                    'body' => 'Layout mengganti template tanpa menyentuh isi konten. History mengembalikan versi sebelumnya dari sebuah bagian — sangat berguna tepat setelah menerapkan versi hasil penyesuaian.',
                ],
                'export' => [
                    'title' => 'Pratinjau dan ekspor',
                    'body' => 'Preview membuka resume utuh di tab baru. Download PDF memberi Anda berkas untuk dikirim bersama lamaran; ekspor PDF adalah fitur Premium.',
                ],
            ],
        ],

        'ats' => [
            'steps' => [
                'intro' => [
                    'title' => 'Apa yang dilakukan Analisis ATS',
                    'body' => 'Sebagian besar lamaran dibaca sistem ATS sebelum sampai ke manusia. Halaman ini menilai resume Anda terhadap deskripsi pekerjaan dengan cara yang sama dan menunjukkan apa yang kurang.',
                ],
                'trial' => [
                    'title' => 'Uji coba gratis Anda',
                    'body' => 'Akun Basic mendapat 3 analisis gratis. Banner ini menghitung mundur sisanya; setelah habis, tombolnya berubah menjadi ajakan upgrade. Premium tanpa batas.',
                ],
                'select_cv' => [
                    'title' => '1. Pilih resume',
                    'body' => 'Tentukan resume mana yang ingin dinilai. Judul posisi dan deskripsi pekerjaan di bawah akan terisi otomatis dari bagian Target Job resume tersebut.',
                ],
                'job' => [
                    'title' => '2. Tempelkan iklan lowongannya',
                    'body' => 'Sunting judul posisi dan tempelkan deskripsi pekerjaan yang benar-benar Anda lamar. Makin mirip dengan iklan aslinya, makin berguna skornya.',
                ],
                'analyze' => [
                    'title' => '3. Jalankan analisisnya',
                    'body' => 'Setiap analisis memakai 1 kredit dan dibatasi 5 kali per menit. Akun Basic punya total 3 analisis gratis; sisanya ditampilkan pada banner di atas tombol.',
                ],
                'results' => [
                    'title' => '4. Baca hasilnya',
                    'body' => 'Anda mendapat skor kecocokan plus kata kunci yang hilang, kata kunci yang cocok, kata kerja aksi, rincian per bagian, dan catatan strategis. Bobot skornya: kata kunci 65%, kata kerja aksi 15%, kuantifikasi 12%, serta panjang dan format 8%.',
                ],
                'history' => [
                    'title' => 'Riwayat pemindaian',
                    'body' => 'Setiap analisis tersimpan di sini. Perbaiki resume di editor, jalankan pemindaian lagi, lalu bandingkan keduanya untuk melihat apakah perubahannya berhasil.',
                ],
                'how' => [
                    'title' => 'Penjelasan lengkap kapan saja',
                    'body' => 'How It Works menjelaskan seluruh bobot penilaian. Buka kapan pun ada hasil yang membuat Anda bingung.',
                ],
            ],
        ],

        'interview' => [
            'steps' => [
                'intro' => [
                    'title' => 'Kenalan dengan pewawancara Anda',
                    'body' => 'Ms. Sarah berperan sebagai pewawancara HRD yang sudah membaca CV Anda. Pertanyaannya berangkat dari apa yang benar-benar Anda tulis, bukan daftar pertanyaan umum.',
                ],
                'quota' => [
                    'title' => 'Biaya satu sesi',
                    'body' => 'Memulai sesi memakai 1 kredit dan setiap jawaban yang Anda kirim memakai 1 kredit lagi. Akun Basic mendapat total 3 sesi uji coba gratis; Premium tanpa batas.',
                ],
                'select_cv' => [
                    'title' => '1. Pilih CV-nya',
                    'body' => 'Tentukan resume yang ingin dijadikan bahan wawancara. Pertanyaannya disusun dari isi resume itu, jadi gunakan yang benar-benar Anda kirimkan ke perusahaan.',
                ],
                'position' => [
                    'title' => '2. Sebutkan posisinya',
                    'body' => 'Ketik posisi yang Anda lamar. Kolom ini terisi otomatis dari bagian Target Job resume, dan bisa Anda ubah untuk lowongan lain.',
                ],
                'start' => [
                    'title' => '3. Mulai wawancaranya',
                    'body' => 'Jawab lewat chat, tekan Enter untuk mengirim dan Shift+Enter untuk baris baru. Pakai pola STAR — Situation, Task, Action, Result — karena itulah yang dinilai dalam laporan. Setelah selesai, klik End Session lalu Generate Report dengan biaya 1 kredit.',
                ],
                'history' => [
                    'title' => 'Pantau perkembangan Anda',
                    'body' => 'Sesi-sesi sebelumnya beserta skornya tersimpan di sini. Buka salah satunya untuk membaca ulang percakapan atau laporannya, dan bandingkan skor seiring latihan Anda.',
                ],
            ],
        ],
    ],

    'help' => [
        'hero_title' => 'Pusat Bantuan',
        'hero_subtitle' => 'Temukan jawaban atas pertanyaan umum atau hubungi tim dukungan kami.',
        'my_tickets' => 'Tiket Saya',
        'faq_heading' => 'Pertanyaan yang Sering Diajukan',
        'faq' => [
            'getting_started' => [
                'label' => 'Memulai',
                'items' => [
                    ['q' => 'Bagaimana cara membuat resume pertama saya?', 'a' => 'Buka dashboard Anda dan klik "New Resume". Pilih template, lalu isi informasi pribadi, pengalaman kerja, pendidikan, dan keahlian Anda. Anda dapat melihat pratinjau dan mengunduh resume sebagai PDF kapan saja.'],
                    ['q' => 'Template apa saja yang tersedia?', 'a' => 'Kami menawarkan berbagai template yang dirancang secara profesional untuk berbagai industri dan tingkat pengalaman. Kunjungi halaman Templates untuk melihat pratinjau semua desain yang tersedia.'],
                    ['q' => 'Bisakah saya membuat beberapa resume?', 'a' => 'Tentu! Anda dapat membuat resume sebanyak yang Anda butuhkan. Setiap resume dapat disesuaikan secara independen untuk lamaran pekerjaan yang berbeda.'],
                ],
            ],
            'resume_builder' => [
                'label' => 'Pembuat Resume',
                'items' => [
                    ['q' => 'Bagaimana cara mengunduh resume saya sebagai PDF?', 'a' => 'Buka resume Anda di editor dan klik tombol "Export PDF" di pojok kanan atas. Resume Anda akan dibuat dan diunduh secara otomatis.'],
                    ['q' => 'Bisakah saya mengganti template setelah mulai mengedit?', 'a' => 'Ya, Anda dapat mengganti template kapan saja dari editor. Konten Anda akan tetap tersimpan, hanya desain visual yang akan berubah.'],
                    ['q' => 'Bagian apa saja yang bisa saya tambahkan ke resume saya?', 'a' => 'Anda dapat menambahkan Informasi Pribadi, Pengalaman Kerja, Pendidikan, Keahlian, dan bagian Target Pekerjaan. Setiap bagian dapat disesuaikan dengan latar belakang Anda.'],
                ],
            ],
            'ai_features' => [
                'label' => 'Fitur AI',
                'items' => [
                    ['q' => 'Bagaimana cara meningkatkan skor ATS saya?', 'a' => 'Gunakan fitur ATS Analyzer untuk memeriksa seberapa cocok resume Anda dengan deskripsi pekerjaan. Tempelkan lowongan pekerjaan, dan AI kami akan mengidentifikasi kata kunci yang hilang serta menyarankan perbaikan. Akun Basic mendapat 3 analisis gratis; Premium tanpa batas.'],
                    ['q' => 'Berapa banyak kredit AI yang saya dapatkan?', 'a' => 'Pengguna Basic menerima 10 kredit AI per bulan. Pengguna Premium mendapatkan 100 kredit. Setiap aksi AI (analisis ATS, optimasi bullet, dll.) menggunakan sejumlah kredit tertentu.'],
                    ['q' => 'Apa fungsi "AI Polish"?', 'a' => 'AI Polish menulis ulang poin-poin resume Anda agar lebih berdampak, menggunakan kata kerja aksi yang kuat dan pencapaian yang terukur. Fitur ini menggunakan 1 kredit per poin.'],
                ],
            ],
            'billing' => [
                'label' => 'Tagihan',
                'items' => [
                    ['q' => 'Bagaimana cara upgrade ke Premium?', 'a' => 'Buka halaman Upgrade dari dashboard atau pengaturan Anda. Kami mendukung pembayaran melalui Midtrans (transfer bank, e-wallet, dan kartu).'],
                    ['q' => 'Apa yang terjadi pada data saya jika saya membatalkan?', 'a' => 'Resume dan data Anda tetap tersimpan. Anda akan diturunkan ke paket Basic dan kuota AI Anda akan disesuaikan pada siklus penagihan berikutnya.'],
                ],
            ],
            'technical' => [
                'label' => 'Teknis',
                'items' => [
                    ['q' => 'Apakah data saya aman?', 'a' => 'Ya. Semua data dienkripsi saat transit (HTTPS) dan saat disimpan. Kami tidak pernah membagikan informasi pribadi Anda kepada pihak ketiga. Anda dapat menghapus akun dan semua data terkait kapan saja dari Settings.'],
                    ['q' => 'Mengapa PDF saya tidak bisa diekspor?', 'a' => 'Pastikan semua bagian yang wajib diisi (Informasi Pribadi, Pengalaman Kerja, Pendidikan, Keahlian) sudah terisi. Jika masalah berlanjut, coba browser lain atau hubungi dukungan.'],
                ],
            ],
        ],
        'contact' => [
            'heading' => 'Masih butuh bantuan?',
            'subtitle' => 'Kirimkan pesan kepada kami dan kami akan segera menghubungi Anda kembali.',
            'subject_label' => 'Subjek',
            'subject_placeholder' => 'Jelaskan secara singkat masalah Anda...',
            'message_label' => 'Pesan',
            'message_placeholder' => 'Jelaskan masalah Anda secara rinci...',
            'send' => 'Kirim Pesan',
            'sending' => 'Mengirim...',
            'success' => 'Pesan Anda telah terkirim! Kami akan segera menghubungi Anda kembali.',
        ],
    ],

    'tickets' => [
        'status' => [
            'open' => 'Terbuka',
            'pending' => 'Menunggu',
            'awaiting_closure' => 'Menunggu Penutupan',
            'closed' => 'Ditutup',
        ],
        'list' => [
            'breadcrumb_help' => 'Pusat Bantuan',
            'breadcrumb_current' => 'Tiket Saya',
            'heading' => 'Tiket Dukungan Saya',
            'new_ticket' => 'Tiket Baru',
            'replies_count' => ':count balasan',
            'empty_title' => 'Belum ada tiket dukungan',
            'empty_cta' => 'Kirim tiket pertama Anda',
        ],
        'show' => [
            'breadcrumb_help' => 'Pusat Bantuan',
            'breadcrumb_my_tickets' => 'Tiket Saya',
            'ticket_number' => 'Tiket #:id',
            'opened_on' => 'Dibuka :date',
        ],
        'chat' => [
            'conversation' => 'Percakapan',
            'unknown_sender' => 'Tidak diketahui',
            'support_badge' => 'Dukungan',
            'no_replies' => 'Belum ada balasan.',
            'ready_to_close' => 'Siap menutup tiket ini?',
            'request_close' => 'Ajukan Penutupan',
            'waiting_other_party' => 'Menunggu pihak lain untuk mengonfirmasi atau menolak permintaan penutupan Anda.',
            'other_requested_close' => 'Pihak lain meminta untuk menutup tiket ini.',
            'reject' => 'Tolak',
            'confirm_close' => 'Konfirmasi Penutupan',
            'reply_label' => 'Pesan balasan',
            'reply_placeholder' => 'Ketik balasan Anda...',
            'send_reply' => 'Kirim Balasan',
            'sending' => 'Mengirim...',
            'closed_notice' => 'Tiket ini telah ditutup.',
            'reply_sent' => 'Balasan Anda telah terkirim.',
            'close_already_pending_error' => 'Permintaan penutupan sudah menunggu atau tiket sudah ditutup.',
            'no_pending_confirm_error' => 'Tidak ada permintaan penutupan yang menunggu untuk dikonfirmasi.',
            'no_pending_reject_error' => 'Tidak ada permintaan penutupan yang menunggu untuk ditolak.',
            'closed_reply_error' => 'Tiket ini telah ditutup dan tidak dapat menerima balasan baru.',
        ],
    ],

    'settings' => [
        'page_title' => 'Pengaturan Akun',
        'heading' => 'Pengaturan',
        'subtitle' => 'Kelola kehadiran editorial dan keamanan workspace Anda.',
        'notifications' => [
            'heading' => 'Notifikasi',
            'mark_all_read' => 'Tandai semua telah dibaca',
            'default_message' => 'Anda memiliki notifikasi baru.',
            'empty' => 'Belum ada notifikasi.',
        ],
        'flash' => [
            'avatar_updated' => 'Avatar berhasil diperbarui.',
            'avatar_deleted' => 'Avatar berhasil dihapus.',
            'profile_updated' => 'Profil berhasil diperbarui.',
            'password_updated' => 'Kata sandi berhasil diperbarui.',
        ],
        'profile_section' => [
            'title' => 'Profil Pengguna',
            'verified_badge' => 'Penulis Terverifikasi',
            'avatar_alt' => 'Avatar Pengguna',
            'avatar_format_hint' => 'Format: JPG, PNG (Maks 2MB)',
            'full_name' => 'Nama Lengkap',
            'email_address' => 'Alamat Email',
            'locked' => 'terkunci',
            'save_changes' => 'Simpan Perubahan',
        ],
        'billing_section' => [
            'title' => 'Langganan & Tagihan',
            'upgrade_quota' => 'Upgrade Kuota',
            'optimized_by' => 'Dioptimalkan oleh Resumify Editorial Engine.',
            'view_transaction_history' => 'Lihat Riwayat Transaksi',
            'transaction_history_wip' => 'Fitur riwayat transaksi sedang dalam pengembangan. Silakan periksa kembali nanti.',
        ],
        'security_section' => [
            'title' => 'Keamanan & Kata Sandi',
            'subtitle' => 'Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.',
            'current_password' => 'Kata Sandi Saat Ini',
            'new_password' => 'Kata Sandi Baru',
            'confirm_new_password' => 'Konfirmasi Kata Sandi Baru',
            'password_hint' => 'Gunakan minimal 8 karakter dengan kombinasi angka dan simbol.',
            'update_password' => 'Perbarui Kata Sandi',
        ],
        'danger_zone' => [
            'title' => 'Zona Berbahaya',
            'warning' => 'Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus secara permanen dari server kami.',
            'confirm_password_placeholder' => 'Konfirmasi kata sandi Anda',
            'delete_account' => 'Hapus Akun',
            'confirm_dialog' => 'Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.',
        ],
    ],

    'upgrade_quota' => [
        'page_title' => 'Paket Harga',
        'hero' => [
            'title' => 'Pilih Paket Suksesmu ✨',
            'subtitle' => 'Tingkatkan narasi kariermu dengan kecerdasan buatan. Biarkan setiap baris pengalamanmu berbicara dengan penuh percaya diri.',
        ],
        'status' => [
            'cancellation_scheduled' => 'Pembatalan Dijadwalkan',
            'premium_active' => 'Premium Aktif',
            'plan_name' => 'Premium PRO',
            'active_until' => 'Akses premium Anda tetap aktif hingga :date.',
            'billing_ends' => 'Periode penagihan Anda saat ini berakhir pada :date.',
            'access_active' => 'Akses premium Anda aktif.',
            'cancel_confirm' => 'Batalkan Premium PRO? Akses premium Anda akan tetap aktif hingga akhir periode penagihan ini.',
            'cancel_plan' => 'Batalkan Paket',
        ],
        'plans' => [
            'starter' => 'Starter',
            'forever' => 'selamanya',
            'month' => 'bulan',
            'one_active_resume' => '1 Resume Aktif',
            'standard_templates' => 'Template Standar',
            'no_ai_enhancement' => 'Tanpa Peningkatan AI',
            'premium_pro' => 'Premium PRO',
            'unlimited_resumes' => 'Resume Tanpa Batas',
            'ai_bullet_optimizer' => 'Pengoptimal Poin AI',
            'ai_bullet_optimizer_subtitle' => 'Optimalkan dengan kata kunci berdampak tinggi',
            'realtime_ats_matcher' => 'Pencocok ATS Real-time',
            'premium_pdf_export' => 'Ekspor PDF Premium',
            'priority_support' => 'Dukungan Prioritas',
            'activate_premium' => 'Aktifkan Premium Sekarang',
            'activate_premium_aria' => 'Aktifkan paket Premium PRO',
        ],
        'payment' => [
            'title' => 'Metode Pembayaran Aman',
            'credit_card' => 'Kartu Kredit',
            'bank_transfer' => 'Transfer Bank',
            'ewallet' => 'Gopay / OVO',
            'money_back' => 'Jaminan Uang Kembali 30 Hari',
            'security_note' => 'Transaksi Anda dilindungi dengan enkripsi AES-256 bit. Privasi dan keamanan data Anda adalah prioritas utama kami.',
        ],
        'social' => [
            'quote' => 'Resumify Premium bukan sekadar alat, ini adalah investasi untuk masa depan. Dengan pengoptimal poin AI, saya mendapat panggilan wawancara dalam 3 hari.',
            'role' => 'Senior Product Manager',
            'careers_elevated' => 'Karier Berhasil Ditingkatkan',
            'join_community' => 'Bergabunglah dengan komunitas profesional hari ini.',
        ],
        'footer' => '© 2026 Resumify - Dikurasi dengan Integritas',
        'js' => [
            'processing' => 'Memproses...',
            'payment_success' => 'Pembayaran berhasil!',
            'payment_pending' => 'Menunggu pembayaran Anda!',
            'payment_failed' => 'Pembayaran gagal!',
            'popup_closed' => 'Anda menutup popup tanpa menyelesaikan pembayaran.',
            'popup_not_ready' => 'Popup pembayaran Midtrans belum siap. Silakan segarkan halaman dan coba lagi.',
            'init_failed' => 'Gagal memulai pembayaran. Silakan coba lagi.',
            'generic_error' => 'Terjadi kesalahan. Silakan coba lagi nanti.',
        ],
    ],

    'landing' => [

        'navbar' => [
            'features' => 'Fitur',
            'templates' => 'Template',
            'pricing' => 'Harga',
            'login' => 'Masuk',
            'cta_create_resume' => 'Buat Resume Gratis',
            'language' => 'Bahasa',
        ],

        'footer' => [
            'copyright' => 'Resumify. Manuskrip Pilihan.',
            'privacy_policy' => 'Kebijakan Privasi',
            'terms_of_service' => 'Syarat Layanan',
            'cookie_policy' => 'Kebijakan Cookie',
            'contact' => 'Kontak',
            'close' => 'Tutup',
            'legal' => [
                'privacy' => [
                    'title' => 'Kebijakan Privasi',
                    'html' => '
                        <p class="mb-4">Di Resumify, kami sangat menghargai privasi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi Anda saat mengunjungi situs web kami.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">1. Informasi yang Kami Kumpulkan</h4>
                        <p class="mb-4">Kami dapat mengumpulkan informasi identifikasi pribadi dari Pengguna dengan berbagai cara, termasuk namun tidak terbatas pada saat Pengguna mengunjungi situs kami, mendaftar di situs, melakukan pemesanan, dan sehubungan dengan aktivitas, layanan, fitur, atau sumber daya lain yang kami sediakan di Situs kami.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">2. Bagaimana Kami Menggunakan Informasi yang Dikumpulkan</h4>
                        <p class="mb-4">Resumify dapat mengumpulkan dan menggunakan informasi pribadi Pengguna untuk tujuan berikut:</p>
                        <ul class="list-disc pl-5 mb-4 space-y-1">
                            <li>Meningkatkan layanan pelanggan</li>
                            <li>Mempersonalisasi pengalaman pengguna</li>
                            <li>Memproses pembayaran secara aman</li>
                            <li>Mengirim email berkala terkait pembaruan manuskrip Anda</li>
                        </ul>
                        <h4 class="font-bold text-primary mb-2 mt-6">3. Keamanan Data</h4>
                        <p>Kami menerapkan praktik pengumpulan, penyimpanan, dan pemrosesan data yang sesuai serta langkah-langkah keamanan untuk melindungi dari akses, perubahan, pengungkapan, atau perusakan yang tidak sah terhadap informasi pribadi, nama pengguna, kata sandi, informasi transaksi, dan data yang tersimpan di Situs kami.</p>
                    ',
                ],
                'terms' => [
                    'title' => 'Syarat Layanan',
                    'html' => '
                        <p class="mb-4">Selamat datang di Resumify. Dengan mengakses situs web ini, kami menganggap Anda menerima syarat dan ketentuan ini. Jangan melanjutkan penggunaan Resumify jika Anda tidak menyetujui seluruh syarat dan ketentuan yang tercantum di halaman ini.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">1. Lisensi</h4>
                        <p class="mb-4">Kecuali dinyatakan lain, Resumify dan/atau pemberi lisensinya memiliki hak kekayaan intelektual atas seluruh materi di Resumify. Semua hak kekayaan intelektual dilindungi. Anda dapat mengakses konten ini dari Resumify untuk penggunaan pribadi Anda, dengan tunduk pada batasan yang ditetapkan dalam syarat dan ketentuan ini.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">2. Akun Pengguna</h4>
                        <p class="mb-4">Saat Anda membuat akun bersama kami, Anda harus memberikan informasi yang akurat, lengkap, dan terkini setiap saat. Kegagalan melakukan hal ini merupakan pelanggaran terhadap Ketentuan ini, yang dapat mengakibatkan penghentian akun Anda di Layanan kami secara langsung.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">3. Batasan Tanggung Jawab</h4>
                        <p>Resumify, beserta pejabat, direktur, dan karyawannya, tidak akan bertanggung jawab atas hal apa pun yang timbul dari atau terkait dengan penggunaan Situs Web ini, baik tanggung jawab tersebut berdasarkan kontrak maupun lainnya.</p>
                    ',
                ],
                'cookie' => [
                    'title' => 'Kebijakan Cookie',
                    'html' => '
                        <p class="mb-4">Situs web kami menggunakan cookie untuk membedakan Anda dari pengguna lain di situs kami. Ini membantu kami memberikan pengalaman yang baik saat Anda menjelajahi situs kami serta memungkinkan kami untuk meningkatkan situs kami.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">1. Apa itu cookie?</h4>
                        <p class="mb-4">Cookie adalah file kecil berisi huruf dan angka yang kami simpan di browser Anda atau hard drive komputer Anda jika Anda menyetujuinya. Cookie berisi informasi yang dipindahkan ke hard drive komputer Anda.</p>
                        <h4 class="font-bold text-primary mb-2 mt-6">2. Bagaimana kami menggunakan cookie</h4>
                        <p class="mb-4">Kami menggunakan cookie berikut:</p>
                        <ul class="list-disc pl-5 mb-4 space-y-1">
                            <li><strong>Cookie yang sangat diperlukan:</strong> Diperlukan untuk pengoperasian situs web kami, seperti area login yang aman.</li>
                            <li><strong>Cookie analitik atau kinerja:</strong> Memungkinkan kami mengenali dan menghitung jumlah pengunjung.</li>
                            <li><strong>Cookie fungsionalitas:</strong> Digunakan untuk mengenali Anda saat kembali ke situs web kami dan mengingat preferensi Anda.</li>
                        </ul>
                        <p>Anda dapat memblokir cookie dengan mengaktifkan pengaturan di browser Anda yang memungkinkan Anda menolak pengaturan sebagian atau seluruh cookie.</p>
                    ',
                ],
                'contact' => [
                    'title' => 'Hubungi Kami',
                    'html' => '
                        <p class="mb-6 text-[15px]">Kami senang mendengar dari Anda. Jika Anda memiliki pertanyaan, kekhawatiran, atau masukan mengenai Resumify, silakan hubungi tim dukungan kami.</p>

                        <div class="bg-primary/5 p-4 rounded-xl mb-4 border border-primary/10">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-secondary">mail</span>
                                <span class="font-bold text-primary">Dukungan Email</span>
                            </div>
                            <p class="text-primary/70 ml-9">hello@resumify.com<br>support@resumify.com</p>
                        </div>

                        <div class="bg-primary/5 p-4 rounded-xl mb-6 border border-primary/10">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-secondary">location_on</span>
                                <span class="font-bold text-primary">Kantor Pusat</span>
                            </div>
                            <p class="text-primary/70 ml-9">123 Innovation Drive<br>Tech District, San Francisco<br>CA 94105, United States</p>
                        </div>

                        <p class="text-sm italic text-primary/60">Tim dukungan kami biasanya merespons dalam 24-48 jam kerja.</p>
                    ',
                ],
            ],
        ],

        'welcome' => [
            'hero' => [
                'title' => 'Tulis Kisah Suksesmu',
                'subtitle' => 'Sesuaikan resume Anda dengan lowongan pekerjaan menggunakan kecerdasan buatan.',
                'cta' => 'Tingkatkan Resume Anda',
                'cta_secondary' => 'Lihat Template',
                'reassurance' => 'Gratis untuk memulai — tanpa kartu kredit.',
            ],
            'preview' => [
                'work_experience' => 'PENGALAMAN KERJA',
                'ai_optimized' => 'Dioptimalkan AI',
                'ai_optimized_desc' => 'Memimpin tim desain beranggotakan 12 orang dan meningkatkan tingkat konversi pengguna sebesar 34% melalui pengujian A/B yang sistematis.',
                'ats_match_score' => 'SKOR KECOCOKAN ATS',
                'low' => 'Rendah',
                'high' => 'Tinggi',
                'input_editor' => 'EDITOR INPUT',
                'name_label' => 'NAMA',
                'company_label' => 'PERUSAHAAN',
                'description_label' => 'DESKRIPSI',
                'description_value' => 'Memimpin tim desain dan meningkatkan konversi sebesar 34%...',
                'resume_quality' => 'Kualitas resume Anda',
                'high_match' => 'Kecocokan Tinggi',
            ],
            'features' => [
                'title' => 'Semua yang kamu butuhkan untuk dapat kerja',
                'subtitle' => 'Buat CV, cek skor ATS, dan latihan wawancara — satu alur kerja tenang yang dibuat untuk pasar kerja Indonesia.',
                'ai_bullet' => [
                    'title' => 'Generator Poin AI',
                    'desc' => 'Tulis pencapaian Anda secara instan dengan saran berbasis data yang menonjol di mata perekrut.',
                ],
                'ats_scanner' => [
                    'title' => 'Pemindai Skor Kecocokan ATS',
                    'desc' => 'Evaluasi resume Anda terhadap deskripsi pekerjaan secara real-time untuk memastikan lolos sistem penyaringan.',
                ],
                'premium_templates' => [
                    'title' => 'Template Premium',
                    'desc' => 'Kumpulan template yang dikurasi secara profesional untuk berbagai industri dan tingkat karier.',
                ],
            ],
            'how' => [
                'title' => 'Dari halaman kosong sampai siap wawancara',
                'subtitle' => 'Satu alur, tiga langkah — setiap langkah melanjutkan langkah sebelumnya.',
                'steps' => [
                    'build' => [
                        'title' => 'Buat dengan AI',
                        'desc' => 'Mulai dari template dan biarkan AI mengubah pengalamanmu menjadi poin-poin yang siap dilirik perekrut.',
                    ],
                    'scan' => [
                        'title' => 'Cek dengan lowongan',
                        'desc' => 'Tempel deskripsi pekerjaan dan dapatkan daftar kekurangan yang konkret — bukan sekadar skor.',
                    ],
                    'rehearse' => [
                        'title' => 'Latihan dengan Ms. Sarah',
                        'desc' => 'Latih pertanyaan wawancara dari CV-mu sendiri dan dapatkan umpan balik STAR yang terstruktur.',
                    ],
                ],
            ],
            'cta' => [
                'title' => 'Siap membangun kisahmu?',
                'subtitle' => 'Bergabunglah dengan ribuan profesional yang telah mempercepat karier mereka bersama Resumify.',
                'button' => 'Mulai Gratis Sekarang',
            ],
        ],

        'templates' => [
            'hero_title' => 'Pilih Template<br>yang Sesuai Kariermu',
            'hero_subtitle' => 'Dari minimalis hingga kreatif, semua template kami dioptimalkan untuk lolos filter ATS dengan sentuhan editorial kelas atas.',
            'tab_all' => 'Semua',
            'use_template_full' => 'Gunakan Template Ini',
            'use_template_short' => 'Gunakan Template',
            'preview_aria' => 'Pratinjau template :title',
            'preview_template' => 'Pratinjau Template',
            'close_preview' => 'Tutup pratinjau',
            'cta' => [
                'title' => 'Belum menemukan yang cocok?',
                'subtitle' => 'Jangan khawatir, semua template dapat disesuaikan sepenuhnya untuk memenuhi kebutuhan personal brand Anda. Mulai perjalanan karier Anda hari ini.',
                'button' => 'Daftar Gratis Sekarang',
            ],
        ],

        'pricing' => [
            'hero' => [
                'title' => 'Investasikan Kariermu',
                'subtitle' => 'Mulai gratis, atau buka potensi penuh Anda dengan fitur AI Premium.',
            ],
            'plans' => [
                'starter' => 'Starter',
                'forever' => 'selamanya',
                'month' => 'bulan',
                'standard_templates' => 'Template Standar',
                'no_ai_enhancement' => 'Tanpa Peningkatan AI',
                'get_started' => 'Mulai Sekarang',
                'premium_pro' => 'Premium PRO',
                'ai_bullet_optimizer' => 'Pengoptimal Poin AI',
                'ai_bullet_optimizer_subtitle' => 'Optimalkan dengan kata kunci berdampak tinggi',
                'realtime_ats_matcher' => 'Pencocok ATS Real-time',
                'premium_pdf_export' => 'Ekspor PDF Premium',
                'priority_support' => 'Dukungan Prioritas',
                'activate_premium' => 'Aktifkan Premium Sekarang',
                'most_popular' => 'Paling Populer',
                'ai_powered' => 'Didukung AI',
                'current_plan' => 'Paket Saat Ini',
            ],
            'compare' => [
                'title' => 'Bandingkan Fitur Kami',
                'key_features' => 'Fitur Utama',
                'number_of_resumes' => 'Jumlah Resume',
                'standard' => 'Standar',
                'premium' => 'Premium',
            ],
            'faq' => [
                'title' => 'Pertanyaan yang Sering Diajukan',
                'q1' => 'Bisakah saya membatalkan langganan saya?',
                'a1' => 'Ya, Anda dapat membatalkan langganan kapan saja melalui pengaturan akun Anda. Akses premium Anda akan tetap aktif hingga akhir periode penagihan saat ini.',
                'q2' => 'Metode pembayaran apa saja yang tersedia?',
                'a2' => 'Kami menerima kartu kredit (Visa, Mastercard), PayPal, dan berbagai dompet digital lokal untuk memfasilitasi transaksi Anda dengan aman.',
                'q3' => 'Bagaimana AI membantu resume saya?',
                'a3' => 'AI kami menganalisis deskripsi pekerjaan dan memberikan saran kata kunci yang relevan, mengoptimalkan tata bahasa poin, dan memastikan format resume Anda mudah dibaca oleh sistem ATS.',
            ],
        ],

    ],

];
