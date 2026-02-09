<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class HeaderMenu extends Model
{
    use HasFactory;
    protected $table = 'header_menus';

    // Specify the fillable field
    protected $fillable = [
        'title',
        'slug',
        'parent_id',
        'parent_type',
        'sort'
    ];
    // Relationship for subpages
    public function children()
    {
        return $this->hasMany(HeaderMenu::class, 'parent_id');
    }

    // Relationship for parent pages
    public function parent()
    {
        return $this->belongsTo(HeaderMenu::class, 'parent_id');
    }


}

