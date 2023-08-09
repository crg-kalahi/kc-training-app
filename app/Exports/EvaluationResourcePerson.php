<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class EvaluationResourcePerson implements FromQuery, WithTitle, WithHeadings, WithMapping
{
    private $id;
    
    public function __construct(string $trainingId)
    {
        $this->id = $trainingId;
    }

    public function query()
    {
        return DB::table('v2_evaluation_resource_person_view')->where('training_id', $this->id)->orderBy('area_rp_id');
    }

    public function title(): string
    {
        return 'Resource Person';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Training ID',
            'Gender',
            'RP Type',
            'Last Name',
            'First Name',
            'Middle Name',
            'Ext Name',
            'Position',
            'Evaluation Value',
            'Adj Rating',
            // 'Evaluation Total Count',
            'Evaluation Title',
        ];
    }

    public function map($row): array
    {
        $adj = "Poor";
        if($row->stat <= 1.49){ $adj = 'Poor';}
        elseif($row->stat <= 2.49){ $adj = 'Fair';}
        elseif($row->stat <= 3.49){ $adj = 'Satisfactory';}
        elseif($row->stat <= 4.49){ $adj = 'Very Satisfactory';}
        elseif($row->stat <= 5.00){ $adj = 'Outstanding';}
        $v = DB::table('key_resource_people')->where('id', $row->area_rp_id)->first();
        return [
            $row->id,
            $row->training_id,
            $row->is_female ? 'Female' : 'Male',
            $row->is_internal ? 'Internal' : 'External',
            $row->lname,
            $row->fname,
            $row->mname,
            $row->ext_name,
            $row->position,
            $row->stat,
            $adj,
            // $row->total_count,
            $v->title,
        ];
    }
}
