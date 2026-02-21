<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Labour extends Model
{
    use HasFactory;
    protected $table = 'labours';
    protected $primaryKey = 'labour_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['labour_name', 'content', 'tax_class_id', 'status',  'sort_order', 'cost_price'];

}
