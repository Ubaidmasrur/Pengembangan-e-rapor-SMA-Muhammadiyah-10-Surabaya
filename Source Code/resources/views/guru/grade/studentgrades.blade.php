<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nilai Siswa</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6" data-bulk-url="{{ route('grades.bulkStore') }}">
        <div class="bg-white shadow rounded-lg p-4">
            <!-- Loading overlay (hidden by default) -->
            <div id="loadingOverlay"
                class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-25">
                <div class="bg-white p-4 rounded shadow flex items-center space-x-3">
                    <svg class="h-6 w-6 text-indigo-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <div class="text-sm font-medium text-gray-700">Memuat data...</div>
                </div>
            </div>
            <!-- Page-level error container (for JS) -->
            <div id="pageErrors" class="hidden mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded"></div>
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                    {{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="academic_year_id" class="block text-sm font-medium text-gray-700">Tahun
                            Ajaran</label>
                        <select name="academic_year_id" id="academic_year_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @if (!empty($academicYears))
                                @foreach ($academicYears as $year)
                                    <option value="{{ $year->id }}"
                                        data-start_month="{{ $year->start_month ?? '' }}"
                                        data-end_month="{{ $year->end_month ?? '' }}"
                                        {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                                        {{ $year->year ?? '-' }} - {{ $year->semester ?? '-' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="master_period" class="block text-sm font-medium text-gray-700">Periode</label>
                        <select id="master_period" name="master_period"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih Bulan --</option>
                            {{-- options will be populated from the selected academic year via JS on load/change --}}
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="class_id" class="block text-sm font-medium text-gray-700">Kelas</label>
                        <select name="class_id" id="class_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="student_id" class="block text-sm font-medium text-gray-700">Pilih Siswa</label>
                        <select name="student_id" id="student_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->student_id }}"
                                    {{ request('student_id') == $student->student_id ? 'selected' : '' }}>
                                    {{ $student->student_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- add populated data button --}}
                <div class="mt-4">
                    <button id="populateDataBtn" type="button"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:ring focus:ring-blue-400 font-semibold transition"
                        aria-label="Muat data nilai siswa berdasarkan filter di atas">
                        Muat Data Nilai
                    </button>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="master_motorik" class="block text-sm font-medium text-gray-700">Motorik</label>
                        <input id="master_motorik" name="master_motorik" type="number" step="0.01" min="0"
                            max="100" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            value="{{ old('master_motorik') ?? (request('master_motorik') ?? '') }}">
                    </div>
                    <div>
                        <label for="master_kognitif" class="block text-sm font-medium text-gray-700">Kognitif</label>
                        <input id="master_kognitif" name="master_kognitif" type="number" step="0.01" min="0"
                            max="100" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            value="{{ old('master_kognitif') ?? (request('master_kognitif') ?? '') }}">
                    </div>
                    <div>
                        <label for="master_sosial" class="block text-sm font-medium text-gray-700">Sosial</label>
                        <input id="master_sosial" name="master_sosial" type="number" step="0.01" min="0"
                            max="100" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            value="{{ old('master_sosial') ?? (request('master_sosial') ?? '') }}">
                    </div>
                </div>

                <div class="mt-4">
                    <label for="master_note" class="block text-sm font-medium text-gray-700">Catatan Siswa</label>
                    <textarea id="master_note" name="master_note" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Catatan perkembangan siswa">{{ old('master_note') ?? (request('master_note') ?? '') }}</textarea>
                </div>
                
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-500">Pilih Tahun Ajaran, Kelas, dan Siswa untuk menampilkan atau
                        memperbarui data secara otomatis.</div>
                    <div class="flex items-center gap-2">
                        <button id="newGradeBtn" type="button"
                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-grade-modal' })); return false;">Tambah
                            Nilai</button>
                    </div>
                </div>

            <div class="mt-6 bg-white shadow rounded-lg p-4">
                <x-loading />
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mata Pelajaran</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nilai</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Huruf</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Catatan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fase</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody id="detailBody" class="bg-white divide-y divide-gray-200">
                            @if (isset($grades) && $grades instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $grades->count())
                                @foreach ($grades as $g)
                                        <tr data-subject-id="{{ $g->subject_id }}">
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ $g->subject_name ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ $g->score ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ $g->grade_letter ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ $g->notes ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">{{ $g->fase ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                <button type="button" class="edit-saved text-indigo-600 mr-2" data-id="{{ $g->id }}">Edit</button>
                                                <button type="button" class="remove-saved text-red-600" data-id="{{ $g->id }}">Remove</button>
                                            </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-sm text-gray-500">Belum ada nilai.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div>
                        @if (isset($grades) && $grades instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $grades->count())
                            {{ $grades->withQueryString()->links() }}
                        @endif
                    </div>
                    <div>
                        <button id="savePageBtn" type="button"
                            class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Simpan
                            Halaman</button>
                    </div>
                </div>
            </div>

            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // pending grades cache (client-side)
                        var pendingGrades = [];
                        var deletedDetailIds = [];
                        var pendingIdCounter = 0;
                        var editingPendingId = null;

                        // element refs
                        const yearSelect = document.getElementById('academic_year_id');
                        const classSelect = document.getElementById('class_id');
                        const studentSelect = document.getElementById('student_id');
                        const addGradeForm = document.getElementById('addGradeForm');
                        const detailBody = document.getElementById('detailBody');
                        const subjectSelectInModal = document.querySelector('select[name="subject_id"]');
                        const saveAllBtn = document.getElementById('saveAllBtn');
                        const savePageBtn = document.getElementById('savePageBtn');
                        const newGradeBtn = document.getElementById('newGradeBtn');
                        const masterPeriodSelect = document.getElementById('master_period');
                        // determine initial selected period from the real DOM element value (not URL param)
                        var initialMasterPeriod = null;
                        try { initialMasterPeriod = masterPeriodSelect ? (masterPeriodSelect.value || null) : null; } catch (e) { initialMasterPeriod = null; }
                        // internal flag to suppress period change handler when we programmatically
                        // update the select (prevents infinite/continuous fetch loops)
                        var _suppressPeriodChange = false;

                        // build month options from start/end specs and append to masterPeriodSelect
                        function populatePeriodsFromOption(opt) {
                            try {
                                if (!masterPeriodSelect) return;
                                // clear except placeholder
                                while (masterPeriodSelect.options.length > 1) masterPeriodSelect.remove(1);
                                if (!opt) return;
                                var start = opt.getAttribute('data-start_month') || '';
                                var end = opt.getAttribute('data-end_month') || '';
                                // helper to parse YYYY-MM or MM or MM-MM
                                function parseSpec(s) {
                                    if (!s) return null;
                                    s = String(s).trim();
                                    var now = new Date();
                                    var nowy = now.getFullYear();
                                    if (/^\d{4}-\d{2}$/.test(s)) {
                                        var parts = s.split('-');
                                        return { y: parseInt(parts[0],10), m: parseInt(parts[1],10) };
                                    }
                                    if (/^\d{1,2}$/.test(s)) {
                                        return { y: nowy, m: parseInt(s,10) };
                                    }
                                    // range in single string like '01-06' -> return special marker
                                    if (/^\d{1,2}-\d{1,2}$/.test(s)) {
                                        var p = s.split('-');
                                        return { range: true, sm: parseInt(p[0],10), em: parseInt(p[1],10), y: nowy };
                                    }
                                    return null;
                                }

                                var sp = parseSpec(start);
                                var ep = parseSpec(end);

                                // if start included a range and end empty, use that
                                if (sp && sp.range && !ep) {
                                    var sy = sp.y, sm = sp.sm, ey = sp.y, em = sp.em;
                                } else if (sp && ep && !sp.range && !ep.range) {
                                    var sy = sp.y, sm = sp.m, ey = ep.y, em = ep.m;
                                } else if (sp && ep && sp.range) {
                                    // fallback: treat sp.range as sm..em
                                    var sy = sp.y, sm = sp.sm, ey = sp.y, em = sp.em;
                                } else {
                                    // unsupported formats -> leave empty
                                    return;
                                }

                                // determine current desired selection from DOM (user/server may have set it)
                                var desiredSelection = null;
                                try { desiredSelection = masterPeriodSelect ? (masterPeriodSelect.value || initialMasterPeriod) : initialMasterPeriod; } catch (e) { desiredSelection = initialMasterPeriod; }
                                var curY = sy, curM = sm;
                                while (curY < ey || (curY === ey && curM <= em)) {
                                    var dt = new Date(curY, curM - 1, 1);
                                    var o = document.createElement('option');
                                    var val = curY + '-' + String(curM).padStart(2, '0');
                                    o.value = val;
                                    // Indonesian month names
                                    try { o.textContent = dt.toLocaleString('id', { month: 'long', year: 'numeric' }); } catch (e) { o.textContent = dt.toLocaleString(undefined, { month: 'long', year: 'numeric' }); }
                                    masterPeriodSelect.appendChild(o);
                                    // if initialMasterPeriod matches, mark selected
                                    if (desiredSelection && desiredSelection === val) o.selected = true;
                                    curM++; if (curM > 12) { curM = 1; curY++; }
                                }
                                // If no desired selection was provided and the select has generated options,
                                // default to the first available period (index 1) so client fetches include a period.
                                // This keeps UI predictable: after choosing a year the first month becomes selected.
                                try {
                                    if (!desiredSelection && masterPeriodSelect && (!masterPeriodSelect.value || masterPeriodSelect.value === '') && masterPeriodSelect.options.length > 1) {
                                        // suppress change handler while we set the selection programmatically
                                        _suppressPeriodChange = true;
                                        masterPeriodSelect.selectedIndex = 1;
                                        // small defer to ensure any downstream code reading .value sees the new value
                                        setTimeout(function() { _suppressPeriodChange = false; }, 0);
                                    }
                                } catch (e) {
                                    /* ignore DOM issues */
                                }
                            } catch (e) {
                                // ignore
                            }
                        }

                        // grade letter helper: map numeric score to letter
                        // Assumption: typical scale used here: A >= 85, B >= 70, C >= 55, D >= 40, E < 40
                        function computeGradeLetter(score) {
                            if (score === null || score === undefined || score === '' || isNaN(score)) return '';
                            var s = parseFloat(score);
                            if (isNaN(s)) return '';
                            if (s >= 85) return 'A';
                            if (s >= 70) return 'B';
                            if (s >= 55) return 'C';
                            if (s >= 40) return 'D';
                            return 'E';
                        }

                        // wire modal score -> grade_letter auto calculation
                        if (addGradeForm) {
                            var modalScoreInput = addGradeForm.querySelector('input[name="score"]');
                            var modalGradeInput = addGradeForm.querySelector('input[name="grade_letter"]');
                            if (modalScoreInput && modalGradeInput) {
                                modalScoreInput.addEventListener('input', function(e) {
                                    var val = this.value || '';
                                    var spinner = addGradeForm.querySelector('.grade-letter-spinner');
                                    // show spinner and grey-out input
                                    if (spinner) spinner.classList.remove('hidden');
                                    modalGradeInput.classList.add('bg-gray-100');
                                    // try server-side lookup first (auth route)
                                    var url = '{{ route('ajax.grade_parameters.lookup') }}' + '?score=' +
                                        encodeURIComponent(val);
                                    fetch(url, {
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    }).then(function(res) {
                                        return res.json();
                                    }).then(function(j) {
                                        if (j && j.found && j.grade_letter) modalGradeInput.value = j
                                            .grade_letter;
                                        else modalGradeInput.value = computeGradeLetter(val);
                                    }).catch(function() {
                                        modalGradeInput.value = computeGradeLetter(val);
                                    }).finally(function() {
                                        if (spinner) spinner.classList.add('hidden');
                                        modalGradeInput.classList.remove('bg-gray-100');
                                    });
                                });
                            }
                        }

                        // helper to get CSRF token (meta tag from layout)
                        function getCsrfToken() {
                            var m = document.querySelector('meta[name="csrf-token"]');
                            return m ? m.getAttribute('content') : (document.querySelector('input[name="_token"]') ? document
                                .querySelector('input[name="_token"]').value : '');
                        }

                        // loading overlay helpers
                        function showLoading() {
                            var o = document.getElementById('loadingOverlay');
                            if (o) o.classList.remove('hidden');
                        }

                        function hideLoading() {
                            var o = document.getElementById('loadingOverlay');
                            if (o) o.classList.add('hidden');
                        }

                        // page error helpers (render into #pageErrors)
                        function showError(message) {
                            var c = document.getElementById('pageErrors');
                            if (!c) {
                                alert(message);
                                return;
                            }
                            c.innerHTML = '<ul class="list-disc pl-5"><li>' + (message || 'Terjadi kesalahan') + '</li></ul>';
                            c.classList.remove('hidden');
                        }

                        function clearError() {
                            var c = document.getElementById('pageErrors');
                            if (!c) return;
                            c.innerHTML = '';
                            c.classList.add('hidden');
                        }

                        // safely populate selects when server returns data (guarded)
                        if (typeof data !== 'undefined' && Array.isArray(data) && studentSelect) {
                            data.forEach(s => {
                                const opt = document.createElement('option');
                                opt.value = s.id;
                                opt.textContent = s.name;
                                studentSelect.appendChild(opt);
                            });
                        }

                        // Add to pending array instead of immediate POST
                        if (addGradeForm) {
                            addGradeForm.addEventListener('submit', function(e) {
                                e.preventDefault();

                                var errContainer = document.getElementById('addGradeErrors');
                                if (errContainer) errContainer.innerHTML = '';

                                var fd = new FormData(this);
                                var payload = {
                                    student_id: fd.get('student_id') || null,
                                    class_id: fd.get('class_id') || null,
                                    academic_year_id: fd.get('academic_year_id') || null,
                                    subject_id: fd.get('subject_id') || null,
                                    score: fd.get('score') || null,
                                    grade_letter: fd.get('grade_letter') || null,
                                    notes: fd.get('notes') || null,
                                    fase: fd.get('fase') || null,
                                    fase_desc: fd.get('fase_desc') || null,
                                };

                                // If editing a persisted detail record, queue the update into pendingGrades
                                var detailIdInput = addGradeForm.querySelector('input[name="detail_id"]');
                                if (detailIdInput && detailIdInput.value) {
                                    var detailId = detailIdInput.value;
                                    // Build an edit-pending object and either update existing pending entry for this detail_id
                                    var editObj = {
                                        __pendingId: 'e' + detailId,
                                        detail_id: detailId,
                                        student_id: payload.student_id,
                                        class_id: payload.class_id,
                                        academic_year_id: payload.academic_year_id,
                                        subject_id: payload.subject_id,
                                        score: payload.score,
                                        grade_letter: payload.grade_letter,
                                        notes: payload.notes,
                                        fase: payload.fase,
                                        fase_desc: payload.fase_desc,
                                    };

                                    var existingIndex = pendingGrades.findIndex(function(p) { return p.__pendingId === editObj.__pendingId; });
                                    if (existingIndex >= 0) {
                                        pendingGrades[existingIndex] = editObj;
                                    } else {
                                        pendingGrades.push(editObj);
                                    }

                                    // Render a pending row so the user sees the queued change immediately,
                                    // and remove the original saved row from the table to avoid duplicate entries.
                                    try {
                                        renderPendingRow(editObj);
                                        var savedRow = detailBody.querySelector('tr[data-detail-id="' + detailId + '"]');
                                        if (savedRow) savedRow.remove();
                                    } catch (e) {}

                                    // remove the hidden input and close modal
                                    try { detailIdInput.parentNode.removeChild(detailIdInput); } catch (e) {}
                                    window.dispatchEvent(new CustomEvent('close'));
                                    updateSaveButtons();
                                    // ensure Save All button visible
                                    if (saveAllBtn) saveAllBtn.classList.remove('hidden');
                                    if (savePageBtn) savePageBtn.disabled = false;
                                    return; // skip adding a new pending row in 'create' mode
                                }

                                // basic client-side validation
                                // if (!payload.student_id || !payload.subject_id || !payload.academic_year_id || !payload
                                //     .class_id) {
                                //     if (errContainer) errContainer.innerHTML =
                                //         '<div class="text-sm text-red-600">Pastikan siswa, mata pelajaran, kelas dan tahun ajaran dipilih.</div>';
                                //     return;
                                // }

                                // prevent submit while grade_letter is still loading or empty
                                var spinner = addGradeForm.querySelector('.grade-letter-spinner');
                                var gl = addGradeForm.querySelector('input[name="grade_letter"]');
                                if ((spinner && !spinner.classList.contains('hidden')) || !gl || !gl.value || gl.value
                                    .trim() === '') {
                                    if (errContainer) errContainer.innerHTML =
                                        '<div class="text-sm text-red-600">Tunggu sampai Huruf Nilai terisi secara otomatis sebelum menyimpan.</div>';
                                    return;
                                }

                                // helper: check existing saved rows (non-pending) for duplicate subject
                                function hasExistingSavedSubject(subjectId) {
                                    if (!detailBody) return false;
                                    var row = detailBody.querySelector('tr[data-subject-id="' + subjectId + '"]');
                                    if (!row) return false;
                                    // if row has data-pending-id attribute it's a pending row; ignore
                                    return !row.hasAttribute('data-pending-id');
                                }

                                // If editing an existing pending item, update it instead of pushing a new one
                                if (editingPendingId) {
                                    var idx = pendingGrades.findIndex(function(p) {
                                        return p.__pendingId === editingPendingId;
                                    });
                                    if (idx !== -1) {
                                        // prevent duplicate subject when changing subject on edit
                                        var duplicatePending = pendingGrades.some(function(p) {
                                            return p.__pendingId !== editingPendingId && String(p
                                                .subject_id) === String(payload.subject_id);
                                        });
                                        var existingSaved = hasExistingSavedSubject(payload.subject_id);
                                        if (duplicatePending || existingSaved) {
                                            if (errContainer) errContainer.innerHTML =
                                                '<div class="text-sm text-red-600">Mata pelajaran sudah ada. Gunakan Ubah pada entri yang sesuai atau hapus entri yang ada terlebih dahulu.</div>';
                                            return;
                                        }

                                        // update pending item
                                        pendingGrades[idx].student_id = payload.student_id;
                                        pendingGrades[idx].class_id = payload.class_id;
                                        pendingGrades[idx].academic_year_id = payload.academic_year_id;
                                        pendingGrades[idx].subject_id = payload.subject_id;
                                        pendingGrades[idx].score = payload.score;
                                        pendingGrades[idx].fase = payload.fase;
                                        pendingGrades[idx].fase_desc = payload.fase_desc;
                                        pendingGrades[idx].grade_letter = payload.grade_letter;
                                        pendingGrades[idx].notes = payload.notes;

                                        // re-render pending row
                                        renderPendingRow(pendingGrades[idx]);

                                        // cleanup
                                        editingPendingId = null;

                                        // keep modal open for quick entry: show inline success, reset form, focus subject
                                        if (errContainer) errContainer.innerHTML =
                                            '<div class="text-sm text-green-600">Entri diperbarui. Anda masih dapat menambah atau mengubah nilai lain.</div>';
                                        try {
                                            addGradeForm.reset();
                                        } catch (e) {}
                                        var subj = addGradeForm.querySelector('select[name="subject_id"]');
                                        if (subj) subj.focus();
                                        if (saveAllBtn) saveAllBtn.classList.remove('hidden');
                                        if (savePageBtn) savePageBtn.disabled = false;
                                        return;
                                    }
                                }

                                // adding a new pending entry: prevent duplicates against pending list and existing saved rows
                                var duplicatePendingNew = pendingGrades.some(function(p) {
                                    return String(p.subject_id) === String(payload.subject_id);
                                });
                                var existingSavedNew = hasExistingSavedSubject(payload.subject_id);
                                if (duplicatePendingNew || existingSavedNew) {
                                    if (errContainer) errContainer.innerHTML =
                                        '<div class="text-sm text-red-600">Mata pelajaran sudah ditambahkan. Gunakan Ubah untuk memperbarui atau hapus entri sebelumnya.</div>';
                                    return;
                                }

                                // push to pending and render in table with a pending badge
                                payload.__pendingId = 'p' + (pendingIdCounter++);
                                pendingGrades.push(payload);

                                // remove empty state if present (table has 6 columns now)
                                if (detailBody) {
                                    var emptyRow = detailBody.querySelector('tr td[colspan="6"]');
                                    if (emptyRow && emptyRow.parentElement) emptyRow.parentElement.remove();
                                }

                                // render via helper
                                renderPendingRow(payload);

                                // keep modal open for quick additional entries: show inline success and reset
                                if (errContainer) errContainer.innerHTML =
                                    '<div class="text-sm text-green-600">Entri ditambahkan ke antrian (pending). Anda dapat menambah nilai lain.</div>';
                                try {
                                    addGradeForm.reset();
                                } catch (e) {}
                                var subj2 = addGradeForm.querySelector('select[name="subject_id"]');
                                if (subj2) subj2.focus();

                                // show Save All button
                                if (saveAllBtn) saveAllBtn.classList.remove('hidden');
                                // enable Save Page since we now have pending data
                                if (savePageBtn) savePageBtn.disabled = false;
                            });
                        }

                        // year/class/student change handlers (fetch functions must exist server-side routes)
                        yearSelect?.addEventListener('change', function() {
                            const y = this.value;
                            // rebuild periods for selected academic year (or clear)
                            try {
                                var sel = this.options[this.selectedIndex] || null;
                                if (sel && sel.value) populatePeriodsFromOption(sel);
                                else if (masterPeriodSelect) { while (masterPeriodSelect.options.length > 1) masterPeriodSelect.remove(1); }
                            } catch (e) {}

                            if (!y) {
                                if (classSelect) classSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
                                if (studentSelect) studentSelect.innerHTML =
                                    '<option value="">-- Pilih Siswa --</option>';
                                return;
                            }
                            // Do not auto-fetch here. Use the "Muat Data Nilai" button to load master+detail.
                            try { updateActionButtons(); updateSaveButtons(); } catch (e) {}
                        });

                        classSelect?.addEventListener('change', function() {
                            const c = this.value;
                            const y = yearSelect?.value;
                            if (!c || !y) {
                                if (studentSelect) studentSelect.innerHTML =
                                    '<option value="">-- Pilih Siswa --</option>';
                                return;
                            }
                            // Do not auto-fetch here. Use the "Muat Data Nilai" button to load master+detail.
                            try { updateActionButtons(); updateSaveButtons(); } catch (e) {}
                        });

                        // auto-submit filters form when year and class are selected (and optionally student) -- debounce
                        var _fsTimeout = null;

                        function tryAutoSubmitFilters() {
                            if (_fsTimeout) clearTimeout(_fsTimeout);
                            _fsTimeout = setTimeout(function() {
                                var y = yearSelect ? yearSelect.value : '';
                                var c = classSelect ? classSelect.value : '';
                                var s = studentSelect ? studentSelect.value : '';
                                var p = masterPeriodSelect ? masterPeriodSelect.value : '';
                                // require year, class, student and period for meaningful load
                                if (y && c && s && p) {
                                    // Do nothing automatically; user must click "Muat Data Nilai".
                                    try { updateActionButtons(); updateSaveButtons(); } catch (e) {}
                                }
                            }, 300);
                        }

                        yearSelect?.addEventListener('change', tryAutoSubmitFilters);
                        classSelect?.addEventListener('change', tryAutoSubmitFilters);
                        studentSelect?.addEventListener('change', tryAutoSubmitFilters);

                        // helper: only fetch when all four filters are chosen
                        function canFetch() {
                            try {
                                var y = yearSelect ? yearSelect.value : '';
                                var c = classSelect ? classSelect.value : '';
                                var s = studentSelect ? studentSelect.value : '';
                                var p = masterPeriodSelect ? masterPeriodSelect.value : '';
                                return !!(y && c && s && p);
                            } catch (e) {
                                return false;
                            }
                        }

                        // initialize master_period on page load: populate from selected academic year or keep empty
                        try {
                            if (yearSelect) {
                                var sel = yearSelect.options[yearSelect.selectedIndex] || null;
                                if (sel && sel.value) populatePeriodsFromOption(sel);
                                else if (masterPeriodSelect) { while (masterPeriodSelect.options.length > 1) masterPeriodSelect.remove(1); }
                            }
                        } catch (e) { /* ignore */ }

                        // enable/disable New Grade and Save Page depending on whether a student is selected
                        function updateActionButtons() {
                            const hasStudent = !!(studentSelect && studentSelect.value);
                            if (newGradeBtn) {
                                // do not fully disable the button (prevents click); use aria-disabled + visual cue so modal can open and show validation
                                if (!hasStudent) {
                                    newGradeBtn.setAttribute('aria-disabled', 'true');
                                    newGradeBtn.classList.add('opacity-50', 'cursor-not-allowed');
                                } else {
                                    newGradeBtn.removeAttribute('aria-disabled');
                                    newGradeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                                }
                            }
                            // enable Save Page when a student is selected and there are pending grades OR master inputs present
                            var masterNote = document.getElementById('master_note') ? document.getElementById('master_note')
                                .value.trim() : '';
                            var masterMotorik = document.getElementById('master_motorik') ? document.getElementById(
                                'master_motorik').value : '';
                            var masterKognitif = document.getElementById('master_kognitif') ? document.getElementById(
                                'master_kognitif').value : '';
                            var masterSosial = document.getElementById('master_sosial') ? document.getElementById(
                                'master_sosial').value : '';
                            var masterPeriod = document.getElementById('master_period') ? document.getElementById(
                                'master_period').value : '';
                            var hasMasterInput = masterNote !== '' || masterMotorik !== '' || masterKognitif !== '' ||
                                masterSosial !== '' || masterPeriod !== '';
                            if (savePageBtn) savePageBtn.disabled = !hasStudent || (pendingGrades.length === 0 && !
                                hasMasterInput);
                        }

                        // Prevent master header inputs from causing network requests.
                        // They should only affect local UI (save button enabling). Stop propagation on change events.
                        (function disableMasterInputFetches() {
                            var ids = ['master_note', 'master_motorik', 'master_kognitif', 'master_sosial',
                                'master_period'
                            ];
                            ids.forEach(function(id) {
                                var el = document.getElementById(id);
                                if (!el) return;
                                el.addEventListener('change', function(e) {
                                    // stop other change handlers from firing (defensive)
                                    try {
                                        e.stopImmediatePropagation();
                                    } catch (_) {}
                                    try {
                                        e.stopPropagation();
                                    } catch (_) {}
                                    try {
                                        updateSaveButtons();
                                    } catch (_) {}
                                });
                                // keep UI responsive when typing
                                el.addEventListener('input', function() {
                                    try {
                                        updateSaveButtons();
                                    } catch (_) {}
                                });
                            });
                            })();                        

                            // When the user actively changes the Periode select, we SHOULD fetch the
                            // master and grades for that period. The defensive handlers above stop
                            // automatic fetches from other master input changes, so add an explicit
                            // listener here for user-driven period selection.
                            (function enableMasterPeriodFetch() {
                                var mp = document.getElementById('master_period');
                                if (!mp) return;
                                mp.addEventListener('change', function(e) {
                                    // ignore programmatic updates
                                    if (typeof _suppressPeriodChange !== 'undefined' && _suppressPeriodChange) return;
                                    try {
                                        // Only update UI state when period changes; do NOT auto-fetch.
                                        try { updateActionButtons(); } catch (err) {}
                                        try { updateSaveButtons(); } catch (err) {}
                                        // Clear any previous page errors when user explicitly picks a period
                                        try { clearError(); } catch (err) {}
                                    } catch (err) {
                                        // ignore – keep UI responsive
                                    }
                                });
                            })();

                        // wire student select changes: only update UI buttons. No automatic fetching.
                        studentSelect?.addEventListener('change', function() {
                            updateActionButtons();
                            try { updateSaveButtons(); } catch (e) {}
                        });

                        // initial state: do not auto-fetch. User must click "Muat Data Nilai".
                        updateActionButtons();

                        // Single entry point: populate data button triggers master + detail fetch
                        var populateBtn = document.getElementById('populateDataBtn');
                        if (populateBtn) {
                            populateBtn.addEventListener('click', function() {
                                try {
                                    if (canFetch && typeof canFetch === 'function') {
                                        if (!canFetch()) {
                                            showError('Silakan pilih Tahun Ajaran, Periode, Kelas, dan Siswa terlebih dahulu.');
                                            return;
                                        }
                                    } else {
                                        // fallback minimal checks
                                        var y = yearSelect ? yearSelect.value : '';
                                        var c = classSelect ? classSelect.value : '';
                                        var s = studentSelect ? studentSelect.value : '';
                                        var p = masterPeriodSelect ? masterPeriodSelect.value : '';
                                        if (!(y && c && s && p)) {
                                            showError('Silakan pilih Tahun Ajaran, Periode, Kelas, dan Siswa terlebih dahulu.');
                                            return;
                                        }
                                    }
                                    clearError();
                                    fetchAndPopulateMaster();
                                    try { fetchGrades(); } catch (e) {}
                                } catch (err) {
                                    // keep UI responsive
                                }
                            });
                        }

                        // central submit function used by both Save All and Save Page (AJAX JSON)
                        function submitPendingGrades() {
                            // if no pending grades, allow submit only when master inputs are present
                            var masterNote = document.getElementById('master_note') ? document.getElementById('master_note')
                                .value.trim() : '';
                            var masterMotorik = document.getElementById('master_motorik') ? document.getElementById(
                                'master_motorik').value : '';
                            var masterKognitif = document.getElementById('master_kognitif') ? document.getElementById(
                                'master_kognitif').value : '';
                            var masterSosial = document.getElementById('master_sosial') ? document.getElementById(
                                'master_sosial').value : '';
                            var hasMasterInput = masterNote !== '' || masterMotorik !== '' || masterKognitif !== '' ||
                                masterSosial !== '';
                            if (!pendingGrades.length && !hasMasterInput) {
                                showError(
                                    'Tidak ada perubahan untuk disimpan. Gunakan Tambah Nilai untuk menambah atau isi catatan/nilai master.'
                                );
                                return;
                            }

                            showLoading();

                            var payload = {
                                grades: pendingGrades.map(function(g) {
                                    return {
                                        detail_id: g.detail_id || null,
                                        student_id: g.student_id || null,
                                        class_id: g.class_id || null,
                                        academic_year_id: g.academic_year_id || null,
                                        subject_id: g.subject_id || null,
                                        score: g.score || null,
                                        grade_letter: g.grade_letter || null,
                                        notes: g.notes || null,
                                        fase: typeof g.fase !== 'undefined' ? g.fase : null,
                                        fase_desc: typeof g.fase_desc !== 'undefined' ? g.fase_desc : null,
                                    };
                                }),
                                master_academic_year_id: yearSelect ? yearSelect.value : null,
                                master_class_id: classSelect ? classSelect.value : null,
                                master_student_id: studentSelect ? studentSelect.value : null,
                                master_note: document.getElementById('master_note') ? document.getElementById('master_note')
                                    .value : null,
                                master_motorik: document.getElementById('master_motorik') ? document.getElementById(
                                    'master_motorik').value : null,
                                master_kognitif: document.getElementById('master_kognitif') ? document.getElementById(
                                    'master_kognitif').value : null,
                                master_sosial: document.getElementById('master_sosial') ? document.getElementById(
                                    'master_sosial').value : null,
                                master_period: document.getElementById('master_period') ? document.getElementById(
                                    'master_period').value : null,
                                deleted_detail_ids: deletedDetailIds.length ? deletedDetailIds : null,
                            };

                            // If there are no pending grades but the table shows existing grades,
                            // include them in the payload so server-side validation that requires
                            // a grades array won't fail. Existing rows use data-subject-id.
                            if ((!payload.grades || payload.grades.length === 0) && detailBody) {
                                try {
                                    var existingRows = Array.from(detailBody.querySelectorAll('tr[data-subject-id]'));
                                    var existingGrades = existingRows.map(function(tr) {
                                        var subjectId = tr.getAttribute('data-subject-id') || null;
                                        var cells = tr.querySelectorAll('td');
                                        // cells currently: 0=subject,1=score,2=letter,3=notes,4=fase,5=actions
                                        var scoreText = cells[1] ? cells[1].textContent.trim() : '';
                                        var letterText = cells[2] ? cells[2].textContent.trim() : '';
                                        var notesText = cells[3] ? cells[3].textContent.trim() : '';
                                        var faseVal = cells[4] ? cells[4].textContent.trim() : (tr.getAttribute('data-fase') || null);
                                        var faseDescVal = tr.getAttribute('data-fase-desc') || null;
                                        return {
                                            student_id: yearSelect && classSelect && studentSelect ? (studentSelect
                                                .value || null) : null,
                                            class_id: classSelect ? (classSelect.value || null) : null,
                                            academic_year_id: yearSelect ? (yearSelect.value || null) : null,
                                            subject_id: subjectId,
                                            score: scoreText === '-' || scoreText === '' ? null : scoreText,
                                            grade_letter: letterText === '-' ? null : letterText,
                                            notes: notesText === '-' ? null : notesText,
                                            fase: faseVal,
                                            fase_desc: faseDescVal,
                                        };
                                    });
                                    if (existingGrades.length) payload.grades = existingGrades;
                                } catch (e) {
                                    /* ignore parsing errors */
                                }
                            }

                            var url = '{{ route('grades.bulkStore') }}';
                            clearError();
                            if (saveAllBtn) {
                                saveAllBtn.disabled = true;
                                saveAllBtn.textContent = 'Menyimpan...';
                            }
                            if (savePageBtn) savePageBtn.disabled = true;

                            fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': getCsrfToken(),
                                    },
                                    body: JSON.stringify(payload),
                                }).then(function(res) {
                                    return res.json().then(function(j) {
                                        return {
                                            ok: res.ok,
                                            status: res.status,
                                            body: j
                                        };
                                    });
                                })
                                .then(function(r) {
                                    hideLoading();
                                    if (r.ok) {
                                        // success - reload to show saved rows
                                        window.location.reload();
                                    } else {
                                        var m = (r.body && r.body.error ? r.body.error : (r.body && r.body.message ? r.body
                                            .message : 'Gagal menyimpan'));
                                        showError(m);
                                        if (saveAllBtn) {
                                            saveAllBtn.disabled = false;
                                            saveAllBtn.textContent = 'Simpan Semua';
                                        }
                                        if (savePageBtn) savePageBtn.disabled = false;
                                    }
                                }).catch(function(err) {
                                    hideLoading();
                                    showError('Gagal menyimpan data. Periksa koneksi atau coba lagi.');
                                    if (saveAllBtn) {
                                        saveAllBtn.disabled = false;
                                        saveAllBtn.textContent = 'Simpan Semua';
                                    }
                                    if (savePageBtn) savePageBtn.disabled = false;
                                });
                        }

                        // fetch existing master record for selected student/class/year and populate master inputs
                        function fetchAndPopulateMaster() {
                            var s = studentSelect ? studentSelect.value : '';
                            var c = classSelect ? classSelect.value : '';
                            var y = yearSelect ? yearSelect.value : '';
                            var saveBtn = document.getElementById('savePageBtn');

                            if (!s || !c || !y) {
                                // selection incomplete — do not clear user inputs here.
                                // Keep existing master input values untouched. Update save button label.
                                if (saveBtn) saveBtn.textContent = 'Simpan Halaman';
                                return;
                            }

                            // Read the currently selected period first (do not clear it) so it will
                            // be included in the fetch URL. Clear other master inputs to avoid stale/merged values.
                            var mp = '';
                            try {
                                mp = document.getElementById('master_period') ? document.getElementById('master_period').value : '';
                            } catch (e) { mp = ''; }

                            // Clear master inputs (but keep master_period untouched so the user's
                            // selection is preserved while fetching).
                            try {
                                if (document.getElementById('master_note')) document.getElementById('master_note').value = '';
                                if (document.getElementById('master_motorik')) document.getElementById('master_motorik').value = '';
                                if (document.getElementById('master_kognitif')) document.getElementById('master_kognitif').value = '';
                                if (document.getElementById('master_sosial')) document.getElementById('master_sosial').value = '';
                            } catch (e) {
                                /* ignore DOM errors */
                            }

                            var url = '{{ route('guru.grading.getMasterRecord') }}' + '?student_id=' + encodeURIComponent(s) +
                                '&class_id=' + encodeURIComponent(c) + '&academic_year_id=' + encodeURIComponent(y);
                            try {
                                if (mp) url += '&period=' + encodeURIComponent(mp);
                            } catch (e) {}
                            showLoading();
                            console.debug('fetchAndPopulateMaster ->', url);
                            showLoading();
                            fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': getCsrfToken()
                                }
                            }).then(function(res) {
                                return res.json();
                            }).then(function(json) {
                                console.debug('getMasterRecord response:', json);
                                if (json && json.found) {
                                    // populate returned master values (overwrite cleared inputs)
                                    if (document.getElementById('master_note')) document.getElementById('master_note')
                                        .value = json.notes || '';
                                    if (document.getElementById('master_motorik')) document.getElementById(
                                        'master_motorik').value = (json.motorik !== undefined && json.motorik !==
                                        null) ? json.motorik : '';
                                    if (document.getElementById('master_kognitif')) document.getElementById(
                                        'master_kognitif').value = (json.kognitif !== undefined && json.kognitif !==
                                        null) ? json.kognitif : '';
                                    if (document.getElementById('master_sosial')) document.getElementById(
                                        'master_sosial').value = (json.sosial !== undefined && json.sosial !==
                                        null) ? json.sosial : '';
                                    if (document.getElementById('master_period')) {
                                        try {
                                            _suppressPeriodChange = true;
                                        } catch (e) {}
                                        document.getElementById('master_period').value = (json.period !== undefined && json.period !== null) ? json.period : '';
                                        // clear the suppression in next tick
                                        setTimeout(function() { try { _suppressPeriodChange = false; } catch (e) {} }, 0);
                                    }
                                    if (saveBtn) saveBtn.textContent = 'Perbarui Halaman';
                                } else {
                                    // no master found — inputs remain cleared
                                    if (saveBtn) saveBtn.textContent = 'Simpan Halaman';
                                }
                                hideLoading();
                            }).catch(function() {
                                hideLoading();
                                // ignore fetch errors silently
                            });
                        }

                        // fetch grades via AJAX and render into the table body
                        function fetchGrades() {
                            console.log('fetchGrades called');
                            var s = studentSelect ? studentSelect.value : '';
                            var c = classSelect ? classSelect.value : '';
                            var y = yearSelect ? yearSelect.value : '';
                            if (!s || !c || !y) return;
                            var url = '{{ route('guru.grading.getGrades') }}' + '?student_id=' + encodeURIComponent(s) +
                                '&class_id=' + encodeURIComponent(c) + '&academic_year_id=' + encodeURIComponent(y);
                            try {
                                var mpEl = document.getElementById('master_period');
                                var mp2 = mpEl ? mpEl.value : '';
                                // If select has no value but options exist, use the first real option so period is included
                                if ((!mp2 || mp2 === '') && mpEl && mpEl.options && mpEl.options.length > 1) {
                                    try {
                                        mp2 = mpEl.options[1].value;
                                        mpEl.value = mp2;
                                    } catch (err) {
                                        // ignore
                                    }
                                }
                                if (mp2) url += '&period=' + encodeURIComponent(mp2);
                            } catch (e) {}
                            showLoading();
                            console.debug('fetchGrades ->', url);
                            showLoading();
                            fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }).then(function(res) {
                                return res.json();
                            }).then(function(json) {
                                console.debug('getGrades response:', json);
                                console.log('Grades rows:', (json && json.grades) ? json.grades.length : 0);
                                var tbody = document.getElementById('detailBody');
                                if (!tbody) return;
                                tbody.innerHTML = '';
                                var rows = (json && json.grades) ? json.grades : [];
                                if (!rows.length) {
                                    // show friendly message and also log to console for debugging
                                    tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-2 text-sm text-gray-500">Belum ada nilai.</td></tr>';
                                    console.warn('No grades returned for', { studentId: s, classId: c, yearId: y, period: (document.getElementById('master_period') ? document.getElementById('master_period').value : '') });
                                    // surface a small page-level message (non-intrusive)
                                    try { var pe = document.getElementById('pageErrors'); if (pe) { pe.innerHTML = '<ul class="list-disc pl-5"><li class="text-sm">Tidak ada baris detail yang cocok untuk filter saat ini.</li></ul>'; pe.classList.remove('hidden'); } } catch (e) {}
                                    return;
                                }
                                rows.forEach(function(r) {
                                    var tr = document.createElement('tr');
                                    tr.setAttribute('data-subject-id', r.subject_id);
                                    tr.setAttribute('data-detail-id', r.id);
                                    // store fase fields on the DOM so edit modal can read them
                                    tr.setAttribute('data-fase', r.fase ?? '');
                                    tr.setAttribute('data-fase-desc', r.fase_desc ?? '');
                                    tr.innerHTML = '<td class="px-4 py-2 text-sm text-gray-700">' + (r
                                            .subject_name || '-') + '</td>' +
                                        '<td class="px-4 py-2 text-sm text-gray-700">' + (r.score ?? '-') +
                                        '</td>' +
                                        '<td class="px-4 py-2 text-sm text-gray-700">' + (r.grade_letter ??
                                            '-') + '</td>' +
                                        '<td class="px-4 py-2 text-sm text-gray-700">' + (r.notes ?? '-') +
                                        '</td>' +
                                        '<td class="px-4 py-2 text-sm text-gray-700">' + (r.fase ?? '-') + '</td>' +
                                        '<td class="px-4 py-2 text-sm text-gray-700">' +
                                        '<button type="button" class="edit-saved text-indigo-600 mr-2" data-id="' + r.id + '">Edit</button>' +
                                        '<button type="button" class="remove-saved text-red-600" data-id="' + r.id + '">Remove</button>' +
                                        '</td>';
                                    tbody.appendChild(tr);
                                });
                            }).catch(function() {
                                /* ignore */
                            }).finally(function() {
                                hideLoading();
                            });
                        }

                        // Save Page triggers direct submit of pending grades
                        if (savePageBtn) {
                            savePageBtn.addEventListener('click', function() {
                                submitPendingGrades();
                            });
                        }

                        // (subjects list removed) modal will not prefill subject automatically

                        // Save All pending grades handler (submit via centralized function)
                        if (saveAllBtn) {
                            saveAllBtn.addEventListener('click', function() {
                                submitPendingGrades();
                            });
                        }

                        // helper to re-render / update pending row DOM
                        function renderPendingRow(item) {
                            if (!detailBody) return;
                            var existing = detailBody.querySelector('tr[data-pending-id="' + item.__pendingId + '"]');
                            var sel = document.querySelector('select[name="subject_id"]');
                            var subjectName = item.subject_id;
                            if (sel) {
                                var o = Array.from(sel.options).find(function(opt) {
                                    return opt.value == item.subject_id;
                                });
                                if (o) subjectName = o.textContent;
                            }
                            var html = '<td class="px-4 py-2 text-sm text-gray-700">' + subjectName +
                                ' <span class="text-xs text-yellow-600">(pending)</span></td>' +
                                '<td class="px-4 py-2 text-sm text-gray-700">' + (item.score ?? '-') + '</td>' +
                                '<td class="px-4 py-2 text-sm text-gray-700">' + (item.grade_letter ?? '-') + '</td>' +
                                '<td class="px-4 py-2 text-sm text-gray-700">' + (item.notes ?? '-') + '</td>' +
                                '<td class="px-4 py-2 text-sm text-gray-700">' + (item.fase ?? '-') + '</td>' +
                                '<td class="px-4 py-2 text-sm text-gray-700">' +
                                '<button type="button" class="edit-pending text-indigo-600 mr-2" data-id="' + item.__pendingId +
                                '">Edit</button>' +
                                '<button type="button" class="remove-pending text-red-600" data-id="' + item.__pendingId +
                                '">Remove</button>' +
                                '</td>';
                            if (existing) existing.innerHTML = html;
                            else {
                                var tr = document.createElement('tr');
                                tr.setAttribute('data-pending-id', item.__pendingId);
                                tr.innerHTML = html;
                                detailBody.prepend(tr);
                            }
                        }

                        // event delegation for edit/remove buttons inside the detail body
                        if (detailBody) {
                            detailBody.addEventListener('click', function(e) {
                                var t = e.target;

                                // Remove a pending (client-only) row
                                if (t.matches('.remove-pending')) {
                                    var id = t.getAttribute('data-id');
                                    pendingGrades = pendingGrades.filter(function(p) {
                                        return p.__pendingId !== id;
                                    });
                                    var r = detailBody.querySelector('tr[data-pending-id="' + id + '"]');
                                    if (r) r.remove();
                                    updateSaveButtons();
                                    return;
                                }

                                // Edit a pending (client-only) row
                                if (t.matches('.edit-pending')) {
                                    var id = t.getAttribute('data-id');
                                    var item = pendingGrades.find(function(p) {
                                        return p.__pendingId === id;
                                    });
                                    if (!item) return;
                                    editingPendingId = id;
                                    // populate modal with pending values
                                    var sel = addGradeForm.querySelector('select[name="subject_id"]');
                                    if (sel) sel.value = item.subject_id || '';
                                    addGradeForm.querySelector('input[name="score"]').value = item.score || '';
                                    // set grade letter from stored value but prefer recalculation from score
                                    var gradeEl = addGradeForm.querySelector('input[name="grade_letter"]');
                                    if (gradeEl) {
                                        var lookupUrl = '{{ route('ajax.grade_parameters.lookup') }}' + '?score=' + encodeURIComponent(item.score || '');
                                        var spinner = addGradeForm.querySelector('.grade-letter-spinner');
                                        if (spinner) spinner.classList.remove('hidden');
                                        gradeEl.classList.add('bg-gray-100');
                                        fetch(lookupUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                            .then(function(res) { return res.json(); })
                                            .then(function(j) {
                                                if (j && j.found && j.grade_letter) gradeEl.value = j.grade_letter;
                                                else gradeEl.value = computeGradeLetter(item.score);
                                            }).catch(function() {
                                                gradeEl.value = computeGradeLetter(item.score);
                                            }).finally(function() {
                                                if (spinner) spinner.classList.add('hidden');
                                                gradeEl.classList.remove('bg-gray-100');
                                            });
                                    }
                                    addGradeForm.querySelector('textarea[name="notes"]').value = item.notes || '';
                                    // ensure any detail_id hidden input removed (editing pending)
                                    var hid = addGradeForm.querySelector('input[name="detail_id"]');
                                    if (hid) hid.parentNode.removeChild(hid);
                                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-grade-modal' }));
                                    return;
                                }

                                // Edit a saved (persisted) row
                                if (t.matches('.edit-saved')) {
                                    var id = t.getAttribute('data-id');
                                    if (!id) return;
                                    var row = detailBody.querySelector('tr[data-detail-id="' + id + '"]');
                                    if (!row) return;
                                    // populate modal with values from the row (best-effort)
                                    editingPendingId = null;
                                    var sel = addGradeForm.querySelector('select[name="subject_id"]');
                                    var subjectText = row.children[0] ? row.children[0].textContent.trim() : '';
                                    if (sel && subjectText) {
                                        for (var i = 0; i < sel.options.length; i++) {
                                            if (sel.options[i].textContent.trim() === subjectText) {
                                                sel.value = sel.options[i].value;
                                                break;
                                            }
                                        }
                                    }
                                    var scoreText = row.children[1] ? row.children[1].textContent.trim() : '';
                                    addGradeForm.querySelector('input[name="score"]').value = (scoreText && scoreText !== '-') ? scoreText : '';
                                    var notesText = row.children[3] ? row.children[3].textContent.trim() : '';
                                    addGradeForm.querySelector('textarea[name="notes"]').value = (notesText && notesText !== '-') ? notesText : '';
                                    // populate fase and fase_desc from row data attributes
                                    try {
                                        var faseVal = row.getAttribute('data-fase') || '';
                                        var faseSel = addGradeForm.querySelector('select[name="fase"]');
                                        if (faseSel) faseSel.value = faseVal;
                                        var faseDescVal = row.getAttribute('data-fase-desc') || '';
                                        var faseDescEl = addGradeForm.querySelector('textarea[name="fase_desc"]');
                                        if (faseDescEl) faseDescEl.value = faseDescVal;
                                    } catch (e) {}
                                    // set hidden detail_id so submit will perform an update
                                    var existing = addGradeForm.querySelector('input[name="detail_id"]');
                                    if (!existing) { var hid = document.createElement('input'); hid.type = 'hidden'; hid.name = 'detail_id'; addGradeForm.appendChild(hid); existing = hid; }
                                    existing.value = id;
                                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-grade-modal' }));
                                    return;
                                }

                                // Remove a saved (persisted) row
                                if (t.matches('.remove-saved')) {
                                    var id = t.getAttribute('data-id');
                                    if (!id) return;
                                        // queue deletion and remove row from DOM. Actual delete will happen on Save Page/Save All
                                        try {
                                            if (!deletedDetailIds.includes(id)) deletedDetailIds.push(id);
                                            var r = detailBody.querySelector('tr[data-detail-id="' + id + '"]');
                                            if (r) r.remove();
                                            updateSaveButtons();
                                            if (saveAllBtn) saveAllBtn.classList.remove('hidden');
                                            if (savePageBtn) savePageBtn.disabled = false;
                                        } catch (e) { showError('Gagal menandai baris untuk dihapus.'); }
                                    return;
                                }
                            });
                        }

                        function updateSaveButtons() {
                            if (saveAllBtn) {
                                if (pendingGrades.length) saveAllBtn.classList.remove('hidden');
                                else saveAllBtn.classList.add('hidden');
                            }
                            // same rule as updateActionButtons: allow saving page if student selected and either pending grades or master inputs
                            var masterNote = document.getElementById('master_note') ? document.getElementById('master_note')
                                .value.trim() : '';
                            var masterMotorik = document.getElementById('master_motorik') ? document.getElementById(
                                'master_motorik').value : '';
                            var masterKognitif = document.getElementById('master_kognitif') ? document.getElementById(
                                'master_kognitif').value : '';
                            var masterSosial = document.getElementById('master_sosial') ? document.getElementById(
                                'master_sosial').value : '';
                            var masterPeriod = document.getElementById('master_period') ? document.getElementById(
                                'master_period').value : '';
                            var hasMasterInput = masterNote !== '' || masterMotorik !== '' || masterKognitif !== '' ||
                                masterSosial !== '' || masterPeriod !== '';
                            if (savePageBtn) savePageBtn.disabled = !(studentSelect && studentSelect.value && (pendingGrades
                                .length > 0 || hasMasterInput));
                        }
                    });
                </script>
            @endpush

            {{-- Add Grade Modal --}}
            <x-modal name="add-grade-modal" focusable>
                <form id="addGradeForm" method="POST" action="{{ route('grades.store') }}" class="p-6">
                    @csrf
                    <h3 class="text-lg font-medium">Add Grade</h3>

                    <div id="addGradeErrors" class="mb-4"></div>

                    <input type="hidden" name="student_id" value="{{ optional($selectedStudent)->id ?? '' }}">
                    <input type="hidden" name="class_id"
                        value="{{ optional($selectedClass)->id ?? (request('class_id') ?? '') }}">
                    <input type="hidden" name="academic_year_id"
                        value="{{ optional($selectedAcademicYear)->id ?? (request('academic_year_id') ?? '') }}">
                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <div>
                            <label class="block text-sm">Subject</label>
                            <select name="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach ($subjects as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm">Score</label>
                            <input type="number" name="score" min="0" max="100"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        {{-- Periode removed from detail entry: period is stored on master student_grade record --}}

                        <div>
                            <label class="block text-sm">Grade Letter</label>
                            <!-- read-only: auto-computed from score; wrapped so we can show a spinner while loading -->
                            <div class="relative mt-1">
                                <input type="text" name="grade_letter"
                                    class="block w-full border-gray-300 rounded-md shadow-sm pr-10" readonly>
                                <!-- spinner (hidden by default) -->
                                <div class="grade-letter-spinner hidden absolute inset-y-0 right-2 flex items-center">
                                    <svg class="h-5 w-5 text-gray-500 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm">Fase</label>
                            <select name="fase" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required>
                                <option value="">-- Pilih Fase --</option>
                                <option value="A"> A </option>
                                <option value="B"> B </option>
                                <option value="C"> C </option>
                                <option value="D"> D </option>
                                <option value="E"> E </option>
                                <option value="F"> F </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm">Fase Deskripsi</label>
                            <textarea name="fase_desc" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm">Notes</label>
                            <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" x-on:click="$dispatch('close')"
                                class="px-3 py-2 bg-gray-200 rounded">Cancel</button>
                            <!-- submission will dispatch close events from the submit handler when successful -->
                            <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded">Save</button>
                        </div>
                    </div>
                </form>
            </x-modal>

            @if ($errors->any())
                <script>
                    // open modal if there are validation errors
                    window.addEventListener('DOMContentLoaded', function() {
                        window.dispatchEvent(new CustomEvent('open-modal', {
                            detail: 'add-grade-modal'
                        }));
                    });
                </script>
            @endif

</x-app-layout>
