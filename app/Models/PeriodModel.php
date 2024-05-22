<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodModel extends Model
{
    use HasFactory;

    protected $table = 'period';
    protected $primaryKey = 'period_id';
    protected $fillable = [
        'period_name',
        'period_status',
        'period_created_at',
        'period_updated_at',
    ];
}
