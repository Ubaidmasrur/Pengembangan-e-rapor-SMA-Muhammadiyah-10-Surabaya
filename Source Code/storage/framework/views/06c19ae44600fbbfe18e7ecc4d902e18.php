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
    
    <main class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <header class="mb-6">
                <h1 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Laporan Hasil Belajar (Guru)</h1>
            </header>

            <section class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" action="<?php echo e(route('guru.report.index')); ?>"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="text-sm text-gray-600">Tahun Ajaran</label>
                        <select name="academic_year" class="mt-1 block w-full rounded border-gray-200">
                            <option value="">Semua</option>
                            <?php $__currentLoopData = $academicYears ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ay->id); ?>"
                                    <?php echo e((string) request('academic_year') === (string) $ay->id ? 'selected' : ''); ?>>
                                    <?php echo e($ay->year); ?> - <?php echo e($ay->semester); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Semester</label>
                        <select name="semester" class="mt-1 block w-full rounded border-gray-200">
                            <option value="">Semua</option>
                            <option value="Ganjil" <?php echo e(request('semester') === 'Ganjil' ? 'selected' : ''); ?>>Ganjil
                            </option>
                            <option value="Genap" <?php echo e(request('semester') === 'Genap' ? 'selected' : ''); ?>>Genap
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Kelas</label>
                        <select name="class_id" class="mt-1 block w-full rounded border-gray-200">
                            <option value="">Semua</option>
                            <?php $__currentLoopData = $classes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>"
                                    <?php echo e((string) request('class_id') === (string) $class->id ? 'selected' : ''); ?>>
                                    <?php echo e($class->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Cari</button>
                        <a href="<?php echo e(route('guru.report.index')); ?>"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Reset</a>
                    </div>
                </form>
            </section>

            <section class="bg-white rounded-lg shadow p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="text-left text-sm text-gray-600">
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">Nama Siswa</th>
                                <th class="px-4 py-2">NIS</th>
                                <th class="px-4 py-2">Kelas</th>
                                <th class="px-4 py-2">Tahun Ajaran</th>
                                <th class="px-4 py-2">Semester</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php $__empty_1 = true; $__currentLoopData = $reports ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($reports->firstItem() + $index); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($report->student_name ?? '-'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($report->nis ?? '-'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($report->class_name ?? '-'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($report->academic_year ?? '-'); ?>

                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo e($report->semester ?? '-'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <?php if(!empty($report->has_report)): ?>
                                                <a href="<?php echo e(route('guru.report.preview', $report->sample_id)); ?>"
                                                    target="_blank"
                                                    class="px-3 py-1 bg-green-600 text-white rounded text-xs">Lihat
                                                    Rapor</a>
                                                <a href="<?php echo e(route('guru.report.export.new', $report->sample_id)); ?>"
                                                    target="_blank"
                                                    class="px-3 py-1 bg-purple-600 text-white rounded text-xs">Export
                                                    Rapor
                                                </a>
                                            <?php else: ?>
                                                <button type="button" disabled
                                                    class="px-3 py-1 bg-gray-300 text-gray-600 rounded text-xs">No
                                                    Report</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">Data tidak
                                        ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <?php if(method_exists($reports, 'links')): ?>
                        <?php echo e($reports->appends(request()->query())->links()); ?>

                    <?php endif; ?>
                </div>
            </section>
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
<?php /**PATH D:\Priv\Project\erapor\e-rapor-sederhana\resources\views/report/teacher.blade.php ENDPATH**/ ?>