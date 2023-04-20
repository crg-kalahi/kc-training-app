<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function getEvaluationStatusAttribute(){
        $eval = $this->evaluations;
        $evaluationStats = [1,2,3,4,5];
        $trainingKeyId = explode(",",$this->key_trainings);
        $highestPercent = 0;
        $evalStatFinal = null;
        $keyAreas = $this->evaluationKeyTrainings;
        $status = ['Poor', 'Fair', 'Satisfactory', 'V-Satisfactory', 'Excellent'];
        
        if(count($eval)){
            $aveStat = [0,0,0,0,0];
            foreach($trainingKeyId as $key){
                $filt_by_area = collect($keyAreas)->filter(function($obj) use ($key){
                    return $obj['area_training_id'] == $key;
                });
                $statCollect = [0,0,0,0,0];
                foreach($evaluationStats as $index => $stat){
                    $count = count($filt_by_area->filter(function($obj) use ($stat){
                        return $obj['stat'] == $stat;
                    }));
                    $ave = ($count / count($eval)) * 100;
                    $statCollect[$index] = $ave;
                }
                foreach($statCollect as $i=>$stat1){
                    $aveStat[$i] += $stat1;
                }
            }
            foreach($aveStat as $index => $stat){
                $tmp = ($stat / count($trainingKeyId));
                if($tmp >= $highestPercent){
                    $highestPercent = $tmp;
                    $evalStatFinal = $index;
                }
            }
        }
        
        return $evalStatFinal != null ? $status[$evalStatFinal] : 'No Evaluation';
    }

    public function getEvaluationLearningsAttribute(){
        
    }
}
