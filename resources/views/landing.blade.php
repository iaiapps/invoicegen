<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InvoiceGen - Invoice Management untuk UMKM Indonesia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <a href="#" class="logo">
                <i class="bi bi-receipt-cutoff"></i>
                <span>InvoiceGen</span>
            </a>
            <nav>
                <div class="nav-links">
                    <a href="#features">Fitur</a>
                    <a href="#pricing">Harga</a>
                    <a href="#testimonials">Testimoni</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="nav-buttons">
                    <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Mulai Gratis</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-badge">
                <i class="bi bi-star-fill" style="color: hsl(var(--primary));"></i>
                <span>70% Lebih Murah · Gratis Selamanya untuk 20 Invoice</span>
            </div>
            <h1>Invoice Profesional,<br>Harga Rakyat</h1>
            <p class="hero-description">
                Solusi invoice terjangkau untuk UMKM mikro & kecil Indonesia.
                Mulai GRATIS atau upgrade dari <strong>Rp 25.000/bulan</strong> - lebih murah dari biaya pulsa!
            </p>
            <div class="hero-buttons">
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                    Mulai Gratis Sekarang
                    <i class="bi bi-arrow-right"></i>
                </a>
                <a href="#pricing" class="btn btn-outline btn-lg">
                    <i class="bi bi-tag"></i>
                    Lihat Harga
                </a>
            </div>
            <div class="hero-image">
                <img src="{{ asset('assets/img/hero.png') }}" alt="hero image">
                {{-- <svg width="1100" height="600" viewBox="0 0 1100 600" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect width="1100" height="600" fill="hsl(var(--muted))" />
                    <rect x="50" y="50" width="1000" height="500" rx="8" fill="white" />
                    <rect x="50" y="50" width="1000" height="60" rx="8" fill="hsl(var(--primary))" />
                    <text x="90" y="90" font-family="sans-serif" font-size="24" font-weight="700"
                        fill="white">InvoiceGen Dashboard</text>
                    <rect x="90" y="150" width="300" height="40" rx="4" fill="hsl(var(--muted))" />
                    <rect x="90" y="210" width="920" height="300" rx="4" fill="hsl(var(--muted))" />
                    <text x="550" y="370" font-family="sans-serif" font-size="18" fill="hsl(var(--muted-foreground))"
                        text-anchor="middle">Dashboard Preview</text>
                </svg> --}}
            </div>
        </div>
    </section>

    <!-- Trust Bar -->
    <section class="trust-bar">
        <div class="trust-container">
            <p class="trust-text">Dipercaya oleh ribuan bisnis di Indonesia</p>
            <div class="trust-logos">
                <div class="trust-stat">
                    <div class="trust-number">10K+</div>
                    <div class="trust-label">Invoice Dibuat</div>
                </div>
                <div class="trust-stat">
                    <div class="trust-number">500+</div>
                    <div class="trust-label">Bisnis Aktif</div>
                </div>
                <div class="trust-stat">
                    <div class="trust-number">99%</div>
                    <div class="trust-label">Kepuasan</div>
                </div>
                <div class="trust-stat">
                    <div class="trust-number">24/7</div>
                    <div class="trust-label">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="features-container">
            <div class="section-header">
                <span class="section-badge">Fitur</span>
                <h2 class="section-title">Semua yang Anda butuhkan</h2>
                <p class="section-description">
                    Platform lengkap untuk mengelola invoice bisnis Anda dengan lebih efisien dan profesional
                </p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h3>Buat Invoice Cepat</h3>
                    <p>Template siap pakai untuk berbagai jenis bisnis. Buat invoice profesional dalam hitungan detik.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <h3>WhatsApp Integration</h3>
                    <p>Kirim invoice langsung ke WhatsApp customer dengan satu klik. Mudah dan praktis.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <h3>Export PDF</h3>
                    <p>Download invoice dalam format PDF berkualitas tinggi untuk dikirim ke customer atau arsip.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3>Customer Management</h3>
                    <p>Database customer terintegrasi dengan riwayat transaksi dan detail kontak lengkap.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h3>Product Catalog</h3>
                    <p>Kelola katalog produk/jasa dengan harga, stok, dan deskripsi untuk input invoice lebih cepat.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3>Analytics Dashboard</h3>
                    <p>Laporan penjualan, grafik performa, dan insights bisnis yang membantu pengambilan keputusan.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <h3>Payment Tracking</h3>
                    <p>Track invoice yang belum dibayar dengan status jelas (Paid/Unpaid) dan kirim reminder manual via
                        WhatsApp.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-image"></i>
                    </div>
                    <h3>Custom Logo</h3>
                    <p>Tambahkan logo toko dan informasi perusahaan untuk invoice yang lebih profesional dan terpercaya.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3>Secure & Reliable</h3>
                    <p>Data Anda aman dengan enkripsi tingkat enterprise dan backup otomatis setiap hari.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works">
        <div class="features-container">
            <div class="section-header">
                <span class="section-badge">Cara Kerja</span>
                <h2 class="section-title">Mulai dalam 3 langkah mudah</h2>
                <p class="section-description">
                    Tidak perlu setup rumit. Mulai buat invoice pertama Anda dalam waktu kurang dari 5 menit
                </p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Daftar Gratis</h3>
                    <p>Buat akun dalam hitungan detik. Tidak perlu kartu kredit untuk trial 7 hari.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Setup Bisnis</h3>
                    <p>Tambahkan informasi bisnis, customer, dan produk Anda dengan mudah.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Buat Invoice</h3>
                    <p>Pilih template, isi detail, dan kirim invoice ke customer via WhatsApp atau email.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="testimonials">
        <div class="features-container">
            <div class="section-header">
                <span class="section-badge">Testimoni</span>
                <h2 class="section-title">Apa kata mereka tentang kami</h2>
                <p class="section-description">
                    Ribuan bisnis sudah merasakan kemudahan mengelola invoice dengan InvoiceGen
                </p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-content">
                        "InvoiceGen sangat membantu bisnis saya! Sekarang saya bisa kirim invoice langsung ke WhatsApp
                        customer dalam hitungan detik. Sangat praktis!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">AB</div>
                        <div class="author-info">
                            <h4>Ahmad Budiman</h4>
                            <p>Owner, Toko Elektronik Maju</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-content">
                        "Dashboard analytics-nya sangat membantu saya memantau penjualan. Sekarang saya bisa lihat
                        performa bisnis dengan jelas. Recommended!"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">SR</div>
                        <div class="author-info">
                            <h4>Siti Rahmawati</h4>
                            <p>Owner, Fashion Boutique</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="testimonial-content">
                        "Aplikasi yang simple dan tidak ribet! Harga sangat terjangkau dan fitur WhatsApp integration
                        sangat membantu komunikasi dengan customer."
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">BP</div>
                        <div class="author-info">
                            <h4>Budi Prakoso</h4>
                            <p>Founder, Digital Agency</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="pricing">
        <div class="features-container">
            <div class="section-header">
                <span class="section-badge">Harga Terjangkau</span>
                <h2 class="section-title">Paket untuk UMKM Indonesia</h2>
                <p class="section-description">
                    Mulai gratis selamanya atau upgrade mulai Rp 25.000/bulan. 70% lebih murah dari kompetitor!
                </p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Free</h3>
                        <p>Perfect untuk UMKM Mikro</p>
                    </div>
                    <div class="pricing-price">
                        <div class="price-amount">Gratis</div>
                        <div class="price-period">selamanya</div>
                    </div>
                    <ul class="pricing-features">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><strong>30 Invoice</strong> per bulan</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><strong>20 Produk</strong></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Customer unlimited</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Export PDF</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>WhatsApp sharing</span>
                        </li>
                        {{-- <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Email support</span>
                        </li> --}}
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-outline" style="width: 100%;">Mulai Gratis</a>
                </div>

                <div class="pricing-card featured">
                    <span class="pricing-badge">POPULER</span>
                    <div class="pricing-header">
                        <h3>Basic</h3>
                        <p>UMKM Kecil yang Berkembang</p>
                    </div>
                    <div class="pricing-price">
                        <div class="price-amount">Rp 25K</div>
                        <div class="price-period">per bulan</div>
                    </div>
                    <ul class="pricing-features">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><strong>60 Invoice</strong> per bulan</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><strong>40 Produk</strong></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Customer unlimited</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Export PDF</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>WhatsApp sharing</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Dashboard analytics</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Custom branding (logo)</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Priority support</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;">Pilih Basic</a>
                </div>

                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Pro</h3>
                        <p>UMKM yang Lebih Besar</p>
                    </div>
                    <div class="pricing-price">
                        <div class="price-amount">Rp 49K</div>
                        <div class="price-period">per bulan</div>
                    </div>
                    <ul class="pricing-features">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><strong>120 Invoice</strong> per bulan</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span><strong>80 Produk</strong></span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Customer unlimited</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Export PDF</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>WhatsApp sharing</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Dashboard analytics</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Custom branding (logo)</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Priority support</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Grace period 7 hari</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-outline" style="width: 100%;">Pilih Pro</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="faq">
        <div class="faq-container">
            <div class="section-header">
                <span class="section-badge">FAQ</span>
                <h2 class="section-title">Pertanyaan yang sering diajukan</h2>
            </div>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        <span>Apakah benar gratis selamanya?</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Ya! Paket Free memberikan 30 invoice dan 20 produk per bulan selamanya tanpa biaya apapun.
                            Tidak ada trial period atau batasan waktu.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        <span>Bagaimana cara upgrade paket?</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Anda bisa upgrade kapan saja dari halaman Subscription. Pilih paket yang diinginkan (Basic Rp
                            25.000 atau Pro Rp 49.000), lakukan pembayaran via transfer bank atau QRIS, kirim bukti
                            transfer ke WhatsApp admin, dan tunggu approval (biasanya 1-24 jam).</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        <span>Apa yang terjadi jika subscription habis?</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Anda mendapat grace period 7 hari. Selama masa ini, Anda masih bisa akses aplikasi tapi
                            dengan limit Free (30 invoice/bulan). Setelah 7 hari, akun otomatis downgrade ke paket Free.
                            Data Anda tetap aman dan tidak hilang.</p>
                    </div>
                </div>

                {{-- <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        <span>Apakah data saya aman?</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Ya, kami menggunakan enkripsi Laravel standar, password hashing bcrypt, dan perlindungan
                            CSRF. Data tersimpan di server yang aman dengan akses terbatas berbasis role.</p>
                    </div>
                </div> --}}

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        <span>Bagaimana cara kerja WhatsApp integration?</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Setelah membuat invoice, klik tombol "Kirim WhatsApp". Aplikasi akan otomatis membuka
                            WhatsApp dengan link invoice yang sudah terisi. Customer bisa langsung buka invoice di
                            browser mereka.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        <span>Apakah ada batasan jumlah customer?</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Tidak ada batasan! Semua paket (Free, Basic, Pro) mendukung unlimited customer. Anda bisa
                            menambahkan sebanyak mungkin customer yang dibutuhkan.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        <span>Bisakah saya menambahkan logo toko?</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Ya! Upload logo dari halaman Settings. Logo akan otomatis muncul di semua invoice. Format:
                            JPG, PNG (max 2MB).</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFaq(this)">
                        <span>Apakah ada biaya tersembunyi?</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Tidak ada! Harga yang tertera adalah harga final per bulan. Tidak ada biaya setup, training,
                            atau maintenance tambahan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div class="cta-container">
            <h2>Siap mengembangkan bisnis Anda?</h2>
            <p>Bergabung dengan ribuan bisnis yang sudah menggunakan InvoiceGen untuk mengelola invoice mereka</p>
            <a href="#" class="btn btn-primary btn-lg">
                Mulai Gratis Sekarang
                <i class="bi bi-arrow-right"></i>
            </a>
            <p class="cta-note">Gratis selamanya 30 invoice perbulan.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3>
                        <i class="bi bi-receipt-cutoff"></i>
                        InvoiceGen
                    </h3>
                    <p>Platform invoice management terbaik untuk UMKM Indonesia</p>
                </div>
                <div class="footer-links">
                    <h4>Produk</h4>
                    <ul>
                        <li><a href="#features">Fitur</a></li>
                        <li><a href="#pricing">Harga</a></li>
                        <li><a href="#">Integrasi</a></li>
                        <li><a href="#">Updates</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Perusahaan</h4>
                    <ul>
                        <li><a href="#">Tentang Kami</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Karir</a></li>
                        <li><a href="#">Kontak</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Dukungan</h4>
                    <ul>
                        <li><a href="#">Bantuan</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#">Dokumentasi</a></li>
                        <li><a href="#">Status</a></li>
                    </ul>
                </div>
                {{-- <div class="footer-links">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div> --}}
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 InvoiceGen. All rights reserved.</p>
                <div class="social-links">
                    <a href="#"><i class="bi bi-twitter"></i></a>
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleFaq(button) {
            const answer = button.nextElementSibling;
            const isActive = button.classList.contains('active');

            // Close all FAQ items
            document.querySelectorAll('.faq-question').forEach(q => {
                q.classList.remove('active');
                q.nextElementSibling.classList.remove('active');
            });

            // Open clicked item if it wasn't active
            if (!isActive) {
                button.classList.add('active');
                answer.classList.add('active');
            }
        }
    </script>
</body>

</html>
