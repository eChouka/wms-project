<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];
    public static function readableColumnNames()
    {
        return [
            'name' => 'Role Name',
        ];
    }

    public static function requiredFields()
    {
        return [
            'name',
        ];
    }
}
