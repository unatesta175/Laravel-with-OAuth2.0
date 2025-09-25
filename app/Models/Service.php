<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'duration',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that owns the service.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the therapists that can perform this service.
     */
    public function therapists()
    {
        return $this->belongsToMany(User::class, 'service_therapist', 'service_id', 'user_id')
                    ->where('role', 'therapist');
    }

    /**
     * Get the bookings for this service.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope to get only active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get available therapists for this service on a specific date
     */
    public function getAvailableTherapists($date)
    {
        return $this->therapists()
                    ->whereDoesntHave('bookings', function ($query) use ($date) {
                        $query->whereDate('appointment_date', $date);
                    });
    }
}




