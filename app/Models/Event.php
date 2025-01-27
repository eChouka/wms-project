<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',           // Event name
        'model_name',     // The model this event is related to
        'page_name',      // The page where this event is available
        'type',           // Type of event: update or delete
        'scope',          // Scope: entire model or specific relation
        'field',          // Field to update (if type is 'update')
        'relation_field', // Relation field (if the event is related to a specific model relation)
        'relation_specific_field',
    ];
}
