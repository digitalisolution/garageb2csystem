<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory;

    // Specify the fillable field
    protected $fillable = ['title', 'slug', 'content', 'tyre_search_form', 'include_headermenu', 'page_banner_path', 'parent_id','exclude_sitemap', 'status', 'meta_title', 'meta_description', 'sort'];
     protected $guarded = ['id', 'created_at', 'updated_at']; // slug must NOT be here
    // Relationship for subpages
    public function children()
    {
        return $this->hasMany(Page::class, 'parent_id');
    }

    // Relationship for parent pages
    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }


}

