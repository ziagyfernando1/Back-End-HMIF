<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisionModel extends Model
{
    use HasFactory;
    protected $table = 'division';
    protected $primaryKey = 'division_id';

    protected $fillable = [
        'period_id',
        'division_name',
        'division_description',
        'division_function',
        'division_icon',
        'created_at',
        'updated_at',
    ];
}
