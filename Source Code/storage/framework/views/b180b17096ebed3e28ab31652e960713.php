<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <main aria-labelledby="dashboard-title" role="main" tabindex="-1">
        <div class="py-8 bg-gray-100 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistic Cards -->
                <section class="mb-8">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-400">
                                <!-- Icon placeholder -->
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800">Total Siswa</div>
                <div class="text-lg font-bold text-gray-700"><?php echo e($totalStudents ?? 0); ?></div>
                            </div>
                        </div>
            <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-400">
                                <!-- Icon placeholder -->
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800">Laporan Selesai</div>
                <div class="text-lg font-bold text-gray-700"><?php echo e($reportsCompleted ?? 0); ?></div>
                            </div>
                        </div>
            <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-400">
                                <!-- Icon placeholder -->
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800">Menunggu Evaluasi</div>
                <div class="text-lg font-bold text-gray-700"><?php echo e($reportsPending ?? 0); ?></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Daftar Siswa -->
                <section class="mb-8">
                    <h2 class="font-semibold text-lg text-gray-800 mb-4">Daftar Siswa</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white rounded-xl shadow p-6 flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-purple-400"><?php echo e(strtoupper(substr($s->name,0,1))); ?></span>
                                <span class="font-semibold text-gray-800"><?php echo e($s->name); ?></span>
                            </div>
                            <div class="space-y-2 mt-2">
                                <div class="h-2 rounded bg-blue-300" style="width: <?php echo e(($studentProgress[$s->id] ?? 0)); ?>%"></div>
                            </div>
                            <div class="flex gap-2 mt-4">
                                <a href="#" class="px-4 py-2 rounded bg-blue-50 text-blue-700 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-400">Lihat</a>
                                <a href="#" class="px-4 py-2 rounded bg-green-50 text-green-700 font-semibold focus:outline-none focus:ring-2 focus:ring-green-400">Nilai</a>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>

                <!-- Riwayat Penilaian (Top 10) -->
                <section class="mb-8">
                    <h2 class="font-semibold text-lg text-gray-800 mb-4">Riwayat Penilaian</h2>
                    <div class="bg-white rounded-xl shadow p-6">
                        <table class="min-w-full text-sm text-gray-700">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-3 px-4 text-left font-semibold">Nama Siswa</th>
                                    <th class="py-3 px-4 text-left font-semibold">Tanggal</th>
                                    <th class="py-3 px-4 text-left font-semibold">Mata Pelajaran</th>
                                    <th class="py-3 px-4 text-left font-semibold">Nilai</th>
                                    <th class="py-3 px-4 text-left font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b">
                                    <td class="py-2 px-4"><?php echo e($r->student_name); ?></td>
                                    <td class="py-2 px-4"><?php echo e(\Carbon\Carbon::parse($r->created_at)->format('Y-m-d')); ?></td>
                                    <td class="py-2 px-4"><?php echo e($r->subject_name); ?></td>
                                    <td class="py-2 px-4"><?php echo e($r->score ?? '-'); ?></td>
                                    <td class="py-2 px-4">
                                        <a href="<?php echo e(route('guru.history.grades', ['student' => $r->student_id])); ?>?open=<?php echo e($r->student_grade_id); ?>" class="text-sm text-teal-700">Lihat</a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\Priv\Project\erapor\e-rapor-sederhana\resources\views/dashboard/teacher.blade.php ENDPATH**/ ?>