<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipTag extends Model
{
    protected $table = 'db_arsip_tags';
    protected $fillable = [
        'id',
        'code_data',
        'nama_tag',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string'; 

    public function company()
    {
        return $this->belongsTo(Company::class, 'code_company', 'code_data');
    }
}
