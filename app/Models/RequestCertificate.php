<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class RequestCertificate extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'training_id', 'training_participant_id', 'is_approve'
    ];

    public function trainingParticipants(){
        return $this->hasOne(TrainingParticipant::class, 'id', 'training_participant_id');
    }

    public function training(){
        return $this->hasOne(Training::class, 'id', 'training_id');
    }
    
}
