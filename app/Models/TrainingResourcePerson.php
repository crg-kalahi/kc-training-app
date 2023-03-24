<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingResourcePerson extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $fillable = [
        'lname', 'fname', 'mname', 'ext_name',
        'topic', 'encoded_by', 'training_id', 'is_internal',
        'position', 'is_female'
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
        return "https://ui-avatars.com/api/?background=random&color=fff&name=". $this->getFullNameAttribute();
    }
}
