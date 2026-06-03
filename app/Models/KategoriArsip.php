<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriArsip extends Model
{
    protected $table = 'db_kategori_arsip';
    protected $fillable = [
        'id',
        'code_data',
        'nama_kategori',
        'keterangan',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string'; 
    
    public function arsip()
    {
        return $this->hasMany(Arsip::class, 'code_kategori', 'code_data');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'code_company', 'code_data');
    }
}
