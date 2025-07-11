<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Training extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $fillable = [
        'title', 'venue', 'date_from', 'date_to', 'encoded_by', 'key_trainings', 'key_learnings', 'key_rp'
    ];

    public function participants(){
        return $this->hasMany(TrainingParticipant::class, 'training_id', 'id')
            ->orderBy('lname', 'ASC')
            ->orderBy('fname', 'ASC');    
    }
    
    public function encodedBy(){
        return $this->belongsTo(User::class, 'encoded_by', 'id');
    }

    public function resourcePersons(){
        return $this->hasMany(TrainingResourcePerson::class, 'training_id', 'id');
    }

    public function facilitators_(){
        return $this->hasMany(EventFacilitator::class, 'training_id', 'id');
    }

    public function facilitators(){
        return $this->hasManyThrough(User::class, EventFacilitator::class, 'training_id', 'id', 'id', 'user_id');
    }

    public function evaluations(){
        return $this->hasMany(EvaluationTraining::class, 'training_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function evaluationKeyTrainings(){
        return $this->hasManyThrough(EvaluationKeyArea::class, EvaluationTraining::class, 'training_id', 'evaluation_id');
    }

    protected $appends = ['evaluation_status'];

    public function getEvaluationStatusAttribute()
    {
        $eval = $this->evaluations;
        if (!count($eval)) {
            return 'Not Yet Evaluated';
        }

        $trainingKeyId = explode(",", $this->key_trainings);
        $keyAreas = $this->evaluationKeyTrainings;
        $status = ['Poor', 'Fair', 'Satisfactory', 'Very Satisfactory', 'Outstanding'];
        $evaluationStats = [1, 2, 3, 4, 5];

        $arrAve = [];
        foreach ($trainingKeyId as $key) {
            $filt_by_area = collect($keyAreas)->filter(function ($obj) use ($key) {
                return $obj['area_training_id'] == $key;
            });

            $totalCountParticipant = 0;
            $totalCountParticipantStat = 0;
            $arr = [0, 0, 0, 0, 0];

            foreach ($evaluationStats as $index => $stat) {
                $filterByStat = $filt_by_area->filter(function ($obj) use ($stat) {
                    return $obj['stat'] == $stat;
                });

                // The count should be just the count of items
                $count = $filterByStat->count();
                $totalCountParticipant += $count;
                $totalCountParticipantStat += $count * $stat;

                // Avoid division by zero
                $arr[$index] = $totalCountParticipant ? ($totalCountParticipantStat / $totalCountParticipant) : 0;
            }

            $arrAve[] = collect($arr)->max();
        }

        $avg = collect($arrAve)->avg();

        if ($avg <= 1.49) return 'Poor';
        if ($avg <= 2.49) return 'Fair';
        if ($avg <= 3.49) return 'Satisfactory';
        if ($avg <= 4.49) return 'Very Satisfactory';
        if ($avg <= 5.00) return 'Outstanding';

        return 'No Evaluation';
    }

    public function getEvaluationLearningsAttribute(){
        
    }
}
