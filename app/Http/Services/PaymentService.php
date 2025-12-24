<?php

namespace App\Http\Services;

use App\Enums\OrderTypeStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Restaurant;

class PaymentService
{
    public $data = array();

    public function payment($paymetSuccess)
    {
        $request = session()->get('checkoutRequest');
        $cart = session()->get('cart');

        if (($cart && isset($cart['delivery_type']) && $cart['delivery_type'] == true) || ($cart && isset($cart['free_delivery']) && $cart['free_delivery'])) {
            $delivery_charge = 0;
            $order_type = OrderTypeStatus::PICKUP;
        } else {
            $delivery_charge = session()->get('delivery_charge');
            $order_type = OrderTypeStatus::DELIVERY;
        }

        $cartItems = session()->get('cart')['items'] ?? [];

        // Group cart items by restaurant
        $itemsByRestaurant = [];
        foreach ($cartItems as $cartItem) {
            $restaurantId = $cartItem['restaurant_id'];
            if (!isset($itemsByRestaurant[$restaurantId])) {
                $itemsByRestaurant[$restaurantId] = [];
            }
            $itemsByRestaurant[$restaurantId][] = $cartItem;
        }

        // Get tax and service fee information from cart
        $taxAmount = isset($cart['tax_amount']) ? $cart['tax_amount'] : 0;
        $serviceFeeAmount = isset($cart['service_fee_amount']) ? $cart['service_fee_amount'] : 0;
        $totalAmountWithTax = (isset($cart['totalAmount']) ? $cart['totalAmount'] : 0) + $delivery_charge + $taxAmount + $serviceFeeAmount;

        $taxAmount = isset($cart['tax_amount']) ? $cart['tax_amount'] : 0;
        $serviceFeeAmount = isset($cart['service_fee_amount']) ? $cart['service_fee_amount'] : 0;
        $totalAmountWithTax = (isset($cart['totalAmount']) ? $cart['totalAmount'] : 0) + $delivery_charge + $taxAmount + $serviceFeeAmount;

        // Determine payment status based on payment method
        $paymentStatus = PaymentStatus::UNPAID;
        $paymentMethod = PaymentMethod::CASH_ON_DELIVERY;
        $paidAmount = 0;

        if ($request['payment_type'] == PaymentMethod::STRIPE && $paymetSuccess) {
            $paidAmount = $totalAmountWithTax;
            $paymentMethod = $request['payment_type'];
            $paymentStatus = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::PAYTM) {
            $paidAmount = $totalAmountWithTax;
            $paymentMethod = $request['payment_type'];
            $paymentStatus = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::PHONEPE) {
            $paidAmount = $totalAmountWithTax;
            $paymentMethod = $request['payment_type'];
            $paymentStatus = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::WALLET) {
            $paidAmount = $totalAmountWithTax;
            $paymentMethod = $request['payment_type'];
            $paymentStatus = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::PAYSTACK && $paymetSuccess) {
            $paidAmount = $totalAmountWithTax;
            $paymentMethod = $request['payment_type'];
            $paymentStatus = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::PAYPAL && $paymetSuccess) {
            $paidAmount = $totalAmountWithTax;
            $paymentMethod = $request['payment_type'];
            $paymentStatus = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::RAZORPAY && $paymetSuccess) {
            $paidAmount = $totalAmountWithTax;
            $paymentMethod = $request['payment_type'];
            $paymentStatus = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::SSLCOMMERZ) {
            $paidAmount = $totalAmountWithTax;
            $paymentMethod = $request['payment_type'];
            $paymentStatus = PaymentStatus::PAID;
        }

        // Create separate orders for each restaurant
        $orderIds = [];
        $restaurantCount = count($itemsByRestaurant);

        foreach ($itemsByRestaurant as $restaurantId => $restaurantItems) {
            // Calculate subtotal for this restaurant's items
            $restaurantSubtotal = 0;
            $items = [];

            foreach ($restaurantItems as $cartItem) {
                $menuItemVariationId = $cartItem['variation']['id'] ?? null;
                $variation = $cartItem['variation'] ?? null;
                $options = $cartItem['options'] ?? null;
                $instructions = $cartItem['instructions'] ?? null;

                $items[] = [
                    'restaurant_id' => $restaurantId,
                    'menu_item_variation_id' => $menuItemVariationId,
                    'menu_item_id' => $cartItem['menuItem_id'],
                    'unit_price' => (float) $cartItem['price'],
                    'quantity' => (int) $cartItem['qty'],
                    'discounted_price' => (float) $cartItem['discount'],
                    'variation' => $variation,
                    'options' => $options,
                    'instructions' => $instructions,
                ];

                $restaurantSubtotal += (float) $cartItem['totalPrice'];
            }

            // Calculate proportional delivery charge, tax, and service fee for this restaurant
            $totalSubtotal = isset($cart['totalAmount']) ? $cart['totalAmount'] : 0;
            $proportionalDeliveryCharge = $totalSubtotal > 0 ? ($restaurantSubtotal / $totalSubtotal) * $delivery_charge : 0;
            $proportionalTax = $totalSubtotal > 0 ? ($restaurantSubtotal / $totalSubtotal) * $taxAmount : 0;
            $proportionalServiceFee = $totalSubtotal > 0 ? ($restaurantSubtotal / $totalSubtotal) * $serviceFeeAmount : 0;
            $proportionalPaidAmount = $totalSubtotal > 0 ? ($restaurantSubtotal / $totalSubtotal) * $paidAmount : 0;

            $orderData = [
                'items' => $items,
                'order_type' => $order_type,
                'restaurant_id' => $restaurantId,
                'user_id' => auth()->user()->id,
                'total' => $restaurantSubtotal,
                'delivery_charge' => $proportionalDeliveryCharge,
                'address' => isset($request['address']) ? $request['address'] : '',
                'mobile' => $request['countrycode'] . $request['mobile'],
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'paid_amount' => $proportionalPaidAmount,
                'tax_rate' => isset($cart['tax_rate']) ? $cart['tax_rate'] : 0,
                'tax_amount' => $proportionalTax,
                'service_fee_rate' => isset($cart['service_fee_rate']) ? $cart['service_fee_rate'] : 0,
                'service_fee_amount' => $proportionalServiceFee,
                'coupon_id' => isset($cart['couponID']) ? $cart['couponID'] : null,
                'coupon_amount' => isset($cart['coupon_amount']) ? ($cart['coupon_amount'] / $restaurantCount) : null,
            ];

            $orderService = app(OrderService::class)->order($orderData);

            if ($orderService->status) {
                $orderIds[] = $orderService->order_id;
            }
        }

        // Return response with all order IDs
        $response = new \stdClass();
        $response->status = count($orderIds) > 0;
        $response->order_id = count($orderIds) > 0 ? $orderIds[0] : null; // Return first order ID for backward compatibility
        $response->order_ids = $orderIds; // All order IDs
        $response->message = count($orderIds) > 0 ? 'Orders created successfully' : 'Failed to create orders';

        return $response;
    }
}
