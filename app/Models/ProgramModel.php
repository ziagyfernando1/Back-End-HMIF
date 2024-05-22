<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramModel extends Model
{
    use HasFactory;
    protected $table = 'program';
    protected $primaryKey = 'program_id';

    protected $fillable = [
        'division_id',
        'program_name',
        'program_status',
        'program_desc',
        'program_date'
    ];
}
