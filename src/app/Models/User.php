<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
            'role'     => UserRole::class,
        ];
    }

    // Relation
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function AttendanceCorrection(): Hasmany
    {
        return $this->hasMany(AttendanceCorrection::class);
    }

    // Role判定
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    Public function isStaff(): bool
    {
        return $this->role === UserRole::Staff;
    }
}
