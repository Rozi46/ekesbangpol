<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalances extends Model
{   
    protected $table = 'db_leave_balances';
    protected $fillable = [
        'id',
        'code_data',
        'code_pegawai',
        'tahun',
        'hak',
        'terpakai',
        'sisa',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
}
