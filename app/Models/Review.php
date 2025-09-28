<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'client_id',
        'therapist_id',
        'service_id',
        'rating',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the booking that owns the review.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the client that wrote the review.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the therapist that received the review.
     */
    public function therapist()
    {
        return $this->belongsTo(User::class, 'therapist_id');
    }

    /**
     * Get the service that was reviewed.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope to get only approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get reviews by rating
     */
    public function scopeRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Get average rating for a therapist
     */
    public static function getAverageRatingForTherapist($therapistId)
    {
        return self::where('therapist_id', $therapistId)
                   ->approved()
                   ->avg('rating');
    }

    /**
     * Get review count for a therapist
     */
    public static function getReviewCountForTherapist($therapistId)
    {
        return self::where('therapist_id', $therapistId)
                   ->approved()
                   ->count();
    }
}








