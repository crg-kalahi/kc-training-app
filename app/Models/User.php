<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, UUID, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'mname',
        'ext_name',
        'id_number',
        'email',
        'password',
        'username',
        'division',
        'section',
        'mobile_no',
        'google2fa_secret',
        'mfa_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['full_name', 'avatar'];

    public function getFullNameAttribute(){
        $m = $this->mname ? " ".ucfirst($this->mname[0])."." : "";
        $ext = $this->ext_name ? " ".strtoupper($this->ext_name): "";
        return ucfirst($this->lname) . ", " . ucfirst($this->fname) . $m . $ext;
    }

    public function getAvatarAttribute(){
        return "https://ui-avatars.com/api/?background=3066BE&color=fff&name=". $this->lname.'+'.$this->fname;
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'model_has_permissions', 'model_id', 'permission_id');
    }
}
