<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveLogs extends Model
{
    protected $table = 'db_leave_logs';
    protected $fillable = [
        'id',
        'code_data',
        'code_pengajuan',
        'aksi',
        'keterangan',
        'code_user',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function leaveRequests()
    {
        return $this->belongsTo(LeaveRequests::class, 'code_pengajuan', 'code_data');
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
