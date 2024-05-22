<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffModel extends Model
{
    use HasFactory;

    protected $table = 'staff';
    protected $primaryKey = 'staff_id';
    protected $fillable = [
        'period_id',
        'member_id',
        'staff_level',
        'staff_is_delete',
        'created_at',
        'updated_at'
    ];

    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id');
    }

    public function period()
    {
        return $this->belongsTo(PeriodModel::class, 'period_id');
    }
}
