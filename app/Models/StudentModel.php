<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentModel extends Model
{
    use HasFactory;

    protected $table = 'student';
    protected $primaryKey = 'student_id';
    protected $fillable = [
        'student_npm',
        'student_name',
        'created_at',
        'updated_at'
    ];
}
