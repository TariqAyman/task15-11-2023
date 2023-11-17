<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    public function isAdmin(): bool
    {
        return $this->user_type == 'admin';
    }

    public function isUser(): bool
    {
        return $this->user_type == 'user';
    }

    public function setPasswordAttribute($password)
    {
        if ($password) {
            $this->attributes['password'] = $password;
        }
    }

    /**
     * Scope a query to only admins.
     */
    public function scopeAdmin(Builder $query): void
    {
        $query->where('user_type', '=', 'admin');
    }

    /**
     * Scope a query to only users.
     */
    public function scopeUser(Builder $query): void
    {
        $query->where('user_type', '=', 'user');
    }

    public function createdTransaction()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    public function createdPayment()
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'payer_id');
    }
}
