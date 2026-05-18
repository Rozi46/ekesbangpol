<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    // protected $connection = 'mysql';
    // protected $connection = 'pgsql';
    protected $table = 'db_activity';
    protected $fillable = [
        'id',
        'code_data',
        'code_user',
        'activity',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    // Jika id adalah UUID/string
    // protected $keyType = 'string';
    
    // Jika tabel tidak memiliki timestamp
    // public $timestamps = false;
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Jika ada relasi bisa ditambahkan di sini    
    public function user()
    {
        return $this->belongsTo(User::class, 'code_user', 'code_data');
    } 
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'code_company', 'code_data');
    }
}

