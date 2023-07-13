<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrainingParticipantExport implements FromQuery, WithHeadings, WithMapping
{
    private $id;
    
    public function __construct(string $trainingId)
    {
        $this->id = $trainingId;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return DB::table('training_participants')
            ->select('lname', 'fname', 'mname', 'ext_name', 'pre_test', 'post_test', 'is_female', 'is_internal', 'email')
            ->where('training_id', $this->id)
            ->whereNull('deleted_at')
            ->orderBy('lname'); 
    }

    public function headings(): array
    {
        return [
            'Last Name',
            'First Name',
            'Middle Name',
            'Ext Name',
            'email',
            'Pre-Test',
            'Post-Test',
            'SEX',
            'is Internal',
        ];
    }

    public function map($row): array
    {
        return [
            $row->lname,
            $row->fname,
            $row->mname,
            $row->ext_name,
            $row->email,
            $row->pre_test,
            $row->post_test,
            $row->is_female ? 'Female' : 'Male',
            $row->is_internal ? 'Internal' : 'External',
        ];
    }
}
