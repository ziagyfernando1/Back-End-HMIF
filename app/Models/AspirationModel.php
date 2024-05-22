<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AspirationModel extends Model
{
    use HasFactory;
    protected $table = 'aspiration';
    protected $primaryKey = 'aspiration_id';
    protected $fillable = [
        'aspiration_class',
        'aspiration_subject',
        'aspiration_period',
        'aspiration_message',
        'student_id',
        'created_at',
        'updated_at'
    ];
}
