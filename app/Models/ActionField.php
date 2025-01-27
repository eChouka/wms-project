<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionField extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_id', 'field_name', 'value_source', 'static_value',
        'current_model_field', 'related_model_relation', 'related_model_field'
    ];

    public function action()
    {
        return $this->belongsTo(Action::class);
    }
}
