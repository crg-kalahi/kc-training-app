<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationKeyArea extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $fillable = [
        'evaluation_id', 'area_training_id', 'stat'
    ];

    public function evaluation(){
        return $this->belongsTo(EvaluationTraining::class, 'evaluation_id', 'id');
    }
}
