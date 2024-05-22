<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisimisiModel extends Model
{
    use HasFactory;

    protected $table = 'visimisi';
    protected $primaryKey = 'visimisi_id';
    protected $fillable = [
        'period_id',
        'visimisi_visi',
        'visimisi_misi',
        'created_at',
        'updated_at'
    ];
}
