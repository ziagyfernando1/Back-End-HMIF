<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventModel extends Model
{
    use HasFactory;
    protected $table = 'event';
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'program_id',
        'staff_id',
        'event_name',
        'event_status',
        'event_date',
        'created_at',
        'updated_at',
    ];
}
