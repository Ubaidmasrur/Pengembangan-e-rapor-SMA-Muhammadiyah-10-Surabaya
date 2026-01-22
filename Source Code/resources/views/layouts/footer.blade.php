<footer class="w-full bg-gray-800 py-5 px-6" aria-label="Footer">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
        <!-- Description Column 1 (50%) -->
        <section class="md:col-span-6" aria-labelledby="footer-desc-title">
            <h2 id="footer-desc-title" class="font-bold text-2xl text-white mb-2">E-Raport PPI</h2>
            <p class="text-gray-300 text-base leading-relaxed">
                Sistem Pelaporan Program Pembelajaran Individual untuk Anak Berkebutuhan Khusus
            </p>
        </section>
        <!-- Navigasi Column 2 (empty for admin, visible for others) -->
        <section class="md:col-span-2" aria-labelledby="footer-nav-title">
            @if (auth()->user() && auth()->user()->role !== 'admin')
                <h3 id="footer-nav-title" class="font-semibold text-lg text-white mb-2">Navigasi</h3>
                <ul class="space-y-1 text-gray-300">
                    <li><a href="#" class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="Beranda">Beranda</a></li>
                    <li><a href="#" class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="Perkembangan">Perkembangan</a></li>
                    <li><a href="#" class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="Laporan">Laporan</a></li>
                    <li><a href="#" class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500"
                            aria-label="Konsultasi">Konsultasi</a></li>
                </ul>
            @else
                <h3 id="footer-nav-title" class="font-semibold text-lg text-transparent mb-2">&nbsp;</h3>
                <ul class="space-y-1 text-transparent">
                    <li>&nbsp;</li>
                    <li>&nbsp;</li>
                    <li>&nbsp;</li>
                    <li>&nbsp;</li>
                </ul>
            @endif
        </section>
        <!-- Bantuan Column 3 -->
        <section class="md:col-span-2" aria-labelledby="footer-help-title">
            <h3 id="footer-help-title" class="font-semibold text-lg text-white mb-2">Bantuan</h3>
            <ul class="space-y-1 text-gray-300">
                <li><a href="#" class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Panduan Pengguna">Panduan Pengguna</a></li>
                <li><a href="https://wa.me/628123" target="_blank"
                        class="hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Kontak Support">Kontak Support</a></li>
            </ul>
        </section>
        <!-- Kontak Column 4 -->
        <section class="md:col-span-2" aria-labelledby="footer-contact-title">
            <h3 id="footer-contact-title" class="font-semibold text-lg text-white mb-2">Kontak</h3>
            <ul class="space-y-1 text-gray-300">
                <li class="flex items-center gap-2">
                    <!-- Mail icon -->
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" aria-label="Email" focusable="false">
                        <title>Email</title>
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                        <polyline points="3 7 12 13 21 7" />
                    </svg>
                    <span>info@erapportppi.id</span>
                </li>
                <li class="flex items-center gap-2">
                    <!-- Phone icon (handset) -->
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" aria-label="Telepon" focusable="false">
                        <title>Telepon</title>
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M22 16.92V19a2 2 0 01-2.18 2A19.86 19.86 0 013 5.18 2 2 0 015 3h2.09a2 2 0 012 1.72c.13 1.13.37 2.25.72 3.32a2 2 0 01-.45 2.11l-1.27 1.27a16 16 0 006.58 6.58l1.27-1.27a2 2 0 012.11-.45c1.07.35 2.19.59 3.32.72A2 2 0 0122 16.92z" />
                    </svg>
                    <span>(021) 1234-5678</span>
                </li>
            </ul>
        </section>
    </div>
</footer>
