<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Blog extends Model
{
     protected $table = 'blogs';
    protected $primaryKey = 'blog_id';
    public $timestamps = false; 

    protected $fillable = [
        'title', 'description', 'category_id', 'tags', 'image',
        'post_author', 'comment_count', 'view', 'meta_title',
        'meta_description', 'sort_order', 'status', 'date_added', 'slug', 'created_at'
    ];

    public function categories()
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_category_blog', 'blog_id', 'category_id');
    }

}
