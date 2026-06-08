<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlasifikasiArsip extends Model
{
    protected $table = 'db_klasifikasi_arsip';
    protected $fillable = [
        'id',
        'code_data',
        'code_klasifikasi',
        'nama_klasifikasi',
        'deskripsi',
        'retensi_aktif',
        'retensi_inaktif',
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
