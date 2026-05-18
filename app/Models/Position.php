<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $table = 'db_positions';
    protected $fillable = [
        'id',
        'code_data',
        'jabatan',
        'status_data',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public function employees()
    {
        return $this->hasMany(Employees::class, 'code_jabatan', 'code_data');
    }
}
