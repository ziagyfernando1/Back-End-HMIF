<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentModel extends Model
{
    use HasFactory;
    protected $table = 'recruitment';
    protected $primaryKey = 'recruitment_id';

    protected $fillable = [
        'event_id',
        'recruitment_name',
        'created_at',
        'updated_at'
    ];
}