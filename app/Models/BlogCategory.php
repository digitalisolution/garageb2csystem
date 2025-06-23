<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table = 'blog_category';
    protected $primaryKey = 'category_id';
    protected $fillable = [
        'title',
        'slug',
    ];
    
    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_category_blog', 'category_id', 'blog_id');
    }
}

