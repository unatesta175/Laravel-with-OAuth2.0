<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a therapist
     */
    public function isTherapist(): bool
    {
        return $this->hasRole('therapist');
    }

    /**
     * Check if user is a client
     */
    public function isClient(): bool
    {
        return $this->hasRole('client');
    }

    /**
     * Get the bookings for the user (clients and therapists)
     */
    public function bookings()
    {
        if ($this->isTherapist()) {
            return $this->hasMany(Booking::class, 'therapist_id');
        }
        return $this->hasMany(Booking::class, 'user_id');
    }

    /**
     * Get reviews written by this user (clients only)
     */
    public function reviewsWritten()
    {
        return $this->hasMany(Review::class, 'client_id');
    }

    /**
     * Get reviews received by this user (therapists only)
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'therapist_id');
    }

    /**
     * Get services this therapist can perform
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_therapist');
    }
}
