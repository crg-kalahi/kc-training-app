<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Conf\OfficeRepresentative;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class OfficeRepresentativesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks in case of related tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Truncate the table to start fresh
        OfficeRepresentative::truncate();
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $json = File::get(database_path('seeders/json/office_representatives.json'));
        $data = json_decode($json, true);

        if (isset($data['RECORDS'])) {
            foreach ($data['RECORDS'] as $record) {
                OfficeRepresentative::create([
                    'id' => $record['id'],
                    'title' => $record['title'],
                    'order' => $record['order'],
                    'is_default' => $record['is_default'],
                    'created_at' => Carbon::createFromFormat('d/m/Y H:i:s', $record['created_at']),
                    'updated_at' => Carbon::createFromFormat('d/m/Y H:i:s', $record['updated_at']),
                    'deleted_at' => $record['deleted_at'] 
                        ? Carbon::createFromFormat('d/m/Y H:i:s', $record['deleted_at']) 
                        : null,
                ]);
            }
        }
    }
}
