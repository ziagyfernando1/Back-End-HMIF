<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionModel extends Model
{
    use HasFactory;
    protected $table = 'selection';
    protected $primaryKey = 'selection_id';
    protected $fillable = [
        'recruitment_id',
        'selection_name',
        'selection_description',
        'created_at',
        'updated_at'
    ];
}
