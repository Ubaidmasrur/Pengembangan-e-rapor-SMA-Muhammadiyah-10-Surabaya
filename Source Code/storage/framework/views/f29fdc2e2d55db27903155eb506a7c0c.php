



<!-- Navbar -->
<nav class="w-full p-6 py-12 text-gray-800" style="background: linear-gradient(to right, #0C9487, #059669);"
    role="navigation" aria-label="Main Navigation">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <div class="font-bold text-lg text-gray-800">
                <a href="/"
                    class="text-gray-800 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700">E-Rapor</a>
            </div>
        </div>
        <div class="space-x-4 flex items-center">

            <a href="<?php echo e(route('dashboard')); ?>"
                class="nav-link inline-block px-4 py-2 rounded-md text-sm font-medium
                        text-gray-900 bg-white bg-green-100 border border-transparent border-green-700 shadow-md
                        hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700">
                <span class="nav-link-text">Beranda</span>
            </a>

            
            

            
            <?php if(auth()->user() && auth()->user()->role === 'guru'): ?>
                
                <a href="<?php echo e(route('guru.class_member')); ?>"
                    class="nav-link inline-block px-4 py-2 rounded-md text-sm font-medium
                            text-gray-900 bg-white bg-green-100 border border-transparent border-green-700 shadow-md
                            hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700">
                    <span class="nav-link-text">Siswa</span>
                </a>
                <a href="<?php echo e(route('guru.studentgrades.index')); ?>"
                    class="nav-link inline-block px-4 py-2 rounded-md text-sm font-medium
                                text-gray-900 bg-white bg-green-100 border border-transparent border-green-700 shadow-md
                                hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700">
                    <span class="nav-link-text">Penilaian</span>
                </a>
            <?php endif; ?>

            
            <?php
                $laporanRoute = '#';
                if (auth()->user()) {
                    if (auth()->user()->role === 'admin') {
                        $laporanRoute = route('admin.report.admin');
                    } elseif (auth()->user()->role === 'guru') {
                        $laporanRoute = route('guru.report.index');
                    } elseif (in_array(auth()->user()->role, ['siswa', 'wali'])) {
                        $laporanRoute = route('siswa.reports.index');
                    }
                }
            ?>
            <a href="<?php echo e($laporanRoute); ?>"
                class="nav-link inline-block px-4 py-2 rounded-md text-sm font-medium
                        text-gray-900 bg-white bg-green-100 border border-transparent border-green-700 shadow-md
                        hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700">
                <span class="nav-link-text">Laporan</span>
            </a>

            <?php if(auth()->user() && auth()->user()->role === 'admin'): ?>
                <a href="<?php echo e(route('admin.activities.index')); ?>"
                    class="nav-link inline-block px-4 py-2 rounded-md text-sm font-medium
                        text-gray-900 bg-white bg-green-100 border border-transparent border-green-700 shadow-md
                        hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700">
                    <span class="nav-link-text">Kegiatan</span>
                </a>

                <!-- Master Data Dropdown (only for admin) -->
                <div x-data="{ open: false, focusIndex: -1 }" class="relative inline-block text-left" @keydown.escape="open = false"
                    @keydown.tab="open = false">
                    <button @click="open = !open; if(open) $nextTick(() => $refs.menuitems.children[0].focus())"
                        :aria-expanded="open" aria-haspopup="true" aria-controls="master-menu"
                        class="nav-link inline-flex justify-center items-center w-full px-4 py-2 rounded-md text-sm font-medium text-green-700 bg-white border border-transparent shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700"
                        id="master-menu-button">
                        Master Data
                        <svg class="ml-2 h-5 w-5 text-green-700" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7l3-3 3 3m0 6l-3 3-3-3" />
                        </svg>
                    </button>
                    <div x-show="open" x-ref="menuitems" id="master-menu" role="menu"
                        aria-labelledby="master-menu-button"
                        class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                        x-transition
                        @keydown.arrow-down.prevent="focusIndex = (focusIndex + 1) % $refs.menuitems.children.length; $refs.menuitems.children[focusIndex].focus()"
                        @keydown.arrow-up.prevent="focusIndex = (focusIndex - 1 + $refs.menuitems.children.length) % $refs.menuitems.children[focusIndex].focus()"
                        @keydown.enter.prevent="if(focusIndex >= 0) $refs.menuitems.children[focusIndex].click()"
                        @keydown.home.prevent="focusIndex = 0; $refs.menuitems.children[0].focus()"
                        @keydown.end.prevent="focusIndex = $refs.menuitems.children.length - 1; $refs.menuitems.children[focusIndex].focus()"
                        @click.away="open = false">
                        <div class="py-1" role="none">
                            <a href="<?php echo e(route('admin.schools.index')); ?>" role="menuitem" tabindex="0"
                                class="nav-link block px-4 py-2 text-sm text-green-700 bg-white border border-transparent hover:bg-gray-100 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-green-700">Sekolah</a>
                            <a href="<?php echo e(route('admin.students.index')); ?>" role="menuitem" tabindex="0"
                                class="nav-link block px-4 py-2 text-sm text-green-700 bg-white border border-transparent hover:bg-gray-100 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-green-700">Siswa</a>
                            
                            <a href="<?php echo e(route('admin.teachers.index')); ?>" role="menuitem" tabindex="0"
                                class="nav-link block px-4 py-2 text-sm text-green-700 bg-white border border-transparent hover:bg-gray-100 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-green-700">Guru</a>
                            <a href="<?php echo e(route('admin.users.index')); ?>" role="menuitem" tabindex="0"
                                class="nav-link block px-4 py-2 text-sm text-green-700 bg-white border border-transparent hover:bg-gray-100 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-green-700">User</a>
                            <a href="<?php echo e(route('admin.grade_parameters.index')); ?>" role="menuitem" tabindex="0"
                                class="nav-link block px-4 py-2 text-sm text-green-700 bg-white border border-transparent hover:bg-gray-100 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-green-700">Parameter
                                Penilaian</a>
                            <a href="<?php echo e(route('admin.subjects.index')); ?>" role="menuitem" tabindex="0"
                                class="nav-link block px-4 py-2 text-sm text-green-700 bg-white border border-transparent hover:bg-gray-100 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-green-700">Mata
                                Pelajaran</a>
                            <a href="<?php echo e(route('admin.academic_years.index')); ?>" role="menuitem" tabindex="0"
                                class="nav-link block px-4 py-2 text-sm text-green-700 bg-white border border-transparent hover:bg-gray-100 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-green-700">Tahun
                                Akademik</a>
                            <a href="<?php echo e(route('admin.school_classes.index')); ?>" role="menuitem" tabindex="0"
                                class="nav-link block px-4 py-2 text-sm text-green-700 bg-white border border-transparent hover:bg-gray-100 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-green-700">Kelas</a>
                            <a href="<?php echo e(route('admin.class_assignments.index')); ?>" role="menuitem" tabindex="0"
                                class="nav-link block px-4 py-2 text-sm text-green-700 bg-white border border-transparent hover:bg-gray-100 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-green-700">Pengaturan
                                Kelas</a>
                            <!-- Add more master data links as needed -->
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit"
                    class="inline-block px-4 py-2 rounded-md text-sm font-medium text-green-700 bg-white border border-transparent shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700"
                    style="color: inherit">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var links = document.querySelectorAll('.nav-link');
        var currentUrl = window.location.pathname.replace(/\/$/, '');
        var masterButton = document.getElementById('master-menu-button');
        var masterActive = false;

        links.forEach(function(link) {
            // Remove previous active styles
            link.classList.remove('bg-green-300', 'border-green-700');
            link.classList.add('bg-white', 'border-transparent');
            // Normalize link href for comparison
            var linkUrl = link.getAttribute('href');
            if (!linkUrl) return;
            var normalizedLink = linkUrl.replace(window.location.origin, '').replace(/\/$/, '');
            // Active if currentUrl starts with linkUrl (for child routes)
            if (normalizedLink !== '/' && currentUrl.startsWith(normalizedLink)) {
                link.classList.remove('bg-white', 'border-transparent');
                link.classList.add('bg-green-300', 'border-green-700');
                // If this link is inside the master menu, set masterActive
                if (link.closest('#master-menu')) {
                    masterActive = true;
                }
            }
        });

        // Highlight master menu button if any child is active
        if (masterButton) {
            masterButton.classList.remove('bg-green-300', 'border-green-700');
            masterButton.classList.add('bg-white', 'border-transparent');
            if (masterActive) {
                masterButton.classList.remove('bg-white', 'border-transparent');
                masterButton.classList.add('bg-green-300', 'border-green-700');
            }
        }
    });
</script>
<script>
    // Delegated TTS handler for nav link speaker buttons
    (function () {
        if (typeof window === 'undefined') return;
        if (window.__navLayoutTtsInstalled) return; window.__navLayoutTtsInstalled = true;

        function speak(text) {
            if (!text) return;
            if ('speechSynthesis' in window) {
                try {
                    var u = new SpeechSynthesisUtterance(text);
                    u.lang = 'id-ID';
                    window.speechSynthesis.cancel();
                    window.speechSynthesis.speak(u);
                } catch (e) { console.warn('TTS failed', e); }
            } else {
                console.warn('TTS not supported in this browser');
            }
        }

        // Speak when a .nav-link receives focus (keyboard/tab navigation)
        document.addEventListener('focusin', function (e) {
            var nav = e.target.closest && e.target.closest('.nav-link');
            if (!nav) return;
            // Only speak when focus enters the anchor itself (not its child interactive elements)
            if (e.target !== nav && nav.contains(e.relatedTarget)) {
                // focus moved within the same nav; skip
                return;
            }
            var textEl = nav.querySelector('.nav-link-text');
            var text = textEl ? textEl.textContent.trim() : (nav.getAttribute('aria-label') || nav.textContent.trim());
            speak(text);
        }, false);
    })();
</script>
<?php /**PATH D:\Priv\Project\erapor\e-rapor-sederhana\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>