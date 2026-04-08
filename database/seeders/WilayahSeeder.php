<?php
// database/seeders/WilayahSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    /**
     * Import data wilayah dari file CSV.
     * Letakkan 4 file CSV di folder database/data/:
     *   - provinces.csv
     *   - regencies.csv
     *   - districts.csv
     *   - villages.csv
     *
     * Jalankan: php artisan db:seed --class=WilayahSeeder
     */
    public function run(): void
    {
        $path = database_path('data');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->importCsv("{$path}/provinces.csv",  'provinces',  ['id', 'name']);
        $this->importCsv("{$path}/regencies.csv",  'regencies',  ['id', 'province_id', 'name']);
        $this->importCsv("{$path}/districts.csv",  'districts',  ['id', 'regency_id',  'name']);
        $this->importCsv("{$path}/villages.csv",   'villages',   ['id', 'district_id', 'name']);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function importCsv(string $file, string $table, array $columns): void
    {
        if (!file_exists($file)) {
            $this->command->error("File tidak ditemukan: {$file}");
            return;
        }

        DB::table($table)->truncate();

        $handle = fopen($file, 'r');
        fgetcsv($handle, 0, ';'); // skip header

        $batch = [];
        $total = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < count($columns)) continue;

            $record = array_combine($columns, array_slice($row, 0, count($columns)));
            // Trim whitespace & strip BOM
            foreach ($record as &$val) {
                $val = trim($val, " \t\n\r\0\x0B\xEF\xBB\xBF\"");
            }
            $batch[] = $record;
            $total++;

            if (count($batch) >= 1000) {
                DB::table($table)->insertOrIgnore($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DB::table($table)->insertOrIgnore($batch);
        }

        fclose($handle);
        $this->command->info("  ✓ {$table}: {$total} baris");
    }
}