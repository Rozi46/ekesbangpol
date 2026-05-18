<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    protected $table = 'db_employees';
    protected $fillable = [
        'id',
        'code_data',
        'nama_pegawai',
        'nomor_ktp',
        'agama',
        'nip',
        'gender',
        'tempat_lahir',
        'tanggal_lahir',
        'code_jabatan',
        'pendidikan',
        'jurusan',
        'code_pangkat',
        'alamat',
        'email',
        'nomor_hp',
        'photo_profil',
        'status_data',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';   
    
    public function position()
    {
        return $this->belongsTo(Position::class, 'code_jabatan', 'code_data');
    }

    public function ranks()
    {
        return $this->belongsTo(Ranks::class, 'code_pangkat', 'code_data');
    }
}
