<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event_PartnershipModel extends Model
{
    use HasFactory;
    protected $table = 'event_partnership';
    protected $primaryKey = 'event_partnership_id';

    protected $fillable = [
        'event_id',
        'partnership_id',
        'event_partnership_detail',
        'created_at',
        'updated_at',
    ];
}
