<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnershipModel extends Model
{
    use HasFactory;
    protected $table = 'partnership';
    protected $primaryKey = 'partnership_id';

    protected $fillable = [
        'period_id',
        'management_id',
        'partnership_name',
        'partnership_phone',
        'partnership_email',
    ];


}
