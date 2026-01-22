<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rapor Semester <?php echo e($academicYear->semester . ' - ' . $academicYear->year . ' - ' . $student->name); ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        h2,
        h4 {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .info-table {
            margin-top: 15px;
            width: 100%;
        }

        .info-table td {
            padding: 3px 6px;
        }

        .section-title {
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 5px;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }

        table.main-table th {
            background-color: #f0f0f0;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>RAPOR SUMATIF AKHIR SEMESTER <?php echo e(strtoupper($academicYear->semester)); ?></h2>

    <table class="info-table">
        <tr>
            <td style="width: 30%">Nama Peserta Didik</td>
            <td>: <?php echo e($student->name); ?></td>
            <td>Kelas</td>
            <td>: <?php echo e($schoolClass->name); ?></td>
        </tr>
        <tr>
            <td>NISN/NIS</td>
            <td>: <?php echo e($student->nisn ?? ($student->nisn ?? '-')); ?></td>
            <td>Fase</td>
            <td>: <?php echo e($fase ?? ($fase ?? '-')); ?></td>
        </tr>
        <tr>
            <td>Nama Madrasah</td>
            <td>: <?php echo e($school->name); ?></td>
            <td>Semester</td>
            <td>: <?php echo e($academicYear->semester); ?></td>
        </tr>
        <tr>
            <td>Alaamt Madrasah</td>
            <td>: <?php echo e($school->address); ?></td>
            <td>Tahun Pelajaran</td>
            <td>: <?php echo e($academicYear->year); ?></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Mata Pelajaran</th>
                <th>Nilai Akhir</th>
                <th>Capain Kompetensi Maksimum</th>
                <th>Capain Kompetensi Minimum</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="text-center"><?php echo e($i + 1); ?></td>
                    <td><?php echo e($detail->subject->name); ?></td>
                    <td class="text-center">
                        <?php echo e(isset($detail->score) ? round($detail->score) : '-'); ?>

                    </td>
                    <td>
                        <?php echo e($detail->max_competency ?? '-'); ?>

                    </td>
                    <td>
                        <?php echo e($detail->min_competency ?? '-'); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

</body>

</html>
<?php /**PATH D:\Priv\Project\erapor\e-rapor-sederhana\resources\views/report/attachmentPDF.blade.php ENDPATH**/ ?>