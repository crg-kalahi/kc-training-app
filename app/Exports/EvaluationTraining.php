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
        return DB::table('evaluation_training_view')->where('training_id', $this->id)->orderBy('order');
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
            'Score'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->area_training_id,
            $row->title,
            $row->stat
        ];
    }
}
