<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Laporan - {{ $student->name ?? '-' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Raport Hasil Belajar</h2>
        <div>{{ $academicYear->year ?? '' }} - {{ $academicYear->semester ?? '' }}</div>
    </div>

    <div>
        <strong>Nama:</strong> {{ $student->name ?? '-' }}<br/>
        <strong>NISN:</strong> {{ $student->nisn ?? '-' }}<br/>
        <strong>Kelas:</strong> {{ $schoolClass->name ?? '-' }}
    </div>

    <hr />

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Nilai</th>
                <th>Huruf</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ optional($d->subject)->name ?? '-' }}</td>
                    <td>{{ $d->score }}</td>
                    <td>{{ $d->grade_letter }}</td>
                    <td>{{ $d->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
