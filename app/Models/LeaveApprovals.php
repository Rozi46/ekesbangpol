<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveApprovals extends Model
{  
    protected $table = 'db_leave_approvals';
    protected $fillable = [
        'id',
        'code_data',
        'code_pengajuan',
        'level',
        'code_appove',
        'status',
        'catatan',
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
        return $this->belongsTo(leaveRequests::class, 'code_pengajuan', 'code_data');
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
