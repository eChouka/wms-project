<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
    ];

    public function getHasManyRelations()
    {
        return [
        ];
    }

    public function getRelationMappings()
    {
        return [
            'role_id' => 'roles',
        ];
    }

    public static function readableColumnNames()
    {
        return [
            'name' => 'Full Name',
            'email' => 'Email Address',
            'role_id'=>'Role'
        ];
    }

    public static function getRelationMethodName()
    {
       return [];
    }

    public static function requiredFields()
    {
        return [
            'name',
            'email',
            'role_id',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'activation_token',
        'remember_token',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->hasMany(Role::class, 'role_id', 'id');
    }
}
