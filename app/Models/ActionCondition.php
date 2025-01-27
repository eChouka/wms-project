<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionCondition extends Model
{
    use HasFactory;
    protected $table='action_conditions';
    protected $fillable = [
        'field_name', 'operator', 'value', 'action_id'
    ];
}
