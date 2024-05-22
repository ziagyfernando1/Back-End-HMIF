<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementModel extends Model
{
    use HasFactory;
    protected $table = 'management';
    protected $primaryKey = 'management_id';

    protected $fillable = [
        'structure_id',
        'division_id',
        'staff_id', 
        'created_at',
        'updated_at',
    ];
}
