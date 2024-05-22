<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StructureModel extends Model
{
    use HasFactory;
    protected $table ='structure';
    protected $primaryKey = 'structure_id';
    protected $fillable = [
        'period_id',
        'structure_name',
        'structure_level',
        'structure_is_delete',
        'created_at',
        'updated_at'

        
    ];
}
