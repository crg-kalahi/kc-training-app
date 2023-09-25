<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingParticipant extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $fillable = [
        'lname', 'fname', 'mname', 'ext_name', 'email',
        'encoded_by', 'training_id', 'is_internal',
        'position', 'is_female', 'pre_test', 'post_test',
        'municipality', 'brgy'
    ];

    public function training(){
        return $this->belongsTo(Training::class, 'training_id', 'id');
    }

    protected $appends = ['full_name', 'avatar'];

    public function getFullNameAttribute(){
        $m = $this->mname ? " ".ucfirst($this->mname[0])."." : "";
        $ext = $this->ext_name ? " ".strtoupper($this->ext_name): "";
        return ucfirst($this->lname) . ", " . ucfirst($this->fname) . $m . $ext;
    }

    public function getAvatarAttribute(){
        return "https://ui-avatars.com/api/?background=E53D00&color=fff&name=". $this->lname.'+'.$this->fname;
    }
}
