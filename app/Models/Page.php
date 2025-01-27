<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['model_name', 'model_relation', 'custom_code', 'entire_model', 'name', 'content', 'url'];
}
