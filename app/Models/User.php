<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    
    protected $table = 'db_users';
    protected $fillable = [
        'id',
        'code_data',
        'full_name',
        'email',
        'password',
        'phone_number',
        'level',
        'image',
        'status_data',
        'key_token',
        'tipe_user',
        'tipe_login',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $hidden = [
       'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function activity()
    {
        return $this->hasMany(Activity::class, 'code_user', 'code_data');
    } 

    public function company()
    {
        return $this->belongsTo(Company::class, 'code_company', 'code_data');
    }

    public function levelAdmin()
    {
        return $this->belongsTo(LevelAdmin::class, 'level', 'code_data');
    }
}
