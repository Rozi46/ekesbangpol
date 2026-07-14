<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequests extends Model
{
    protected $table = 'db_leave_requests';
    protected $fillable = [
        'id',
        'code_data',
        'nomor_pengajuan',
        'code_cuti',
        'tanggal_mulai',
        'tanggal_akhir',
        'jumlah_hari',
        'alasan',
        'alamat_selama_cuti',
        'status',
        'file_pendukung',
        'code_user',
        'code_company',
        'created_at',
        'updated_at'
    ];
}
