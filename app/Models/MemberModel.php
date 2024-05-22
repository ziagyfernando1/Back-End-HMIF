<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberModel extends Model
{
    use HasFactory;

    protected $table = 'member';
    protected $primaryKey = 'member_id';

    protected $fillable = [
        'period_id',
        'member_nim',
        'member_name',
        'member_status',
        'member_phone',
        'member_address',
        'member_birthdate',
        'member_image_url',
        'member_email',
        'member_password',
        'created_at',
        'updated_at',
        'structure_id',
        'division_id'
    ];

    protected $hidden = [
        'member_password'
    ];

    public function period()
    {
        return $this->belongsTo(PeriodModel::class, 'period_id');
    }
}
