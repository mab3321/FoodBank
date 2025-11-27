<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderTypeStatus;
use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Shipu\Watchable\Traits\HasModelEvents;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use HasModelEvents, InteractsWithMedia;

    protected $table    = 'orders';
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'total',
        'sub_total',
        'delivery_charge',
        'tax_rate',
        'tax_amount',
        'status',
        'payment_status',
        'paid_amount',
        'address',
        'payment_method',
        'mobile',
        'lat',
        'long',
        'misc',
        'invoice_id',
        'order_type',
        'process_started_at',
        'ready_at',
        'estimated_wait_time',
        'actual_preparation_time'
    ];
    protected $casts = [
        'status' => 'int',
        'order_type' => 'int',
        'payment_status' => 'int',
        'product_received' => 'int',
        'payment_method' => 'int',
        'user_id' => 'int',
        'restaurant_id' => 'int',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_boy_id' => 'int',
        'estimated_wait_time' => 'int',
        'actual_preparation_time' => 'int',
        'process_started_at' => 'datetime',
        'ready_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderLineItem::class)->with('menuItem', 'variation')->with('restaurant');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->with('media', 'roles');
    }

    public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_boy_id', 'id')->with('media', 'roles');
    }

    public function discount()
    {
        return $this->hasOne(Discount::class, 'order_id', 'id');
    }

    public function getOrderCodeAttribute()
    {
        return json_decode($this->misc)->order_code ?? 'ORD-000000';
    }

    public function getRemarksAttribute()
    {
        return json_decode($this->misc)->remarks;
    }

    private function onModelCreated()
    {
        $invoice_id = Str::uuid();

        $invoice               = new Invoice;
        $invoice->id           = $invoice_id;
        $invoice->meta         = ['order_id' => $this->id, 'amount' => $this->total, 'user_id' => $this->user_id];
        $invoice->creator_type = User::class;
        $invoice->editor_type  = User::class;
        $invoice->creator_id   = 1;
        $invoice->editor_id    = 1;
        $invoice->save();

        $this->invoice_id = $invoice_id;
        $this->save();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id')->with('media');
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Invoice::class);
    }
    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function getGetOrderStatusAttribute()
    {
        return __('order_status.' . $this->status);
    }
    public function getGetOrderTypeAttribute()
    {

        return __('orders_type.' . $this->order_type);
    }

    public function getGetPaymentStatusAttribute()
    {
        return __('payment_status.' . $this->payment_status);
    }

    public function getGetPaymentMethodAttribute()
    {
        return __('payment_method.' . $this->payment_method);
    }

    public function getImageAttribute()
    {
        if (!empty($this->getFirstMediaUrl('orders'))) {
            return asset($this->getFirstMediaUrl('orders'));
        }
        return asset('backend/images/default/order.png');
    }

    public function getAttachmentAttribute()
    {
        return $this->getFirstMediaUrl('orders');
    }

    public function getAttachmentInfoAttribute()
    {
        return $this->getFirstMedia('orders');
    }

    public function scopeOrderowner($query)
    {
        $roleID = auth()->user()->myrole ?? 0;
        if ($roleID != UserRole::ADMIN) {
            if ($roleID == UserRole::RESTAURANTOWNER) {
                $restaurant_id = auth()->user()->restaurant->id ?? 0;
                $query->where('restaurant_id', $restaurant_id);
            } else if ($roleID == UserRole::DELIVERYBOY) {
                $query->where('delivery_boy_id', auth()->id());
            } else {
                $query->where('user_id', auth()->id());
            }
        }
    }

    public function getPaymentStatusNameAttribute()
    {
        if ($this->payment_status == PaymentStatus::PAID) {
            return '<span class="db-table-badge text-green-600 bg-green-100">' . trans('payment_status.' . $this->payment_status) . '</span>';
        } elseif ($this->payment_status == PaymentStatus::UNPAID) {
            return '<span class="db-table-badge text-red-600 bg-red-100">' . trans('payment_status.' . $this->payment_status) . '</span>';
        } else {
            return '<span class="db-table-badge text-black bg-gray-200">' . trans('payment_status.' . $this->payment_status) . '</span>';
        }
    }

    public function getStatusNameAttribute()
    {
        if ($this->status == OrderStatus::ACCEPT) {
            return '<span class="db-table-badge text-green-600 bg-green-100">' . trans('order_status.' . $this->status) . '</span>';
        } elseif ($this->status == OrderStatus::PENDING) {
            return '<span class="db-table-badge text-yellow-600 bg-yellow-100">' . trans('order_status.' . $this->status) . '</span>';
        } elseif ($this->status == OrderStatus::CANCEL) {
            return '<span class="db-table-badge text-red-600 bg-red-100">' . trans('order_status.' . $this->status) . '</span>';
        } elseif ($this->status == OrderStatus::REJECT) {
            return '<span class="db-table-badge text-red-600 bg-red-100">' . trans('order_status.' . $this->status) . '</span>';
        } elseif ($this->status == OrderStatus::PROCESS) {
            return '<span class="db-table-badge text-blue-600 bg-blue-100">' . trans('order_status.' . $this->status) . '</span>';
        } elseif ($this->status == OrderStatus::ON_THE_WAY) {
            return '<span class="db-table-badge text-yellow-600 bg-yellow-100">' . trans('order_status.' . $this->status) . '</span>';
        } elseif ($this->status == OrderStatus::COMPLETED) {
            return '<span class="db-table-badge text-green-600 bg-green-100">' . trans('order_status.' . $this->status) . '</span>';
        } else {
            return '<span class="db-table-badge text-black bg-gray-200">' . trans('order_status.' . $this->status) . '</span>';
        }
    }

    public function getGetOrderTypeNameAttribute()
    {
        if ($this->order_type == OrderTypeStatus::DELIVERY) {
            return '<span class="db-table-badge text-green-600 bg-green-100">' . trans('orders_type.' . $this->order_type) . '</span>';
        } elseif ($this->order_type == OrderTypeStatus::PICKUP) {
            return '<span class="db-table-badge text-yellow-600 bg-yellow-100">' . trans('orders_type.' . $this->order_type) . '</span>';
        } elseif ($this->order_type == OrderTypeStatus::TABLE) {
            return '<span class="db-table-badge text-green-600 bg-green-100">' . trans('orders_type.' . $this->order_type) . '</span>';
        } else {
            return '<span class="db-table-badge text-black bg-gray-200">' . trans('orders_type.' . $this->order_type) . '</span>';
        }
    }

    /**
     * Calculate estimated preparation time based on menu items
     */
    public function calculateEstimatedWaitTime()
    {
        if (!$this->items || $this->items->isEmpty()) {
            return 15; // Default fallback
        }

        // Get the maximum wait time from all items in the order
        $maxWaitTime = $this->items->map(function ($item) {
            return $item->menuItem->wait_time ?? 15;
        })->max();

        return $maxWaitTime;
    }

    /**
     * Start the preparation timer when order moves to PROCESS status
     */
    public function startTimer()
    {
        if ($this->status == OrderStatus::PROCESS && !$this->process_started_at) {
            $this->process_started_at = now();
            $this->estimated_wait_time = $this->calculateEstimatedWaitTime();
            $this->save();
        }
    }

    /**
     * Stop the timer and calculate actual preparation time
     */
    public function stopTimer()
    {
        if ($this->process_started_at && !$this->ready_at) {
            $this->ready_at = now();
            $this->actual_preparation_time = $this->process_started_at->diffInMinutes($this->ready_at);
            $this->save();
        }
    }

    /**
     * Get remaining time in minutes
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->process_started_at || !$this->estimated_wait_time) {
            return 0;
        }

        $elapsedMinutes = $this->process_started_at->diffInMinutes(now());
        $remaining = $this->estimated_wait_time - $elapsedMinutes;

        return max(0, $remaining);
    }

    /**
     * Get elapsed time in minutes since process started
     */
    public function getElapsedTimeAttribute()
    {
        if (!$this->process_started_at) {
            return 0;
        }

        return $this->process_started_at->diffInMinutes(now());
    }

    /**
     * Check if order is overdue
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->process_started_at || !$this->estimated_wait_time) {
            return false;
        }

        return $this->elapsed_time > $this->estimated_wait_time;
    }

    /**
     * Get progress percentage (0-100)
     */
    public function getProgressPercentageAttribute()
    {
        if (!$this->process_started_at || !$this->estimated_wait_time) {
            return 0;
        }

        $percentage = ($this->elapsed_time / $this->estimated_wait_time) * 100;
        return min(100, max(0, round($percentage)));
    }

    /**
     * Get formatted remaining time string
     */
    public function getFormattedRemainingTimeAttribute()
    {
        $minutes = $this->remaining_time;

        if ($minutes <= 0) {
            return '0:00';
        }

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d', $hours, $mins);
        }

        return sprintf('%d:00', $mins);
    }

    /**
     * Get formatted tax rate for display
     */
    public function getFormattedTaxRateAttribute()
    {
        return number_format($this->tax_rate, 2) . '%';
    }

    /**
     * Get formatted tax amount for display
     */
    public function getFormattedTaxAmountAttribute()
    {
        return currencyFormat($this->tax_amount);
    }

    /**
     * Calculate and set tax for this order based on restaurant tax rate
     */
    public function calculateAndSetTax()
    {
        if ($this->restaurant && $this->restaurant->tax_rate > 0) {
            $this->tax_rate = $this->restaurant->tax_rate;
            $this->tax_amount = $this->restaurant->calculateTax($this->sub_total);

            // Update total to include tax
            $this->total = $this->sub_total + $this->delivery_charge + $this->tax_amount;
        }
    }

    /**
     * Get the total before tax (subtotal + delivery charge)
     */
    public function getTotalBeforeTaxAttribute()
    {
        return $this->sub_total + $this->delivery_charge;
    }

    /**
     * Get the grand total (subtotal + delivery charge + tax)
     */
    public function getGrandTotalAttribute()
    {
        return $this->sub_total + $this->delivery_charge + $this->tax_amount;
    }
}
