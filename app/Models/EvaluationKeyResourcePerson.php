<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationKeyResourcePerson extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $fillable = [
        'evaluation_id', 'rp_id', 'area_rp_id', 'stat'
    ];

    public function evaluation(){
        return $this->belongsTo(EvaluationTraining::class, 'evaluation_id', 'id');
    }

    public function person(){
        return $this->hasOne(TrainingResourcePerson::class, 'id', 'rp_id');
    }
}
