<div class="mb-4 tts-component" aria-hidden="false">
    <div class="flex items-center gap-2">
        <button id="tts-read" type="button" class="px-3 py-1 rounded bg-teal-600 text-white text-sm">Baca Halaman</button>
        <button id="tts-pause" type="button" class="px-3 py-1 rounded bg-gray-200 text-sm">Jeda / Lanjut</button>
        <button id="tts-stop" type="button" class="px-3 py-1 rounded bg-red-100 text-red-700 text-sm">Stop</button>
        <label for="tts-voice" class="text-sm ml-2 sr-only">Pilih Suara</label>
        <select id="tts-voice" class="rounded border-gray-300 px-2 py-1 text-sm" aria-label="Pilih suara"></select>
        <label for="tts-rate" class="text-sm ml-2">Kecepatan</label>
        <input id="tts-rate" type="range" min="0.5" max="2" step="0.1" value="1" class="w-32" aria-label="Kecepatan baca">
    </div>
    <div id="tts-status" class="sr-only" aria-live="polite"></div>

    <script>
        (function(){
            const readBtn = document.getElementById('tts-read');
            const pauseBtn = document.getElementById('tts-pause');
            const stopBtn = document.getElementById('tts-stop');
            const voiceSelect = document.getElementById('tts-voice');
            const rateInput = document.getElementById('tts-rate');
            const statusEl = document.getElementById('tts-status');

            let voices = [];
            let utterance = null;

            function setStatus(s){ statusEl.textContent = s; }

            function loadVoices(){
                voices = speechSynthesis.getVoices() || [];
                voiceSelect.innerHTML = '';
                voices.forEach((v, i) => {
                    const opt = document.createElement('option');
                    opt.value = i;
                    opt.textContent = `${v.name} (${v.lang})`;
                    voiceSelect.appendChild(opt);
                });
                // restore saved voice/rate if present
                const savedVoice = localStorage.getItem('tts_voice_index');
                const savedRate = localStorage.getItem('tts_rate');
                if (savedVoice && voices[savedVoice]) voiceSelect.value = savedVoice;
                else {
                    const idx = voices.findIndex(v => /id|indonesia/i.test(v.lang) || /indonesia/i.test(v.name));
                    if (idx >= 0) voiceSelect.value = idx;
                }
                if (savedRate) rateInput.value = savedRate;
            }

            function gatherText(){
                // Prefer an explicit default TTS element if present
                const defaultEl = document.querySelector('[data-tts-default="true"]');
                const sectionOrder = ['profile', 'progress', 'reports', 'activities'];

                if (defaultEl && defaultEl.innerText.trim().length > 2) {
                    // Start with default text, then append structured sections (if any) to provide a fuller briefing
                    const parts = [defaultEl.innerText.trim()];
                    sectionOrder.forEach((name) => {
                        const sel = document.querySelector('[data-tts-section="' + name + '"]');
                        if (sel && sel.innerText.trim().length > 2) {
                            // avoid duplicating if default already contains the same text (simple containment check)
                            const stext = sel.innerText.trim();
                            if (!parts[0].includes(stext)) parts.push(stext);
                        }
                    });
                    return parts.join('\n');
                }

                // If there are structured TTS sections (no explicit default), read them in order
                const collected = [];
                sectionOrder.forEach((name) => {
                    const el = document.querySelector('[data-tts-section="' + name + '"]');
                    if (el && el.innerText.trim().length > 2) collected.push(el.innerText.trim());
                });
                if (collected.length) return collected.join('\n');

                // Fallback: gather sensible headings and paragraphs from main content
                const parts = [];
                const title = document.querySelector('#dashboard-title') || document.querySelector('#page-title');
                if (title) parts.push(title.innerText.trim());
                document.querySelectorAll('#main-content h1, #main-content h2, #main-content h3, #main-content p, #main-content li, #main-content caption').forEach(el => {
                    const t = el.innerText.trim(); if (t && t.length>2) parts.push(t);
                });
                return parts.join('. ');
            }

            function readPage(){
                if (!('speechSynthesis' in window)) { setStatus('TTS tidak tersedia di browser ini.'); return; }
                const text = gatherText();
                if (!text) { setStatus('Tidak ada teks untuk dibaca.'); return; }
                stop();
                utterance = new SpeechSynthesisUtterance(text);
                const v = voices[voiceSelect.value]; if (v) utterance.voice = v;
                const rate = parseFloat(rateInput.value) || 1.0;
                utterance.rate = rate;
                // persist preferences
                try { localStorage.setItem('tts_voice_index', voiceSelect.value); localStorage.setItem('tts_rate', rate); } catch(e){}
                utterance.onend = () => setStatus('Selesai');
                speechSynthesis.speak(utterance); setStatus('Sedang membaca...');
            }

            // Keyboard shortcut: Alt+Shift+R to read the page
            document.addEventListener('keydown', (ev) => {
                if (ev.altKey && ev.shiftKey && (ev.key === 'R' || ev.key === 'r')) {
                    ev.preventDefault(); readPage();
                }
            });

            function pauseResume(){
                if (!('speechSynthesis' in window)) return;
                if (speechSynthesis.speaking && !speechSynthesis.paused) { speechSynthesis.pause(); setStatus('Dijeda'); }
                else if (speechSynthesis.paused) { speechSynthesis.resume(); setStatus('Dilanjutkan'); }
            }

            function stop(){ if (!('speechSynthesis' in window)) return; speechSynthesis.cancel(); utterance=null; setStatus(''); }

            readBtn.addEventListener('click', readPage);
            pauseBtn.addEventListener('click', pauseResume);
            stopBtn.addEventListener('click', stop);

            // store selections when changed
            voiceSelect.addEventListener('change', () => { try { localStorage.setItem('tts_voice_index', voiceSelect.value); } catch(e){} });
            rateInput.addEventListener('change', () => { try { localStorage.setItem('tts_rate', rateInput.value); } catch(e){} });

            if ('speechSynthesis' in window) {
                loadVoices();
                window.speechSynthesis.onvoiceschanged = loadVoices;
            } else {
                setStatus('TTS tidak tersedia');
            }

            // expose for testing and section reading
            function readSection(name) {
                const sel = document.querySelector('[data-tts-section="' + name + '"]');
                if (!sel) return false;
                const text = sel.innerText.trim();
                if (!text) return false;
                stop();
                utterance = new SpeechSynthesisUtterance(text);
                const v = voices[voiceSelect.value]; if (v) utterance.voice = v;
                utterance.rate = parseFloat(rateInput.value) || 1.0;
                utterance.onend = () => setStatus('Selesai');
                speechSynthesis.speak(utterance); setStatus('Sedang membaca...');
                return true;
            }

            window.__tts = { readPage, pauseResume, stop, readSection };
        })();
    </script>
</div>
