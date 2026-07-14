<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'db_company';
    protected $fillable = [
        'id',
        'code_data',
        'nama_company',
        'jenis',
        'alamat',
        'email',
        'keterangan',
        'foto',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public function activity()
    {
        return $this->hasMany(Activity::class, 'code_company', 'code_data');
    }
    
    public function user()
    {
        return $this->hasMany(User::class, 'code_company', 'code_data');
    }
    
    public function leaveAprovals()
    {
        return $this->hasMany(LeaveApprovals::class, 'code_company', 'code_data');
    }
    
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalances::class, 'code_company', 'code_data');
    }
    
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequests::class, 'code_company', 'code_data');
    }
}
