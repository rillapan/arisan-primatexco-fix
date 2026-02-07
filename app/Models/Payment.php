<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Participant;
use App\Models\MonthlyPeriod;
use App\Models\Group;
use App\Models\User;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'monthly_period_id',
        'group_id',
        'amount',
        'installment_number',
        'payment_date',
        'payment_method',
        'notes',
        'receipt_number',
        'is_confirmed',
        'confirmed_by',
        'is_notification_read',
        'notification_read_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'is_confirmed' => 'boolean',
        'is_notification_read' => 'boolean',
        'notification_read_at' => 'datetime'
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function monthlyPeriod()
    {
        return $this->belongsTo(MonthlyPeriod::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function markNotificationAsRead()
    {
        $this->is_notification_read = true;
        $this->notification_read_at = now();
        $this->save();
    }

    public function scopeUnreadNotifications($query)
    {
        return $query->where('is_notification_read', false)
                    ->where('is_confirmed', true);
    }

    public function generateReceiptNumber()
    {
        $prefix = 'ANG';
        $date = now()->format('Ymd');
        $lastPayment = Payment::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastPayment ? (int)substr($lastPayment->receipt_number, -4) + 1 : 1;
        
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = $payment->generateReceiptNumber();
            }
        });
    }
}
