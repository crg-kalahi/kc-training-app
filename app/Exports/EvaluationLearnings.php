<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class EvaluationLearnings implements FromQuery, WithTitle, WithHeadings, WithMapping
{
    private $id;
    
    public function __construct(string $trainingId)
    {
        $this->id = $trainingId;
    }

    public function query()
    {
        return DB::table('evaluation_learning_view')->whereNotNull('answer')->where('training_id', $this->id)->orderBy('learning_id');
    }

    public function title(): string
    {
        return 'Learnings';
    }

    public function headings(): array
    {
        return [
            'ID',
            'learning ID',
            'Question',
            'Answer'
        ];
    }

    public function map($row): array
    {
        $v = DB::table('learnings')->where('id', $row->learning_id)->first();
        return [
            $row->id,
            $row->learning_id,
            $v->title,
            $row->answer
        ];
    }
}
