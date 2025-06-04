<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EvaluationTraining extends Model implements Auditable
{
    use HasFactory, SoftDeletes, UUID;
    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = [
        'training_id', 'sex', 'office_rep_id', 'participants_id'
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
