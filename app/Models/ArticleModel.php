<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleModel extends Model
{
    use HasFactory;

    protected $table = 'article';
    protected $primaryKey = 'article_id';
    protected $fillable = [
        'member_id',
        'category_id',
        'article_title',
        'article_content',
        'article_image',
        'article_slug',
        'created_at',
        'updated_at'
    ];

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }

    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id');
    }
}
