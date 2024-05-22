<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationModel extends Model
{
    use HasFactory;
    protected $table = 'registration';
    protected $primaryKey = 'registration_id';

    protected $fillable = [
        'event_id',
        'registration_name',
        'created_at',
        'updated_at',
    ];
}
