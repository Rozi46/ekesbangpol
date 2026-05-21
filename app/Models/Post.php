<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'db_post';
    protected $fillable = [
        'id',
        'code_data',
        'code_user',
        'url',
        'judul',
        'isi',
        'sumber',
        'tumb',
        'jumlah_view',
        'tipe',
        'status_data',
        'code_company',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';   
    
    public function user()
    {
        return $this->belongsTo(User::class, 'code_user', 'code_data');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'code_company', 'code_data');
    }
}
