# Multi-Restaurant Cart Implementation

## Overview
This document describes the implementation of a multi-restaurant cart feature that allows users to add items from multiple restaurants to a single cart and place separate orders for each restaurant during checkout.

## Problem Statement
Previously, the cart would be cleared whenever a user switched to a different restaurant. This prevented users from ordering from multiple restaurants in a single checkout session.

## Solution
The system now supports:
1. **Persistent Multi-Restaurant Cart** - Items from different restaurants remain in cart when switching restaurants
2. **Grouped Cart Display** - Cart items are visually grouped by restaurant
3. **Multi-Order Checkout** - Separate orders are created for each restaurant at checkout
4. **Proportional Charges** - Delivery charges, taxes, and service fees are distributed proportionally across restaurants

## Changes Made

### 1. Cart Controller (API) - `/app/Http/Controllers/Api/v1/CartController.php`
**Changed:** Removed cart clearing logic when switching restaurants
```php
// BEFORE: Cart was destroyed when switching restaurants
if ( session('session_cart_restaurant_id') != $menuItem->restaurant_id ) {
    Cart::destroy();
}

// AFTER: Multi-restaurant cart is preserved
// Allow multi-restaurant cart - don't clear cart when switching restaurants
```

### 2. ShowCart Component - `/app/Livewire/ShowCart.php`
**Changed:** Removed session-based cart clearing
```php
// Now simply stores the current restaurant reference without clearing cart
session()->put('session_cart_restaurant_id', $restaurant_id);
session()->put('session_cart_restaurant', $this->restaurant->slug);
```

### 3. Payment Service - `/app/Http/Services/PaymentService.php`
**Major Refactor:** Complete rewrite to support multiple orders

**Key Changes:**
- Groups cart items by `restaurant_id`
- Creates separate orders for each restaurant
- Calculates proportional delivery charges, taxes, and service fees
- Returns array of order IDs (`order_ids`) for all created orders

**Algorithm:**
```php
// 1. Group items by restaurant
$itemsByRestaurant = [];
foreach ($cartItems as $cartItem) {
    $restaurantId = $cartItem['restaurant_id'];
    $itemsByRestaurant[$restaurantId][] = $cartItem;
}

// 2. Calculate proportional charges for each restaurant
$proportionalDeliveryCharge = ($restaurantSubtotal / $totalSubtotal) * $delivery_charge;
$proportionalTax = ($restaurantSubtotal / $totalSubtotal) * $taxAmount;
$proportionalServiceFee = ($restaurantSubtotal / $totalSubtotal) * $serviceFeeAmount;

// 3. Create separate order for each restaurant
foreach ($itemsByRestaurant as $restaurantId => $restaurantItems) {
    $orderService = app(OrderService::class)->order($orderData);
    $orderIds[] = $orderService->order_id;
}
```

### 4. Checkout Controller - `/app/Http/Controllers/Frontend/CheckoutController.php`
**Updated:** `handleOrderServiceResponse()` method to handle multiple orders

**Key Changes:**
- Checks for `order_ids` array in response
- Sends notifications for all created orders
- Shows count of orders in success message
- Redirects to first order's detail page

```php
if (count($orderService->order_ids) > 1) {
    return redirect(route('account.order.show', $orderService->order_ids[0]))
        ->withSuccess('Orders completed successfully! ' . count($orderService->order_ids) . ' orders have been placed.');
}
```

### 5. OrderCart Component - `/app/Livewire/OrderCart.php`
**Added:** `render()` method with cart grouping logic

**New Feature:**
- Groups cart items by restaurant for display
- Fetches restaurant details for each group
- Passes `groupedItems` array to view

```php
public function render()
{
    $groupedItems = [];
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
    
    return view('livewire.order-cart', ['groupedItems' => $groupedItems]);
}
```

### 6. Cart View - `/resources/views/livewire/order-cart.blade.php`
**Updated:** Display to show items grouped by restaurant

**Visual Changes:**
- Cart title changed from "Your order from [Restaurant]" to "Your cart"
- Added restaurant name headers for each group
- Each restaurant section shows its items
- Visual separators between restaurant groups

```blade
@foreach ($groupedItems as $restaurantId => $group)
    <div class="restaurant-group mb-3">
        <h4 class="restaurant-group-name">
            <i class="fa fa-store"></i> {{ $group['restaurant']->name }}
        </h4>
        <ul class="cart-list">
            @foreach ($group['items'] as $key => $content)
                <!-- Cart items for this restaurant -->
            @endforeach
        </ul>
    </div>
@endforeach
```

## Database Structure
**No database changes required** - The existing schema already supports this feature:
- `order_line_items.restaurant_id` - Already stores restaurant ID with each order item
- Cart items stored in session include `restaurant_id` in their options

## User Flow

### Adding Items from Multiple Restaurants
1. User visits Restaurant A and adds items to cart
2. User navigates to Restaurant B
3. **NEW:** Cart retains Restaurant A items
4. User adds items from Restaurant B
5. Cart now shows items from both restaurants grouped separately

### Checkout Process
1. User proceeds to checkout
2. Single checkout form for all items
3. User selects payment method and address
4. System creates separate orders:
   - Order 1: Restaurant A items
   - Order 2: Restaurant B items
5. Each order gets proportional delivery charge and fees
6. Success message shows: "Orders completed successfully! 2 orders have been placed."
7. User redirected to first order's detail page

## Charge Distribution

### Example Calculation
**Cart Contents:**
- Restaurant A: $30 of items
- Restaurant B: $20 of items
- Total: $50
- Delivery Charge: $5
- Tax Rate: 10%
- Service Fee Rate: 5%

**Order 1 (Restaurant A):**
- Subtotal: $30
- Delivery Charge: ($30/$50) * $5 = $3.00
- Tax: ($30/$50) * ($50 * 0.10) = $3.00
- Service Fee: ($30/$50) * ($50 * 0.05) = $1.50
- Total: $37.50

**Order 2 (Restaurant B):**
- Subtotal: $20
- Delivery Charge: ($20/$50) * $5 = $2.00
- Tax: ($20/$50) * ($50 * 0.10) = $2.00
- Service Fee: ($20/$50) * ($50 * 0.05) = $1.00
- Total: $25.50

## Payment Methods
All payment methods are supported:
- Cash on Delivery
- Credit/Debit Card (Stripe)
- PayPal
- Razorpay
- Paystack
- Paytm
- PhonePe
- SSLCommerz
- Wallet

**Important:** Payment is made once for the entire cart. The system then distributes the payment proportionally across the created orders.

## API Changes

### Response Structure
**Before:**
```json
{
    "status": true,
    "order_id": 123
}
```

**After (Backward Compatible):**
```json
{
    "status": true,
    "order_id": 123,      // First order ID (backward compatibility)
    "order_ids": [123, 124],  // All order IDs
    "message": "Orders created successfully"
}
```

## Notifications
- Each restaurant receives notification for their respective order
- Customer receives notification for the first order (with mention of multiple orders in message)

## Testing Checklist

### Basic Functionality
- [ ] Add items from Restaurant A to cart
- [ ] Navigate to Restaurant B without losing Restaurant A items
- [ ] Add items from Restaurant B to cart
- [ ] Verify cart shows both restaurants' items grouped separately
- [ ] Remove individual items from cart
- [ ] Adjust quantities for items

### Checkout Process
- [ ] Complete checkout with items from multiple restaurants
- [ ] Verify separate orders are created in database
- [ ] Check order IDs are returned correctly
- [ ] Verify proportional charge distribution
- [ ] Check success message shows correct count

### Payment Methods
- [ ] Test Cash on Delivery
- [ ] Test Credit Card (Stripe)
- [ ] Test PayPal
- [ ] Test Wallet payment
- [ ] Verify payment amount matches total cart value

### Edge Cases
- [ ] Cart with single restaurant (backward compatibility)
- [ ] Remove all items from one restaurant in multi-restaurant cart
- [ ] Apply coupon code (should apply to total)
- [ ] Pickup vs Delivery selection
- [ ] Free delivery threshold

## Known Limitations

1. **Coupon Distribution**: Coupons are currently split equally across orders. Future enhancement could be to apply coupons intelligently based on restaurant restrictions.

2. **Single Delivery Address**: All orders use the same delivery address. Future enhancement could allow different addresses per restaurant.

3. **Single Payment**: All orders must use the same payment method. Cannot pay differently for different restaurants.

4. **Order Detail View**: Currently redirects to first order only. Future enhancement could create a "multi-order" summary page.

## Future Enhancements

1. **Smart Delivery Grouping**: Group orders from nearby restaurants with a single delivery person
2. **Restaurant-Specific Coupons**: Apply different coupons to different restaurants
3. **Delivery Time Slots**: Allow different delivery times for different restaurants
4. **Multi-Order Dashboard**: Show all orders from a single checkout in one view
5. **Combined Tracking**: Track all orders from a checkout session together

## Rollback Plan
If issues arise, revert these files:
1. `/app/Http/Services/PaymentService.php`
2. `/app/Http/Controllers/Frontend/CheckoutController.php`
3. `/app/Http/Controllers/Api/v1/CartController.php`
4. `/app/Livewire/ShowCart.php`
5. `/app/Livewire/OrderCart.php`
6. `/resources/views/livewire/order-cart.blade.php`

Use git to restore previous versions:
```bash
git checkout <commit-before-changes> -- <file-path>
```

## Support
For issues or questions about this implementation, check:
- Cart items include `restaurant_id` in session data
- PaymentService returns `order_ids` array
- CheckoutController checks for `order_ids` in response
- OrderCart component groups items by `restaurant_id`

---
**Implementation Date:** December 24, 2025
**Version:** 1.0
**Status:** Completed and Ready for Testing
