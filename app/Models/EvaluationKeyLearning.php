<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluationKeyLearning extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $fillable = [
        'evaluation_id', 'learning_id', 'answer'
    ];
}
