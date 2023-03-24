<?php

namespace App\Models\Conf;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeRepresentative extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'order', 'is_default'];
}
