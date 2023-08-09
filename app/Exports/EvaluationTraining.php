<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class EvaluationTraining implements FromQuery, WithTitle, WithHeadings, WithMapping
{
    private $id;
    
    public function __construct(string $trainingId)
    {
        $this->id = $trainingId;
    }

    public function query()
    {
        return DB::table('v2_evaluation_training_view')->where('training_id', $this->id)->orderBy('area_training_id');
    }

    public function title(): string
    {
        return 'Key Area';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Key: ID',
            'Key: Title',
            'Poor',
            'Fair',
            'Satisfactory',
            'Very Satisfactory',
            'Outstanding',
            'Score',
            'Adj Rating'
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
        return [
            $row->id,
            $row->area_training_id,
            $row->title,
            $row->poor,
            $row->fair,
            $row->satisfactory,
            $row->very_satisfactory,
            $row->outstanding,
            $row->stat,
            $adj,
        ];
    }
}
