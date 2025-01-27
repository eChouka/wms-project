<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionNotification extends Model
{
    use HasFactory;
    protected $table='action_notifications';
    protected $fillable = [
        'recipient', 'subject', 'message', 'action_id'
    ];
}
