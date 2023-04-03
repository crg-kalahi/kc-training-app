<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EvaluationExport implements WithMultipleSheets
{
    use Exportable;

    protected $trainingId;

    public function __construct(string $trainingId)
    {
        $this->trainingId = $trainingId;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new EvaluationResourcePerson($this->trainingId);
        $sheets[] = new EvaluationLearnings($this->trainingId);
        return $sheets;
    }
}
