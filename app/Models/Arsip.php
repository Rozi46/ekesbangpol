<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    protected $table = 'db_arsip';
    protected $fillable = [
        'id',
        'code_data',
        'code_kategori',
        'judul',
        'tanggal_dokumen',
        'deskripsi',
        'akses',
        'file_path',
        'code_user',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string'; 
    
    public function kategori()
    {
        return $this->belongsTo(KategoriArsip::class, 'code_kategori', 'code_data');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'code_user', 'code_data');
    }
    
    public function arsiplog()
    {
        return $this->hasMany(ArsipLog::class, 'code_arsip', 'code_data');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'code_company', 'code_data');
    }
}
