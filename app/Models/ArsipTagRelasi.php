<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArsipTag extends Model
{
    protected $table = 'db_arsip_tag_relasi';
    protected $fillable = [
        'id',
        'code_data',
        'code_arsip',
        'code_arsiptags',
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

    public function arsiptag()
    {
        return $this->belongsTo(ArsipTag::class, 'code_arsiptags', 'code_data');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'code_company', 'code_data');
    }
}
