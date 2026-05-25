<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'db_setting';
    protected $fillable = [
        'id',
        'code_data',
        'manual_book',
        'file_struktur_organisasi',
        'kata_sambutan',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
}
