<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageCounter extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'subscription_id',
        'billing_cycle_start',
        'billing_cycle_end',
        'used_units',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'billing_cycle_start' => 'date',
        'billing_cycle_end' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
