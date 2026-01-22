<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Rapor Semester <?php echo e($academicYear->semester . ' - ' . $academicYear->year . ' - ' . $student->name); ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .header-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .header-text {
            text-align: center;
            line-height: 1.4;
        }

        .info-table,
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .info-table td {
            padding: 4px;
        }

        table {
            margin-bottom: 20px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .signature {
            border-bottom: 2px solid #000;
            display: inline-block;
            padding-bottom: 2px;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 15%;">
                <?php if($school->logo_path ?? false): ?>
                    <img src="<?php echo e(public_path('storage/' . $school->logo_path)); ?>" class="header-logo" alt="Logo">
                <?php else: ?>
                    <img src="<?php echo e(public_path('default-logo.png')); ?>" class="header-logo" alt="Logo">
                <?php endif; ?>
            </td>
            <td class="header-text">
                <h4>LAPORAN HASIL BELAJAR SUMATIF TENGAH SEMESTER GASAL</h4>
                <h2><?php echo e($school->name); ?></h2>
                <h4><?php echo e($school->address); ?></h4>
                <h4>TAHUN PELAJARAN <?php echo e($academicYear->year); ?></h4>
            </td>
        </tr>
    </table>

    <hr>

    <table class="info-table">
        <tr>
            <td style="width: 25%">Nama Peserta Didik</td>
            <td>: <?php echo e($student->name); ?></td>
        </tr>
        <tr>
            <td>Nomor Induk</td>
            <td>: <?php echo e($student->nisn); ?></td>
        </tr>
        <tr>
            <td>Kelas / Fase</td>
            <td>: <?php echo e($schoolClass->name); ?> / <?php echo e($fase); ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 50%">Mata Pelajaran</th>
                <th style="width: 5%">KKTP</th>
                <th style="width: 5%">Nilai</th>
                <th style="width: 25%">Kriteria Ketuntasan</th>
                <th style="width: 10%">KET</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $kktp = $detail->subject->min_grade ?? 75;
                    $score = round($detail->score ?? 0);
                    $status = $score >= $kktp ? 'Sudah Mencapai KKTP' : 'Belum Mencapai KKTP';
                ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td style="text-align: left"><?php echo e($detail->subject->name); ?></td>
                    <td><?php echo e($kktp); ?></td>
                    <td><b><?php echo e($score); ?></b></td>
                    <td><?php echo e($status); ?></td>
                    <td></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <?php
                $total = $details->sum('score');
                $avg = $details->avg('score');
            ?>
            <tr>
                <td colspan="3" style="text-align: center;"><strong>Jumlah Nilai Hasil Belajar</strong></td>
                <td style="text-align: center;"><strong><?php echo e(round($total)); ?></strong></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center;"><strong>Nilai Rata-rata</strong></td>
                <td style="text-align: center;"><strong><?php echo e(round($avg, 2)); ?></strong></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center;"><strong>Peringkat/Ranking</strong></td>
                <td style="text-align: center;"><strong>-</strong></td>
                <td colspan="2" style="text-align: left;"><strong>dari <?php echo e($totalStudents); ?> Peserta Didik</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <strong>KKTP = Kriteria Ketercapaian Tujuan Pembelajaran</strong>

    <br><br>

    <table width="100%">
        <tr>
            <td width="33%" class="text-center">Orang Tua/Wali<br><br><br><br>
                <strong class="signature">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
            </td>
            <td width="33%" class="text-center">
                Mengetahui,<br>
                Kepala Madrasah<br><br><br><br>
                <strong
                    class="signature"><?php echo e($school->principal_name ?? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'); ?></strong><br>
                NIP/NUPTK/NBM
            </td>
            <td width="33%" class="text-center">
                Paciran, <?php echo e(\Carbon\Carbon::now()->translatedFormat('d F Y')); ?><br>
                Wali Kelas<br><br><br><br>
                <strong class="signature"><?php echo e($homeroomName); ?></strong><br>
                NIP/NUPTK/NBM. <?php echo e($homeroomNip); ?>

            </td>
        </tr>
    </table>
</body>

</html>
<?php /**PATH D:\Priv\Project\erapor\e-rapor-sederhana\resources\views/report/summaryPDF.blade.php ENDPATH**/ ?>