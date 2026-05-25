<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SingelPage extends Model
{
    protected $table = 'db_singel_page';
    protected $fillable = [
        'id',
        'code_data',
        'code_user',
        'url_page',
        'judul_page',
        'isi_page',
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
