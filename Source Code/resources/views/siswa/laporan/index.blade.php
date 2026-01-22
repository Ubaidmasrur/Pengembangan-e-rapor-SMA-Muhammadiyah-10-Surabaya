<x-app-layout>
    <main class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-xl font-bold">Daftar Laporan Penilaian</h1>
                        <div class="text-sm text-gray-600">Semua laporan penilaian Anda</div>
                    </div>
                    <form method="GET" class="flex items-center gap-2">
                        <select name="academic_year_id" class="border rounded p-2">
                            <option value="">Semua Tahun</option>
                            @foreach($years as $y)
                                <option value="{{ $y->id }}" {{ (string)$y->id === (string)($yearId ?? '') ? 'selected' : '' }}>{{ $y->year }} - {{ $y->semester }}</option>
                            @endforeach
                        </select>
                        <button class="px-3 py-2 bg-indigo-600 text-white rounded">Filter</button>
                    </form>
                </div>

                @if($masters->count())
                    <div class="space-y-3">
                        @foreach($masters as $m)
                            <div class="border rounded p-3 flex items-center justify-between">
                                <div>
                                    <div class="font-semibold">{{ $m->academicYear?->year ?? 'Tahun' }} - {{ $m->academicYear?->semester ?? '' }}</div>
                                    <div class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($m->notes ?? 'Tidak ada catatan', 100) }}</div>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('student.grades', ['open' => $m->id]) }}" class="px-3 py-2 bg-green-600 text-white rounded text-sm">Lihat</a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">{{ $masters->links() }}</div>
                @else
                    <div class="text-sm text-gray-500">Belum ada laporan.</div>
                @endif
            </div>
        </div>
    </main>
</x-app-layout>
