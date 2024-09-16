<?php

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{

    protected $fillable = ['name', 'guard_name'];

    protected $translatable = ['public_name'];
    
}
