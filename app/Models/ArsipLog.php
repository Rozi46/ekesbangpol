<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipLog extends Model
{
    protected $table = 'db_arsip_log';
    protected $fillable = [
        'id',
        'code_data',
        'code_arsip',
        'aksi',
        'code_user',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string'; 
    
    public function arsip()
    {
        return $this->belongsTo(Arsip::class, 'code_arsip', 'code_data');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'code_user', 'code_data');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'code_company', 'code_data');
    }
}
