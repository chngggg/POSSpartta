<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'avatar' => 'string',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($roleSlug)
    {
        return $this->role && $this->role->slug === $roleSlug;
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isEmployee()
    {
        return $this->hasRole('employee');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }
}
