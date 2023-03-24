<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationTraining extends Model
{
    use HasFactory, SoftDeletes, UUID;
    
    protected $fillable = [
        'training_id', 'is_female', 'office_rep_id'
    ];

    public function keyTraining(){
        return $this->hasMany(EvaluationKeyArea::class, 'evaluation_id', 'id');
    }

    public function keyResourcePerson(){
        return $this->hasMany(EvaluationKeyResourcePerson::class, 'evaluation_id', 'id');
    }

    public function keyLearning(){
        return $this->hasMany(EvaluationKeyLearning::class, 'evaluation_id', 'id');
    }
    
}
