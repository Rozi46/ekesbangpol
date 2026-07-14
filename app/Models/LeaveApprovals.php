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
}
