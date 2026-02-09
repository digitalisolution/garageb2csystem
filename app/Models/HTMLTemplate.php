<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HTMLTemplate extends Model
{
    use HasFactory;
    protected $table = 'html_templates';
    protected $fillable = ['title', 'content', 'template_type', 'status', 'sort_order'];
}
