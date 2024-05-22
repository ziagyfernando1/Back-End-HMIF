<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectModel extends Model
{
    use HasFactory;
    protected $table = 'subject';
    protected $primaryKey = 'subject_id';

    protected $fillable = [
        'subject_code',
        'subject_name',
        'created_at',
        'updated_at'
    
    ];
}
