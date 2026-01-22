<x-app-layout>
@php
    // Expecting $student, $masters and optional $openId to be prepared by controller.
    $openId = $openId ?? request()->query('open');
    $student = $student ?? null;
    $masters = $masters ?? collect();
@endphp

    <main class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-xl font-bold">Riwayat Penilaian Siswa</h1>
                        <div class="text-sm text-gray-600">Ringkasan historis nilai siswa</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold">{{ optional($student)->name ?? 'Siswa' }}</div>
                        <div class="text-sm text-gray-600">{{ optional($student)->class_name ?? (optional(optional($student)->class)->name ?? '') }}</div>
                    </div>
                </div>

                @if($masters->isEmpty())
                    <div class="p-6 bg-yellow-50 border border-yellow-200 rounded text-yellow-800">Belum ada data nilai.</div>
                @else
                    <div class="space-y-4">
                        @foreach($masters as $m)
                            <div class="border rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm text-gray-500">Semester</div>
                                        <div class="font-semibold">{{ optional($m->academicYear)->year ? (optional($m->academicYear)->year . ' - ' . optional($m->academicYear)->semester) : 'Tahun' }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Periode</div>
                                        <div class="font-semibold">{{ $m->period_label ?? (optional($m->academicYear)->year ?? '') }}</div>
                                    </div>
                                    <div class="flex-1 px-6">
                                        <div class="grid grid-cols-3 gap-4 text-sm">
                                            <div class="bg-gray-50 p-3 rounded">
                                                <div class="text-xs text-gray-600">Kognitif</div>
                                                <div class="font-bold">{{ $m->kognitif ?? '-' }}</div>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded">
                                                <div class="text-xs text-gray-600">Motorik</div>
                                                <div class="font-bold">{{ $m->motorik ?? '-' }}</div>
                                            </div>
                                            <div class="bg-gray-50 p-3 rounded">
                                                <div class="text-xs text-gray-600">Sosial</div>
                                                <div class="font-bold">{{ $m->sosial ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button data-toggle-detail="master-{{ $m->id }}" class="px-3 py-2 bg-indigo-600 text-white rounded text-sm toggle-detail">Lihat Rincian</button>
                                    </div>
                                </div>

                                <div id="master-{{ $m->id }}" class="mt-4 hidden">
                                    <div class="mb-3 text-sm text-gray-700">Catatan: {{ $m->notes ?? '-' }}</div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs text-gray-500">Mata Pelajaran</th>
                                                    <th class="px-4 py-2 text-left text-xs text-gray-500">Periode</th>
                                                    <th class="px-4 py-2 text-left text-xs text-gray-500">Nilai</th>
                                                    <th class="px-4 py-2 text-left text-xs text-gray-500">Huruf</th>
                                                    <th class="px-4 py-2 text-left text-xs text-gray-500">Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @if($m->details && $m->details->count())
                                                    @foreach($m->details as $d)
                                                        <tr>
                                                            <td class="px-4 py-2 text-sm text-gray-700">{{ optional($d->subject)->name ?? ($d->subject_name ?? '-') }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $m->period_label ?? (optional($m->academicYear)->year ?? '-') }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $d->score ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $d->grade_letter ?? '-' }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $d->notes ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr><td colspan="5" class="px-4 py-2 text-sm text-gray-500">Belum ada rincian.</td></tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.toggle-detail').forEach(function(btn){
        btn.addEventListener('click', function(){
            var id = this.getAttribute('data-toggle-detail');
            var el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('hidden');
            this.textContent = el.classList.contains('hidden') ? 'Lihat Rincian' : 'Sembunyikan';
        });
    });
    // auto-open if requested
    try {
        var openId = '{{ $openId ?? '' }}';
        if (openId) {
            var el = document.getElementById('master-' + openId);
            if (el) {
                el.classList.remove('hidden');
                var btn = document.querySelector('[data-toggle-detail="master-' + openId + '"]');
                if (btn) btn.textContent = 'Sembunyikan';
                // scroll into view
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    } catch (e) { /* ignore */ }
});
</script>
@endpush

</x-app-layout>
