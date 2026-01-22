<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('student_grades') && Schema::hasColumn('student_grades', 'period')) {
            // Convert existing enum month names (if any) to canonical YYYY-MM using academic_years.year
            // and then change the column to VARCHAR(7). We do this in a DB-driver-aware way.
            $driver = DB::getDriverName();

            // helper: map Indonesian month name to month number
            $months = [
                'Januari' => '01','Februari' => '02','Maret' => '03','April' => '04','Mei' => '05','Juni' => '06',
                'Juli' => '07','Agustus' => '08','September' => '09','Oktober' => '10','November' => '11','Desember' => '12',
            ];

            if ($driver === 'mysql') {
                // First, alter the column so we can write canonical YYYY-MM values without truncation
                DB::statement("ALTER TABLE `student_grades` MODIFY COLUMN `period` VARCHAR(7) NULL");

                // Now convert existing enum month names (if any) to canonical YYYY-MM using academic_years.year
                DB::beginTransaction();
                try {
                    $rows = DB::table('student_grades')
                        ->select('id', 'period', 'academic_year_id')
                        ->whereNotNull('period')
                        ->get();

                    foreach ($rows as $row) {
                        $period = $row->period;
                        if (!is_string($period) || strlen($period) === 0) {
                            continue;
                        }

                        // If already in YYYY-MM form, skip
                        if (preg_match('/^\d{4}-\d{2}$/', $period)) {
                            continue;
                        }

                        // Try to map month name to month number
                        if (isset($months[$period])) {
                            // Load academic year to derive the year (e.g. "2023/2024")
                            $ay = DB::table('academic_years')->where('id', $row->academic_year_id)->first();
                            if ($ay && is_string($ay->year)) {
                                // academic year stored like "2023/2024" — take the first year as start
                                if (preg_match('/^(\d{4})(?:\/|\\\-|\s)*(\d{4})?$/', $ay->year, $m)) {
                                    $startYear = $m[1];
                                    $monthNum = $months[$period];

                                    // Determine whether the month belongs to the start year or next year
                                    // Heuristic: if month number (1-12) is >= start_month of academic year we keep startYear
                                    // If academic_years.start_month is set, use it; otherwise assume academic year starts in July (07)
                                    $startMonthSpec = DB::table('academic_years')->where('id', $row->academic_year_id)->value('start_month');
                                    $ayStartMonth = 7; // default July
                                    if (is_string($startMonthSpec) && preg_match('/^(\d{4}-)?(\d{2})$/', $startMonthSpec, $mm)) {
                                        $ayStartMonth = intval($mm[count($mm)-1]);
                                    } elseif (is_string($startMonthSpec) && preg_match('/^(\d{2})-(\d{2})$/', $startMonthSpec, $mm2)) {
                                        $ayStartMonth = intval($mm2[1]);
                                    }

                                    $monthInt = intval($monthNum);
                                    $yearForPeriod = $startYear;
                                    if ($monthInt < $ayStartMonth) {
                                        // month falls into the next calendar year
                                        $yearForPeriod = (string)(intval($startYear) + 1);
                                    }

                                    $canonical = sprintf('%s-%02d', $yearForPeriod, $monthInt);
                                    DB::table('student_grades')->where('id', $row->id)->update(['period' => $canonical]);
                                }
                            }
                        }
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    // If anything fails, rethrow so migration fails visibly
                    throw $e;
                }
            } elseif ($driver === 'sqlite') {
                // SQLite: complex ALTERs are not trivial; attempt best-effort data conversion only
                $rows = DB::table('student_grades')
                    ->select('id', 'period', 'academic_year_id')
                    ->whereNotNull('period')
                    ->get();
                foreach ($rows as $row) {
                    $period = $row->period;
                    if (!is_string($period) || strlen($period) === 0) {
                        continue;
                    }
                    if (preg_match('/^\d{4}-\d{2}$/', $period)) {
                        continue;
                    }
                    if (isset($months[$period])) {
                        $ay = DB::table('academic_years')->where('id', $row->academic_year_id)->first();
                        if ($ay && is_string($ay->year) && preg_match('/^(\d{4})(?:\/|\\\-|\s)*(\d{4})?$/', $ay->year, $m)) {
                            $startYear = $m[1];
                            $monthNum = $months[$period];
                            $monthInt = intval($monthNum);
                            $ayStartMonth = 7;
                            $startMonthSpec = DB::table('academic_years')->where('id', $row->academic_year_id)->value('start_month');
                            if (is_string($startMonthSpec) && preg_match('/^(\d{4}-)?(\d{2})$/', $startMonthSpec, $mm)) {
                                $ayStartMonth = intval($mm[count($mm)-1]);
                            } elseif (is_string($startMonthSpec) && preg_match('/^(\d{2})-(\d{2})$/', $startMonthSpec, $mm2)) {
                                $ayStartMonth = intval($mm2[1]);
                            }
                            $yearForPeriod = $startYear;
                            if ($monthInt < $ayStartMonth) {
                                $yearForPeriod = (string)(intval($startYear) + 1);
                            }
                            $canonical = sprintf('%s-%02d', $yearForPeriod, $monthInt);
                            DB::table('student_grades')->where('id', $row->id)->update(['period' => $canonical]);
                        }
                    }
                }

                // NOTE: Changing column type in SQLite requires table rebuild; we leave the type change to the developer
            } else {
                // Other drivers: try Laravel change() if available, but first attempt data conversion
                $rows = DB::table('student_grades')
                    ->select('id', 'period', 'academic_year_id')
                    ->whereNotNull('period')
                    ->get();
                foreach ($rows as $row) {
                    $period = $row->period;
                    if (!is_string($period) || strlen($period) === 0) {
                        continue;
                    }
                    if (preg_match('/^\d{4}-\d{2}$/', $period)) {
                        continue;
                    }
                    if (isset($months[$period])) {
                        $ay = DB::table('academic_years')->where('id', $row->academic_year_id)->first();
                        if ($ay && is_string($ay->year) && preg_match('/^(\d{4})(?:\/|\\\-|\s)*(\d{4})?$/', $ay->year, $m)) {
                            $startYear = $m[1];
                            $monthNum = $months[$period];
                            $monthInt = intval($monthNum);
                            $ayStartMonth = 7;
                            $startMonthSpec = DB::table('academic_years')->where('id', $row->academic_year_id)->value('start_month');
                            if (is_string($startMonthSpec) && preg_match('/^(\d{4}-)?(\d{2})$/', $startMonthSpec, $mm)) {
                                $ayStartMonth = intval($mm[count($mm)-1]);
                            } elseif (is_string($startMonthSpec) && preg_match('/^(\d{2})-(\d{2})$/', $startMonthSpec, $mm2)) {
                                $ayStartMonth = intval($mm2[1]);
                            }
                            $yearForPeriod = $startYear;
                            if ($monthInt < $ayStartMonth) {
                                $yearForPeriod = (string)(intval($startYear) + 1);
                            }
                            $canonical = sprintf('%s-%02d', $yearForPeriod, $monthInt);
                            DB::table('student_grades')->where('id', $row->id)->update(['period' => $canonical]);
                        }
                    }
                }

                try {
                    Schema::table('student_grades', function (Blueprint $table) {
                        $table->string('period', 7)->nullable()->change();
                    });
                } catch (\Exception $e) {
                    // ignore - user may install doctrine/dbal to run the change
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_grades') && Schema::hasColumn('student_grades', 'period')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                // Attempt to convert any YYYY-MM back to month names (best-effort)
                $mapping = [
                    '01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April','05' => 'Mei','06' => 'Juni',
                    '07' => 'Juli','08' => 'Agustus','09' => 'September','10' => 'Oktober','11' => 'November','12' => 'Desember',
                ];

                DB::beginTransaction();
                try {
                    $rows = DB::table('student_grades')->select('id', 'period')->whereNotNull('period')->get();
                    foreach ($rows as $row) {
                        $period = $row->period;
                        if (preg_match('/^(\d{4})-(\d{2})$/', $period, $m)) {
                            $mon = $m[2];
                            if (isset($mapping[$mon])) {
                                DB::table('student_grades')->where('id', $row->id)->update(['period' => $mapping[$mon]]);
                            }
                        }
                    }

                    DB::statement("ALTER TABLE `student_grades` MODIFY COLUMN `period` ENUM('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember') NULL");
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            } elseif ($driver === 'sqlite') {
                // sqlite: no-op
            } else {
                try {
                    Schema::table('student_grades', function (Blueprint $table) {
                        // If Doctrine is available this will work
                        $table->enum('period', ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'])->nullable()->change();
                    });
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }
    }
};
