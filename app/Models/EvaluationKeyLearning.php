<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EvaluationKeyLearning extends Model implements Auditable
{
    use HasFactory, SoftDeletes, UUID;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'evaluation_id', 'learning_id', 'answer'
    ];
}
