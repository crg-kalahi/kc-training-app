<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachments extends Model
{
    use HasFactory, SoftDeletes, UUID;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'training_id', 
        'name', 
        'description',
        'type',
        'file_path'
    ];

}
