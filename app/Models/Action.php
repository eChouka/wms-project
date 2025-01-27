<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasFactory;
    protected $fillable = [
        'model_name', 'event_type', 'action_type', 'target_model', 'condition'
    ];

    public function fields()
    {
        return $this->hasMany(ActionField::class);
    }

    public function conditions()
    {
        return $this->hasMany(ActionCondition::class);
    }

    public function notification()
    {
        return $this->hasOne(ActionNotification::class);
    }
}
