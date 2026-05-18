<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranks extends Model
{
    protected $table = 'db_ranks';
    protected $fillable = [
        'id',
        'code_data',
        'pangkat',
        'golongan',
        'ruang',
        'status_data',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    public function employees()
    {
        return $this->hasMany(Employees::class, 'code_pangkat', 'code_data');
    }
}
