<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Coupon;
use Livewire\Component;
use App\Models\Discount;
use App\Enums\CouponType;
use App\Models\Restaurant;
use App\Enums\DeliveryType;
use App\Enums\DiscountType;
use App\Enums\DeliveryStatus;
use App\Enums\DiscountStatus;

class OrderCart extends Component
{
    public $carts = [];
    public $totalQty = 0;
    public $totalItem = 0;
    public $totalAmount = 0;
    public $subTotalAmount = 0;
    public $delivery_charge = 0;
    public $totalPayAmount = 0;
    public $restaurant;
    public $totalItemQtyAmount;
    public $totalItemQty;
    public $qty = 1;
    public $coupon = '';
    public $discountAmount = 0;
    public $min_order = 0;
    public $max_order = 0;
    public $msg = '';
    public $couponID = 0;
    public $delivery_type;
    public $free_delivery;
    public bool $isActive = false;
    public bool $isActiveCheckout = true;

    protected $listeners = ['addCart'];

    /**
     * Hydrate method - called every time the component is hydrated from a request
     * This ensures cart is always loaded from session
     */
    public function hydrate()
    {
        // CRITICAL: Always reload cart from session when component hydrates
        $sessionCart = session()->get('cart');
        if (!blank($sessionCart) && is_array($sessionCart)) {
            $this->carts = $sessionCart;
        } elseif (!isset($this->carts['items'])) {
            // Ensure items array exists
            $this->carts = ['items' => []];
        }
    }

    public function mount($restaurant = null)
    {
        // Accept restaurant from parent component but don't let it clear the cart
        if ($restaurant) {
            $this->restaurant = $restaurant;
            // Update session to track current restaurant view (but don't clear cart)
            session()->put('session_cart_restaurant_id', $restaurant->id);
            session()->put('session_cart_restaurant', $restaurant->slug);
        } else {
            // Get the last accessed restaurant or first restaurant from cart items
            $restaurantId = session()->get('session_cart_restaurant_id');
            $existingCart = session()->get('cart');

            // If no restaurant in session, try to get from first cart item
            if (!$restaurantId && !blank($existingCart) && !blank($existingCart['items'])) {
                $firstItem = reset($existingCart['items']);
                $restaurantId = $firstItem['restaurant_id'] ?? null;
                if ($restaurantId) {
                    session()->put('session_cart_restaurant_id', $restaurantId);
                }
            }

            $this->restaurant = Restaurant::find($restaurantId);
        }

        // Set delivery options based on current restaurant being viewed
        if ($this->restaurant) {
            if ($this->restaurant->pickup_status == DeliveryStatus::ENABLE && $this->restaurant->delivery_status == DeliveryStatus::DISABLE) {
                $this->delivery_charge = 0;
                $this->delivery_type = true;
            } elseif ($this->restaurant->pickup_status == DeliveryStatus::DISABLE && $this->restaurant->delivery_status == DeliveryStatus::DISABLE) {
                $this->isActiveCheckout = false;
            }
        }

        // CRITICAL: Load cart from session - DON'T clear it
        // This must happen EVERY time the component mounts
        $sessionCart = session()->get('cart');
        if (!blank($sessionCart) && is_array($sessionCart)) {
            $this->carts = $sessionCart;
        } else {
            // Initialize empty cart structure if nothing in session
            $this->carts = ['items' => []];
        }

        // Recalculate totals if cart has items
        if (!blank($this->carts) && isset($this->carts['items']) && !empty($this->carts['items'])) {
            $this->totalCartAmount();
        }
    }

    public function isUpdating()
    {
        $this->delivery_type = $this->isActive;
        if (!blank($this->carts)) {
            $this->totalCartAmount();
        }
    }

    public function render()
    {
        // Group cart items by restaurant for multi-restaurant display
        $groupedItems = [];
        if (!blank($this->carts) && !blank($this->carts['items'])) {
            foreach ($this->carts['items'] as $key => $item) {
                $restaurantId = $item['restaurant_id'];
                if (!isset($groupedItems[$restaurantId])) {
                    $restaurant = Restaurant::find($restaurantId);
                    $groupedItems[$restaurantId] = [
                        'restaurant' => $restaurant,
                        'items' => []
                    ];
                }
                $groupedItems[$restaurantId]['items'][$key] = $item;
            }
        }

        // Ensure we have a restaurant for the view (use first from grouped items or stored one)
        $restaurant = $this->restaurant;
        if (!$restaurant && !empty($groupedItems)) {
            $firstGroup = reset($groupedItems);
            $restaurant = $firstGroup['restaurant'];
        }

        // If still no restaurant, try to get from session
        if (!$restaurant) {
            $restaurant = Restaurant::find(session()->get('session_cart_restaurant_id'));
        }

        return view('livewire.order-cart', [
            'groupedItems' => $groupedItems,
            'restaurant' => $restaurant
        ]);
    }

    public function addCart($item)
    {
        // Ensure carts array is properly initialized
        if (!is_array($this->carts)) {
            $this->carts = [];
        }

        // Ensure items array exists
        if (!isset($this->carts['items']) || !is_array($this->carts['items'])) {
            $this->carts['items'] = [];
        }

        $status = true;

        // Check if item already exists in cart (same item with same variation)
        if (!empty($this->carts['items'])) {
            foreach ($this->carts['items'] as $key => $cart) {
                if ($item['id'] == $cart['id'] && $item['variationID'] == $cart['variationID']) {
                    $this->carts['items'][$key]['qty'] += $item['qty'];
                    $status = false;
                    break;
                }
            }
        }

        // If item not found, add as new item
        if ($status) {
            $this->carts['items'][] = $item;
        }

        $this->totalCartAmount();
    }

    public function changeEvent($id)
    {
        if ($this->carts['items'][$id]['qty'] != '' && $this->carts['items'][$id]['qty'] != 0) {
            $this->totalCartAmount();
        }
    }
    public function addItemQty($id)
    {
        $this->carts['items'][$id]['qty']++;
        $this->addCoupon();
        $this->totalCartAmount();
    }
    public function removeItemQty($id)
    {
        if (!blank($this->carts['items'])) {
            $this->carts['items'][$id]['qty']--;
            if ($this->carts['items'][$id]['qty'] == 0) {
                unset($this->carts['items'][$id]);
                $this->carts['items'] = array_values($this->carts['items']);
            }
        }
        $this->totalCartAmount();
    }


    public function totalCartAmount()
    {
        $this->totalItem = 0;
        $this->totalAmount = 0;
        $this->totalQty = 0;
        $this->subTotalAmount = 0;

        if (!blank($this->carts['items'])) {
            // Group items by restaurant to calculate tax/service fee per restaurant
            $restaurantTotals = [];

            foreach ($this->carts['items'] as $key => $cart) {
                $this->totalItem += 1;
                $this->totalQty += $cart['qty'];
                $itemTotal = $cart['price'] * $cart['qty'];
                $this->totalAmount += $itemTotal;
                $this->carts['items'][$key]['totalPrice'] = $itemTotal;

                // Group by restaurant for tax calculation
                $restaurantId = $cart['restaurant_id'];
                if (!isset($restaurantTotals[$restaurantId])) {
                    $restaurantTotals[$restaurantId] = 0;
                }
                $restaurantTotals[$restaurantId] += $itemTotal;
            }

            // Set delivery charge based on delivery type BEFORE tax calculation
            if ($this->delivery_type == DeliveryType::PICKUP) {
                $this->delivery_charge = 0;
                $this->delivery_type = true;
            } else {
                $this->delivery_charge = setting('basic_delivery_charge');
            }

            // Calculate tax and service fee for EACH restaurant and sum them up
            $totalTaxAmount = 0;
            $totalServiceFeeAmount = 0;
            $weightedTaxRate = 0;
            $weightedServiceFeeRate = 0;

            $subtotalAfterDiscount = $this->totalAmount - $this->discountAmount;

            foreach ($restaurantTotals as $restaurantId => $restaurantSubtotal) {
                $restaurant = Restaurant::find($restaurantId);
                if ($restaurant) {
                    // Calculate proportion of this restaurant in total cart
                    $proportion = $subtotalAfterDiscount > 0 ? ($restaurantSubtotal / $this->totalAmount) : 0;
                    $restaurantSubtotalAfterDiscount = $subtotalAfterDiscount * $proportion;

                    // Calculate tax for this restaurant's items
                    if ($restaurant->tax_rate > 0) {
                        $restaurantTax = round(($restaurantSubtotalAfterDiscount * $restaurant->tax_rate) / 100, 2);
                        $totalTaxAmount += $restaurantTax;
                        $weightedTaxRate += ($restaurant->tax_rate * $proportion);
                    }

                    // Calculate service fee for this restaurant's items
                    if ($restaurant->service_fee_rate > 0) {
                        $restaurantServiceFee = round(($restaurantSubtotalAfterDiscount * $restaurant->service_fee_rate) / 100, 2);
                        $totalServiceFeeAmount += $restaurantServiceFee;
                        $weightedServiceFeeRate += ($restaurant->service_fee_rate * $proportion);
                    }
                }
            }

            $this->carts['couponID'] = $this->couponID;
            $this->subTotalAmount = $this->totalAmount;
            $this->carts['subTotalAmount'] = $this->totalAmount;
            $this->carts['totalQty'] = $this->totalQty;

            // Store weighted average rates for display and total amounts
            $this->carts['tax_rate'] = round($weightedTaxRate, 2);
            $this->carts['tax_amount'] = $totalTaxAmount;
            $this->carts['service_fee_rate'] = round($weightedServiceFeeRate, 2);
            $this->carts['service_fee_amount'] = $totalServiceFeeAmount;

            // Calculate final amounts
            $this->totalAmount = $subtotalAfterDiscount; // Amount after discount
            $this->totalPayAmount = $this->totalAmount + $this->delivery_charge + $totalTaxAmount + $totalServiceFeeAmount;

            $this->carts['totalPayAmount'] = $this->totalPayAmount;
            $this->carts['totalAmount'] = $this->totalAmount;
            $this->carts['delivery_charge'] = $this->delivery_charge;


            $this->carts['min_order'] = $this->min_order;
            $this->carts['max_order'] = $this->max_order;
            $this->carts['coupon_amount'] = $this->discountAmount;
            $this->carts['delivery_type'] = $this->delivery_type;
            $this->carts['free_delivery'] = $this->free_delivery;
        } else {
            $this->totalAmount = 0;
            $this->delivery_charge = 0;
            $this->delivery_type = false;
            $this->free_delivery = false;
            $this->min_order = 0;
            $this->max_order = 0;
            $this->totalPayAmount = 0;
            $this->subTotalAmount = 0;
            $this->carts['couponID'] = 0;
            $this->carts['subTotalAmount'] = 0;
            $this->carts['totalQty'] = 0;
            $this->carts['totalPayAmount'] = 0;
            $this->carts['totalAmount'] = 0;
            $this->carts['delivery_charge'] = 0;
            $this->carts['coupon_amount'] = 0;
            $this->carts['tax_rate'] = 0;
            $this->carts['tax_amount'] = 0;
            $this->carts['service_fee_rate'] = 0;
            $this->carts['service_fee_amount'] = 0;
        }

        $this->dispatch('showCartQty', ['qty' => $this->totalQty]);

        session()->put('cart', $this->carts);
        $this->carts = session()->get('cart');
    }

    public function removeItem($id)
    {
        unset($this->carts['items'][$id]);
        session()->put('cart', $this->carts);
        $this->totalCartAmount();
    }



    public function addCoupon()
    {
        $this->msg = '';
        $this->discountAmount = 0.0;
        $this->couponID = 0;

        if (blank($this->coupon)) {
            $this->totalCartAmount();
            return;
        }

        $now = now();
        $restaurantId = session('session_cart_restaurant_id');
        $totalAmount = (float) data_get($this->carts, 'totalAmount', 0);
        $coupon = Coupon::where('slug', $this->coupon)->first();

        if (!$coupon) {
            $this->msg = 'This Coupon is Invalid';
            $this->totalCartAmount();
            return;
        }

        $totalUsed = Discount::where('coupon_id', $coupon->id)
            ->where('status', DiscountStatus::ACTIVE)
            ->count();

        $userLimit = Discount::where([
            'coupon_id' => $coupon->id,
            'user_id'   => auth()->id() ?? 0,
        ])
            ->where('status', DiscountStatus::ACTIVE)
            ->count();

        if ($coupon->coupon_type == CouponType::VOUCHER && $coupon->restaurant_id != $restaurantId) {
            $this->msg = 'This Coupon is Invalid';
        } elseif ($totalUsed >= (int) $coupon->limit) {
            $this->msg = 'This Coupon is Expired';
        } elseif ($now->lt(Carbon::parse($coupon->from_date)) || $now->gt(Carbon::parse($coupon->to_date))) {
            $this->msg = 'This Coupon is Expired';
        } elseif ($userLimit >= (int) $coupon->user_limit) {
            $this->msg = 'This Coupon is Expired';
        } elseif ($totalAmount < (float) $coupon->minimum_order_amount) {
            $this->msg = 'Minimum Order Amount for This Coupon is ' . currencyFormat($coupon->minimum_order_amount);
        }

        if (!blank($this->msg)) {
            $this->totalCartAmount();
            return;
        }
        $this->couponID = $coupon->id;

        $base = (float) data_get(
            $this->carts,
            'discountableAmount',
            data_get($this->carts, 'subTotal', $totalAmount)
        );
        if ($coupon->discount_type == DiscountType::FIXED) {
            $discount = (float) $coupon->amount;
        } else {
            $percent = (float) $coupon->amount;
            if ($percent > 0 && $percent <= 1) {
                $percent = $percent * 100;
            }

            if (function_exists('bcdiv')) {
                $discount = bcdiv(bcmul((string)$base, (string)$percent, 4), '100', 2);
                $discount = (float) $discount;
            } else {
                $discount = round(($base * $percent) / 100, 2, PHP_ROUND_HALF_UP);
            }

            if (!empty($coupon->max_discount)) {
                $discount = min($discount, (float) $coupon->max_discount);
            }
        }
        $discount = min($discount, $base);

        $this->discountAmount = (float) $discount;
        $this->totalCartAmount();
    }
}
