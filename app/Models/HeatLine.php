<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeatLine extends Model
{
    protected $table = 'db_heat_lines';
    protected $fillable = [
        'id',
        'code_data',
        'code_heat',
        'code_athlete',
        'line_number',
        'best_time',
        'hasil',
        'ranking',
        'created_at',
        'updated_at'
    ];
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';   
    
    public function heat()
    {
        return $this->belongsTo(Heat::class, 'code_heat', 'code_data');
    }

    public function atlet()
    {
        return $this->belongsTo(Atlet::class, 'code_athlete', 'code_data');
    }
    
    public function result()
    {
        return $this->hasMany(Result::class, 'code_heatline', 'code_data');
    }
}
