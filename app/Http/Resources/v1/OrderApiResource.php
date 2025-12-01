<?php

/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 18/4/20
 * Time: 2:07 PM
 */

namespace App\Http\Resources\v1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderApiResource extends JsonResource
{
    public function toArray($request)
    {
        return   [
            'id'               => $this->id,
            'order_code'       => $this->order_code,
            'user_id'          => (int)$this->user_id,
            'total'            => $this->total,
            'sub_total'        => $this->sub_total,
            'delivery_charge'  => $this->delivery_charge,
            'tax_rate'         => $this->tax_rate,
            'tax_amount'       => $this->tax_amount,
            'formatted_tax_rate' => $this->formatted_tax_rate,
            'formatted_tax_amount' => $this->formatted_tax_amount,
            'service_fee_rate' => $this->service_fee_rate,
            'service_fee_amount' => $this->service_fee_amount,
            'formatted_service_fee_rate' => $this->formatted_service_fee_rate,
            'formatted_service_fee_amount' => $this->formatted_service_fee_amount,
            'platform'         => $this->platform,
            'device_id'        => $this->device_id,
            'ip'               => $this->ip,
            'status'           => (int)$this->status,
            'order_type'       =>  (int)$this->order_type,
            'order_type_name'  =>  $this->getOrderType,
            'status_name'      => trans('order_status.' . $this->status),
            'payment_status'   => (int)$this->payment_status,
            'payment_method'   => (int)$this->payment_method,
            'payment_method_name'    => trans('payment_method.' . $this->payment_method),
            'paid_amount'      => $this->paid_amount,
            'address'          => orderAddress($this->address),
            'invoice_id'       => $this->invoice_id,
            'delivery_boy_id'   => (int)$this->delivery_boy_id,
            'restaurant_id'    => (int)$this->restaurant_id,
            'product_received' => (int)$this->product_received,
            'mobile'           => $this->mobile,
            'lat'              => $this->lat,
            'long'             => $this->long,
            'misc'             => $this->misc,
            'created_at'       => $this->created_at->format('d M Y, h:i A'),
            'updated_at'       => $this->updated_at->format('d M Y, h:i A'),
            'time_format'           => $this->created_at->diffForHumans(),
            'date'                  => Carbon::parse($this->created_at)->format('d M Y'),
            'items'            => OrderItemsResource::collection(
                $this->whenLoaded('items')
            ),
            'customer'         => new UserResource($this->user),
            'restaurant'             => new RestaurantResource($this->restaurant),
            'deliveryBoy' => $this->delivery_boy_id == null ? null : new UserResource($this->delivery),

            // Timer information for live tracking
            'timer' => $this->when(
                $this->status == \App\Enums\OrderStatus::PROCESS && $this->process_started_at,
                [
                    'process_started_at' => $this->process_started_at?->toISOString(),
                    'estimated_wait_time' => $this->estimated_wait_time,
                    'remaining_time' => $this->remaining_time,
                    'elapsed_time' => $this->elapsed_time,
                    'is_overdue' => $this->is_overdue,
                    'progress_percentage' => $this->progress_percentage,
                    'formatted_remaining_time' => $this->formatted_remaining_time
                ]
            ) ?: $this->when(
                $this->process_started_at && $this->ready_at,
                [
                    'process_started_at' => $this->process_started_at?->toISOString(),
                    'ready_at' => $this->ready_at?->toISOString(),
                    'estimated_wait_time' => $this->estimated_wait_time,
                    'actual_preparation_time' => $this->actual_preparation_time,
                    'was_on_time' => $this->actual_preparation_time <= $this->estimated_wait_time
                ]
            ),
        ];
    }
}
