<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'status',
        'payment_method',
        'toyyibpay_transaction_id',
        'toyyibpay_bill_code',
        'paid_at',
        'failed_at',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Get the booking that owns the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope to get paid payments
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope to get pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid($transactionId = null)
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'toyyibpay_transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);

        // Update booking status to confirmed when payment is successful
        if ($this->booking && $this->booking->status === Booking::STATUS_PENDING) {
            $this->booking->update(['status' => Booking::STATUS_CONFIRMED]);
        }
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }
}








