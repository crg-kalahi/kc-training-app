<?php

namespace App\Exports;

use App\Models\TrainingParticipant;
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

    public function query()
    {
        return TrainingParticipant::query()
            ->where('training_id', $this->id)
            ->whereNull('deleted_at')
            ->orderBy('lname');
    }

    public function headings(): array
    {
        return [
            'has_evaluated',
            'Last Name',
            'First Name',
            'Middle Name',
            'Ext Name',
            'Municipality',
            'Barangay',
            'Position',
            'Email',
            'Pre-Test',
            'Post-Test',
            'SEX',
            'Is Internal',
        ];
    }

    public function map($participant): array
    {
        return [
            $participant->has_evaluated ? 'Yes' : 'No',
            strtoupper($participant->lname),
            strtoupper($participant->fname),
            $participant->mname ? strtoupper($participant->mname) : "",
            $participant->ext_name,
            $participant->municipality,
            $participant->brgy,
            $participant->position,
            $participant->email,
            $participant->pre_test,
            $participant->post_test,
            $participant->is_female ? 'Female' : 'Male',
            $participant->is_internal ? 'Internal' : 'External',
        ];
    }
}
