<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $fillable = ['name', 'guard_name'];

    const USER_LOGIN = 'Guest - Login';    
    const STAFF_LOGIN = 'STAFF - Login';

    const USER_ROLE = 'guest';
    const STAFF_ROLE = 'staff-admin';
}
