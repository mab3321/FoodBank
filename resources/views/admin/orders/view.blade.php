@extends('admin.app')

@section('content')

    <!--====================================
                CONTENT PART START
    =====================================-->
    <div class="row">
        <div class="col-12">
            <div class="custome-breadcrumb">
                {{ Breadcrumbs::render('orders/view') }}
            </div>
        </div>
        <div class="col-12">
            <div class="db-card p-4">
                <div class="flex flex-wrap gap-y-5 items-end justify-between">
                    <div>
                        <div class="flex flex-wrap items-start gap-y-2 gap-x-6 mb-5">
                            <p class="text-2xl font-medium">{{ __('order.order') }}: <span
                                    class="text-heading">#{{ $order->order_code }}</span></p>
                            <div class="flex items-center gap-2 mt-1.5">
                                @php
                                    // Payment status colors
                                    $paymentColors = [
                                        5 => ['text' => '#22C55E', 'bg' => '#DCFCE7'], // Paid - Green
                                        10 => ['text' => '#EF4444', 'bg' => '#FEE2E2'], // Unpaid - Red
                                    ];
                                    $paymentColor = $paymentColors[$order->payment_status] ?? ['text' => '#6B7280', 'bg' => '#F3F4F6'];
                                    
                                    // Order status colors
                                    $statusColors = [
                                        5 => ['text' => '#F59E0B', 'bg' => '#FEF3C7'], // Pending - Yellow
                                        10 => ['text' => '#EF4444', 'bg' => '#FEE2E2'], // Cancel - Red
                                        12 => ['text' => '#EF4444', 'bg' => '#FEE2E2'], // Reject - Red
                                        14 => ['text' => '#3B82F6', 'bg' => '#DBEAFE'], // Accept - Blue
                                        15 => ['text' => '#8B5CF6', 'bg' => '#EDE9FE'], // Process - Purple
                                        17 => ['text' => '#F97316', 'bg' => '#FED7AA'], // On The Way - Orange
                                        20 => ['text' => '#22C55E', 'bg' => '#DCFCE7'], // Completed - Green
                                    ];
                                    $statusColor = $statusColors[$order->status] ?? ['text' => '#6B7280', 'bg' => '#F3F4F6'];
                                @endphp
                                
                                <span class="text-xs capitalize h-5 leading-5 px-2 rounded-3xl font-medium"
                                    style="color: {{ $paymentColor['text'] }}; background-color: {{ $paymentColor['bg'] }}"
                                    title="{{ __('levels.payment_status') }}">{{ trans('payment_status.' . $order->payment_status) ?? 'Unknown' }}</span>
                                    
                                <span class="text-xs capitalize h-5 leading-5 px-2 rounded-3xl font-medium"
                                    style="color: {{ $statusColor['text'] }}; background-color: {{ $statusColor['bg'] }}"
                                    title="{{ __('levels.delivery') }}">{{ trans('order_status.' . $order->status) ?? 'Unknown Status' }}</span>
                            </div>
                        </div>
                        <ul class="flex flex-col gap-2">
                            <li class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.33301 1.3335V3.3335" stroke="#6E7191" stroke-width="1.5"
                                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.667 1.3335V3.3335" stroke="#6E7191" stroke-width="1.5"
                                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M2.33301 6.06006H13.6663" stroke="#6E7191" stroke-width="1.5"
                                        stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M14 5.66683V11.3335C14 13.3335 13 14.6668 10.6667 14.6668H5.33333C3 14.6668 2 13.3335 2 11.3335V5.66683C2 3.66683 3 2.3335 5.33333 2.3335H10.6667C13 2.3335 14 3.66683 14 5.66683Z"
                                        stroke="#6E7191" stroke-width="1.5" stroke-miterlimit="10"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.4635 9.13314H10.4694" stroke="#6E7191" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M10.4635 11.1331H10.4694" stroke="#6E7191" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M7.99666 9.13314H8.00265" stroke="#6E7191" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M7.99666 11.1331H8.00265" stroke="#6E7191" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5.52987 9.13314H5.53585" stroke="#6E7191" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M5.52987 11.1331H5.53585" stroke="#6E7191" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span
                                    class="text-xs">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</span>
                            </li>
                            <li class="text-xs">{{ __('frontend.restaurant') }} : <span
                                    class="text-heading">{{ @$order->restaurant->name }}</span></li>
                            <li class="text-xs">{{ __('levels.order_type') }} : <span
                                        class="text-heading">{{ $order->getOrderType }}</span></li>
                            <li class="text-xs">{{ __('order.payment_method') }} : <span
                                class="text-heading">{{ trans('payment_method.' . $order->payment_method) }}</span>
                            </li>
                            
                        </ul>
                    </div>
                    <div class="flex flex-wrap flex-col gap-3">
                        <div class="btn-box flex gap-3">
                            @if ($order->attachment)
                                <a class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-sky-600 text-white"
                                    href="{{ route('admin.orders.order-file', $order->id) }}"><i
                                        class="fa fa-arrow-circle-down"></i> {{ __('levels.download') }}</a>
                            @endif

                            @if (auth()->user()->myRole == App\Enums\UserRole::RESTAURANTOWNER && $order->status == App\Enums\OrderStatus::PENDING)
                                <a type="button"
                                    href="{{ route('admin.order.change-status', [$order->id, \App\Enums\OrderStatus::REJECT]) }}"
                                    class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#FB4E4E]">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7.99967 1.3335C4.32634 1.3335 1.33301 4.32683 1.33301 8.00016C1.33301 11.6735 4.32634 14.6668 7.99967 14.6668C11.673 14.6668 14.6663 11.6735 14.6663 8.00016C14.6663 4.32683 11.673 1.3335 7.99967 1.3335ZM10.2397 9.5335C10.433 9.72683 10.433 10.0468 10.2397 10.2402C10.1397 10.3402 10.013 10.3868 9.88634 10.3868C9.75967 10.3868 9.63301 10.3402 9.53301 10.2402L7.99967 8.70683L6.46634 10.2402C6.36634 10.3402 6.23967 10.3868 6.11301 10.3868C5.98634 10.3868 5.85967 10.3402 5.75967 10.2402C5.56634 10.0468 5.56634 9.72683 5.75967 9.5335L7.29301 8.00016L5.75967 6.46683C5.56634 6.2735 5.56634 5.9535 5.75967 5.76016C5.95301 5.56683 6.27301 5.56683 6.46634 5.76016L7.99967 7.2935L9.53301 5.76016C9.72634 5.56683 10.0463 5.56683 10.2397 5.76016C10.433 5.9535 10.433 6.2735 10.2397 6.46683L8.70634 8.00016L10.2397 9.5335Z"
                                            fill="white" />
                                    </svg>
                                    <span class="text-sm capitalize text-white">{{ __('order.reject') }}</span>
                                </a>
                                <a type="button"
                                    href="{{ route('admin.order.change-status', [$order->id, \App\Enums\OrderStatus::ACCEPT]) }}"
                                    class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-[#2AC769]">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M7.99967 1.3335C4.32634 1.3335 1.33301 4.32683 1.33301 8.00016C1.33301 11.6735 4.32634 14.6668 7.99967 14.6668C11.673 14.6668 14.6663 11.6735 14.6663 8.00016C14.6663 4.32683 11.673 1.3335 7.99967 1.3335ZM11.1863 6.46683L7.40634 10.2468C7.31301 10.3402 7.18634 10.3935 7.05301 10.3935C6.91967 10.3935 6.79301 10.3402 6.69967 10.2468L4.81301 8.36016C4.61967 8.16683 4.61967 7.84683 4.81301 7.6535C5.00634 7.46016 5.32634 7.46016 5.51967 7.6535L7.05301 9.18683L10.4797 5.76016C10.673 5.56683 10.993 5.56683 11.1863 5.76016C11.3797 5.9535 11.3797 6.26683 11.1863 6.46683Z"
                                            fill="white" />
                                    </svg>
                                    <span class="text-sm capitalize text-white">{{ __('order.accept') }}</span>
                                </a>
                            @elseif (auth()->user()->myRole == App\Enums\UserRole::RESTAURANTOWNER)
                                @php
                                    // Show ALL order statuses - owner can jump to any status directly
                                    $currentStatus = $order->status;
                                    $allStatuses = [
                                        App\Enums\OrderStatus::ACCEPT => ['name' => __('order.accept'), 'color' => 'bg-green-500 hover:bg-green-600', 'icon' => 'fa-check'],
                                        App\Enums\OrderStatus::PROCESS => ['name' => __('order.process'), 'color' => 'bg-blue-500 hover:bg-blue-600', 'icon' => 'fa-cog'],
                                        App\Enums\OrderStatus::ON_THE_WAY => ['name' => 'Ready for Pickup', 'color' => 'bg-purple-500 hover:bg-purple-600', 'icon' => 'fa-clock'],
                                        App\Enums\OrderStatus::COMPLETED => ['name' => __('order.completed'), 'color' => 'bg-green-600 hover:bg-green-700', 'icon' => 'fa-check-circle'],
                                        App\Enums\OrderStatus::REJECT => ['name' => __('order.reject'), 'color' => 'bg-red-500 hover:bg-red-600', 'icon' => 'fa-times'],
                                        App\Enums\OrderStatus::CANCEL => ['name' => __('order.cancel'), 'color' => 'bg-gray-500 hover:bg-gray-600', 'icon' => 'fa-ban']
                                    ];
                                    
                                    // Remove current status from available options
                                    unset($allStatuses[$currentStatus]);
                                @endphp
                                
                                <div class="bg-gray-50 p-4 rounded-lg border">
                                    <div class="flex flex-wrap gap-3 items-center mb-3">
                                        <span class="text-sm font-semibold text-gray-700">Current Status:</span>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            {{ trans('order_status.' . $order->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2 items-center">
                                        <span class="text-sm font-medium text-gray-600 mr-2">Jump to Status:</span>
                                        @foreach($allStatuses as $statusCode => $statusInfo)
                                            <button onclick="changeOrderStatus({{ $order->id }}, {{ $statusCode }}, '{{ $statusInfo['name'] }}')" 
                                                   class="status-btn flex items-center gap-2 px-3 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg {{ $statusInfo['color'] }}">
                                                <i class="fas {{ $statusInfo['icon'] }}"></i>
                                                {{ $statusInfo['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        You can jump directly to any status above
                                    </div>
                                </div>
                                
                            @elseif (auth()->user()->myRole == App\Enums\UserRole::DELIVERYBOY && ($order->status == App\Enums\OrderStatus::ON_THE_WAY || $order->status == App\Enums\OrderStatus::PROCESS))
                                <div class="bg-gray-50 p-4 rounded-lg border">
                                    <div class="flex flex-wrap gap-3 items-center mb-3">
                                        <span class="text-sm font-semibold text-gray-700">Current Status:</span>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            @if($order->status == App\Enums\OrderStatus::PROCESS)
                                                Processing
                                            @elseif($order->status == App\Enums\OrderStatus::ON_THE_WAY)
                                                Ready for Pickup
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @if($order->status == App\Enums\OrderStatus::ON_THE_WAY)
                                        {{-- Staff can mark Ready for Pickup orders as COMPLETED when customer picks up --}}
                                        <div class="flex flex-wrap gap-3 items-center">
                                            <span class="text-sm font-medium text-gray-600 mr-2">Customer Pickup:</span>
                                            <button onclick="changeOrderStatus({{ $order->id }}, {{ App\Enums\OrderStatus::COMPLETED }}, 'Order Picked Up')" 
                                                   class="status-btn flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg bg-green-600 hover:bg-green-700">
                                                <i class="fas fa-hand-holding"></i>
                                                Order Picked Up
                                            </button>
                                        </div>
                                    @elseif($order->status == App\Enums\OrderStatus::PROCESS)
                                        <div class="flex flex-wrap gap-3 items-center">
                                            <span class="text-sm font-medium text-gray-600 mr-2">Product Status:</span>
                                            <button onclick="updateDeliveryStatus({{ $order->id }}, 10, '{{ __('order.not_receive') }}')" 
                                                   class="status-btn flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg bg-red-500 hover:bg-red-600">
                                                <i class="fas fa-times-circle"></i>
                                                {{ __('order.not_receive') }}
                                            </button>
                                            <button onclick="updateDeliveryStatus({{ $order->id }}, 5, '{{ __('order.receive') }}')" 
                                                   class="status-btn flex items-center gap-2 px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg bg-green-500 hover:bg-green-600">
                                                <i class="fas fa-check-circle"></i>
                                                {{ __('order.receive') }}
                                            </button>
                                        </div>
                                    @endif
                                </div>    
                            @endif

                            @if (!($order->status == App\Enums\OrderStatus::PENDING || $order->status == App\Enums\OrderStatus::REJECT))
                                
                                <button onclick="printDiv('invoice-print')" class="flex items-center justify-center gap-2 px-4 h-[38px] rounded shadow-db-card bg-primary">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.66699 3.3335C4.66699 2.22683 5.56033 1.3335 6.66699 1.3335H9.33366C10.4403 1.3335 11.3337 2.22683 11.3337 3.3335C11.3337 3.70016 11.0337 4.00016 10.667 4.00016H5.33366C4.96699 4.00016 4.66699 3.70016 4.66699 3.3335Z" fill="white"/>
                                        <path d="M11.8337 10C11.8337 10.2733 11.607 10.5 11.3337 10.5H10.667V12.6667C10.667 13.7733 9.77366 14.6667 8.66699 14.6667H7.33366C6.22699 14.6667 5.33366 13.7733 5.33366 12.6667V10.5H4.66699C4.39366 10.5 4.16699 10.2733 4.16699 10C4.16699 9.72667 4.39366 9.5 4.66699 9.5H11.3337C11.607 9.5 11.8337 9.72667 11.8337 10Z" fill="white"/>
                                        <path d="M12 4.6665H4C2.66667 4.6665 2 5.33317 2 6.6665V9.99984C2 11.3332 2.66667 11.9998 4 11.9998H4.25C4.48012 11.9998 4.66667 11.8133 4.66667 11.5832C4.66667 11.3531 4.4742 11.1735 4.25894 11.0921C3.81746 10.9253 3.5 10.4967 3.5 9.99984C3.5 9.35984 4.02667 8.83317 4.66667 8.83317H11.3333C11.9733 8.83317 12.5 9.35984 12.5 9.99984C12.5 10.4967 12.1825 10.9253 11.7411 11.0921C11.5258 11.1735 11.3333 11.3531 11.3333 11.5832C11.3333 11.8133 11.5199 11.9998 11.75 11.9998H12C13.3333 11.9998 14 11.3332 14 9.99984V6.6665C14 5.33317 13.3333 4.6665 12 4.6665ZM6.66667 7.83317H4.66667C4.39333 7.83317 4.16667 7.6065 4.16667 7.33317C4.16667 7.05984 4.39333 6.83317 4.66667 6.83317H6.66667C6.94 6.83317 7.16667 7.05984 7.16667 7.33317C7.16667 7.6065 6.94 7.83317 6.66667 7.83317Z" fill="white"/>
                                    </svg>
                                    <span class="text-sm capitalize text-white">{{ __('levels.print_invoice') }}</span>
                                </button>

                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Enhanced Restaurant Order Management Panel --}}
        @if (auth()->user()->myRole == App\Enums\UserRole::RESTAURANTOWNER || auth()->user()->myRole == App\Enums\UserRole::ADMIN)
        <div class="col-12">
            <div class="db-card mb-4">
                <div class="db-card-header">
                    <h3 class="db-card-title">🍽️ Order Status Management</h3>
                </div>
                <div class="db-card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Current Status Display --}}
                        <div class="col-span-full mb-4">
                            <div class="flex items-center gap-3">
                                <h4 class="text-lg font-semibold">Current Status:</h4>
                                <span class="badge px-3 py-1 rounded-full text-white
                                    @if($order->status == 5) bg-gray-500
                                    @elseif($order->status == 10 || $order->status == 12) bg-red-500
                                    @elseif($order->status == 14) bg-green-500
                                    @elseif($order->status == 15) bg-blue-500
                                    @elseif($order->status == 17) bg-yellow-500
                                    @elseif($order->status == 20) bg-green-600
                                    @endif">
                                    {{ trans('order_status.' . $order->status) }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Quick Action Buttons --}}
                        @if($order->status == App\Enums\OrderStatus::PENDING)
                            <a href="{{ route('admin.order.change-status', [$order->id, App\Enums\OrderStatus::ACCEPT]) }}" 
                               class="btn bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors"
                               onclick="return confirm('Accept this order?')">
                                <i class="fas fa-check mr-2"></i>Accept Order
                            </a>
                            <a href="{{ route('admin.order.change-status', [$order->id, App\Enums\OrderStatus::REJECT]) }}" 
                               class="btn bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors"
                               onclick="return confirm('Reject this order?')">
                                <i class="fas fa-times mr-2"></i>Reject Order
                            </a>
                        @elseif($order->status == App\Enums\OrderStatus::ACCEPT)
                            <a href="{{ route('admin.order.change-status', [$order->id, App\Enums\OrderStatus::PROCESS]) }}" 
                               class="btn bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors"
                               onclick="return confirm('Start processing this order?')">
                                <i class="fas fa-cog mr-2"></i>Start Processing
                            </a>
                        @elseif($order->status == App\Enums\OrderStatus::PROCESS)
                            @if($order->order_type == App\Enums\OrderTypeStatus::PICKUP)
                                <a href="{{ route('admin.order.change-status', [$order->id, App\Enums\OrderStatus::COMPLETED]) }}" 
                                   class="btn bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors"
                                   onclick="return confirm('Mark as completed?')">
                                    <i class="fas fa-check-circle mr-2"></i>Complete (Pickup)
                                </a>
                            @else
                                <a href="{{ route('admin.order.change-status', [$order->id, App\Enums\OrderStatus::ON_THE_WAY]) }}" 
                                   class="btn bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 transition-colors"
                                   onclick="return confirm('Mark as ready for pickup?')">
                                    <i class="fas fa-clock mr-2"></i>Ready for Pickup
                                </a>
                            @endif
                        @elseif($order->status == App\Enums\OrderStatus::ON_THE_WAY)
                            <a href="{{ route('admin.order.change-status', [$order->id, App\Enums\OrderStatus::COMPLETED]) }}" 
                               class="btn bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors"
                               onclick="return confirm('Mark as picked up?')">
                                <i class="fas fa-hand-holding mr-2"></i>Mark Picked Up
                            </a>
                        @endif
                        
                        {{-- Cancel option (always available) --}}
                        @if(!in_array($order->status, [App\Enums\OrderStatus::COMPLETED, App\Enums\OrderStatus::CANCEL]))
                            <a href="{{ route('admin.order.change-status', [$order->id, App\Enums\OrderStatus::CANCEL]) }}" 
                               class="btn bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors"
                               onclick="return confirm('Cancel this order?')">
                                <i class="fas fa-ban mr-2"></i>Cancel Order
                            </a>
                        @endif
                    </div>

                    {{-- Order Timer Component --}}
                    @if($order->status == App\Enums\OrderStatus::PROCESS && $order->process_started_at)
                    <div class="mt-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-100 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xl font-bold text-gray-800">
                                <i class="fas fa-clock text-blue-600 mr-2"></i>
                                Order Preparation Timer
                            </h4>
                            <div class="text-sm text-gray-600">
                                Started: {{ $order->process_started_at->format('H:i') }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Timer Display --}}
                            <div class="text-center">
                                <div class="text-4xl font-bold mb-2 countdown-display
                                    @if($order->is_overdue) text-red-600 animate-pulse
                                    @elseif($order->remaining_time <= 5) text-orange-500
                                    @else text-green-600 @endif" 
                                    id="countdown-{{ $order->id }}">
                                    {{ $order->formatted_remaining_time }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    @if($order->is_overdue)
                                        <span class="text-red-600 font-semibold">⚠️ OVERDUE</span>
                                    @else
                                        Remaining Time
                                    @endif
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="flex items-center">
                                <div class="w-full">
                                    <div class="flex justify-between text-sm mb-2">
                                        <span>Progress</span>
                                        <span id="progress-text-{{ $order->id }}">{{ $order->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-1000 progress-bar
                                            @if($order->is_overdue) bg-red-500
                                            @elseif($order->progress_percentage >= 80) bg-orange-500
                                            @else bg-green-500 @endif"
                                            id="progress-bar-{{ $order->id }}"
                                            style="width: {{ min($order->progress_percentage, 100) }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Order Info --}}
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-700 mb-1">
                                    {{ $order->estimated_wait_time }} min
                                </div>
                                <div class="text-sm text-gray-600">
                                    Estimated Time
                                </div>
                                <div class="text-sm text-gray-500 mt-2">
                                    Elapsed: <span id="elapsed-{{ $order->id }}">{{ $order->elapsed_time }}</span> min
                                </div>
                            </div>
                        </div>

                        {{-- Quick Action for Overdue Orders --}}
                        @if($order->is_overdue)
                        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-red-700">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span class="font-semibold">Order is overdue!</span>
                                </div>
                                <div class="flex gap-2">
                                    @if($order->order_type == App\Enums\OrderTypeStatus::PICKUP)
                                        <a href="{{ route('admin.order.change-status', [$order->id, App\Enums\OrderStatus::COMPLETED]) }}" 
                                           class="btn bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors text-sm"
                                           onclick="return confirm('Mark this order as ready for pickup?')">
                                            <i class="fas fa-check mr-1"></i>Mark Ready
                                        </a>
                                    @else
                                        <a href="{{ route('admin.order.change-status', [$order->id, App\Enums\OrderStatus::ON_THE_WAY]) }}" 
                                           class="btn bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors text-sm"
                                           onclick="return confirm('Mark this order as ready for pickup?')">
                                            <i class="fas fa-check mr-1"></i>Mark Ready
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @elseif($order->process_started_at && $order->ready_at)
                    {{-- Completed Timer Info --}}
                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="text-lg font-semibold text-green-800 mb-2">
                            <i class="fas fa-check-circle mr-2"></i>Order Completed
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Started:</span>
                                <div class="font-semibold">{{ $order->process_started_at->format('H:i') }}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Completed:</span>
                                <div class="font-semibold">{{ $order->ready_at->format('H:i') }}</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Estimated:</span>
                                <div class="font-semibold">{{ $order->estimated_wait_time }} min</div>
                            </div>
                            <div>
                                <span class="text-gray-600">Actual:</span>
                                <div class="font-semibold {{ $order->actual_preparation_time > $order->estimated_wait_time ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $order->actual_preparation_time }} min
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        <div class="col-12 sm:col-6">
            <div class="db-card">
                <div class="db-card-header">
                    <h3 class="db-card-title">{{ __('order.order_details') }}</h3>
                </div>
                <div class="db-card-body">
                    <div class="pl-3">

                        @foreach ($items as $itemKey => $item)
                            <div class="mb-3 pb-3 border-b last:mb-0 last:pb-0 last:border-b-0 border-gray-2">
                                <div class="flex items-center gap-3 relative">
                                    <h3
                                        class="absolute top-5 -left-3 text-sm w-[26px] h-[26px] leading-[26px] text-center rounded-full text-white bg-heading">
                                        {{ $item->quantity }}</h3>
                                    <img class="w-16 h-16 rounded-lg flex-shrink-0" src="{{ $item->menuItem->image }}"
                                        alt="thumbnail">
                                    <div class="w-full">
                                        <ad href="#"
                                            class="text-sm font-medium capitalize transition text-heading hover:underline">{{ $item->menuItem->name }}</ad>
                                        @if (!blank($item->variation))
                                            <p class="capitalize text-xs mb-1.5">{{ __('order.variation') }} :
                                                {{ json_decode($item->variation, true)['name'] }}</p>
                                        @endif

                                        <h3 class="text-xs font-semibold">{{ currencyFormat($item->unit_price) }}</h3>
                                    </div>
                                </div>

                                @if (!empty(json_decode($item->options)) || !blank($item->instructions))
                                    <ul class="flex flex-col gap-1.5 mt-2">
                                        @if (!empty(json_decode($item->options)))
                                            <li class="flex gap-1">
                                                <h3 class="capitalize text-xs w-fit whitespace-nowrap">
                                                    {{ __('order.options') }}:</h3>
                                                <ul class="text-xs w-full py-1">
                                                    @foreach (json_decode($item->options, true) as $option)
                                                        <li class="inline-block">
                                                            {{ $option['name'] }}{{ $loop->last ? '' : ' ,' }}</li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endif

                                        @if (!blank($item->instructions))
                                            <li class="flex gap-1">
                                                <h3 class="capitalize text-xs w-fit whitespace-nowrap">
                                                    {{ __('levels.instructions') }} :</h3>
                                                <p class="text-xs">{{ $item->instructions }}</p>
                                            </li>
                                        @endif
                                    </ul>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 sm:col-6">
            <div class="row">
                <div class="col-12">
                    <div class="db-card p-1">
                        <ul class="flex flex-col gap-2 py-3 px-5">
                            @if ($order->discount && $order->discount->amount > 0 && Schema::hasColumn('coupons', 'slug'))
                                <li class="flex items-center justify-between text-heading">
                                    <span class="text-sm leading-6 capitalize">{{ __('levels.discount') }}</span>
                                    <span
                                        class="text-sm leading-6 capitalize">{{ currencyFormat($order->discount->amount) }}</span>
                                </li>
                            @endif
                            <li class="flex items-center justify-between text-heading">
                                <span class="text-sm leading-6 capitalize">{{ __('levels.sub_total') }}</span>
                                <span class="text-sm leading-6 capitalize">{{ currencyFormat($order->sub_total) }}</span>
                            </li>
                            @if ($order->order_type !== App\Enums\OrderTypeStatus::PICKUP)
                            <li class="flex items-center justify-between text-heading">
                                <span class="text-sm leading-6 capitalize">{{ __('levels.delivery_charge') }}</span>
                                <span
                                    class="text-sm leading-6 capitalize font-semibold text-[#1AB759]">{{ currencyFormat($order->delivery_charge) }}</span>
                            </li>
                            @endif

                            @if ($order->tax_amount > 0)
                            <li class="flex items-center justify-between text-heading">
                                <span class="text-sm leading-6 capitalize">{{ __('levels.tax_rate') }} ({{ $order->formatted_tax_rate }})</span>
                                <span class="text-sm leading-6 capitalize font-semibold text-[#1AB759]">{{ currencyFormat($order->tax_amount) }}</span>
                            </li>
                            @endif
                        </ul>
                        <div class="flex items-center justify-between py-3 border-t border-dashed border-[#EFF0F6]">
                            <h4 class="text-sm leading-6 font-bold capitalize">{{ __('levels.total') }}</h4>
                            <h5 class="text-sm leading-6 font-bold capitalize">{{ currencyFormat($order->total) }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="db-card">
                        <div class="db-card-header">
                            <h3 class="db-card-title">{{ __('order.customer_information') }}</h3>
                        </div>
                        <div class="db-card-body">
                            <div class="flex items-center gap-3 mb-4">
                                <img class="w-8 rounded-full" src="{{ $order->user->image }}" alt="avatar">
                                <h4 class="font-semibold text-sm capitalize text-[#374151]">
                                    {{ $order->user->name ?? null }}
                                </h4>
                            </div>
                            
                            <ul class="flex flex-col gap-3 py-4 border-t border-[#EFF0F6]">
                                <li class="flex items-center gap-2.5">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M11.333 13.6668H4.66634C2.66634 13.6668 1.33301 12.6668 1.33301 10.3335V5.66683C1.33301 3.3335 2.66634 2.3335 4.66634 2.3335H11.333C13.333 2.3335 14.6663 3.3335 14.6663 5.66683V10.3335C14.6663 12.6668 13.333 13.6668 11.333 13.6668Z"
                                            stroke="#6E7191" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M11.3337 6L9.24699 7.66667C8.56032 8.21333 7.43366 8.21333 6.74699 7.66667L4.66699 6"
                                            stroke="#6E7191" stroke-width="1.5" stroke-miterlimit="10"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="text-sm text-[#374151]">{{ $order->user->email }}</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M14.6463 12.2202C14.6463 12.4602 14.593 12.7068 14.4797 12.9468C14.3663 13.1868 14.2197 13.4135 14.0263 13.6268C13.6997 13.9868 13.3397 14.2468 12.933 14.4135C12.533 14.5802 12.0997 14.6668 11.633 14.6668C10.953 14.6668 10.2263 14.5068 9.45967 14.1802C8.69301 13.8535 7.92634 13.4135 7.16634 12.8602C6.39967 12.3002 5.67301 11.6802 4.97967 10.9935C4.29301 10.3002 3.67301 9.5735 3.11967 8.8135C2.57301 8.0535 2.13301 7.2935 1.81301 6.54016C1.49301 5.78016 1.33301 5.0535 1.33301 4.36016C1.33301 3.90683 1.41301 3.4735 1.57301 3.0735C1.73301 2.66683 1.98634 2.2935 2.33967 1.96016C2.76634 1.54016 3.23301 1.3335 3.72634 1.3335C3.91301 1.3335 4.09967 1.3735 4.26634 1.4535C4.43967 1.5335 4.59301 1.6535 4.71301 1.82683L6.25967 4.00683C6.37967 4.1735 6.46634 4.32683 6.52634 4.4735C6.58634 4.6135 6.61967 4.7535 6.61967 4.88016C6.61967 5.04016 6.57301 5.20016 6.47967 5.3535C6.39301 5.50683 6.26634 5.66683 6.10634 5.82683L5.59967 6.3535C5.52634 6.42683 5.49301 6.5135 5.49301 6.62016C5.49301 6.6735 5.49967 6.72016 5.51301 6.7735C5.53301 6.82683 5.55301 6.86683 5.56634 6.90683C5.68634 7.12683 5.89301 7.4135 6.18634 7.76016C6.48634 8.10683 6.80634 8.46016 7.15301 8.8135C7.51301 9.16683 7.85967 9.4935 8.21301 9.7935C8.55967 10.0868 8.84634 10.2868 9.07301 10.4068C9.10634 10.4202 9.14634 10.4402 9.19301 10.4602C9.24634 10.4802 9.29967 10.4868 9.35967 10.4868C9.47301 10.4868 9.55967 10.4468 9.63301 10.3735L10.1397 9.8735C10.3063 9.70683 10.4663 9.58016 10.6197 9.50016C10.773 9.40683 10.9263 9.36016 11.093 9.36016C11.2197 9.36016 11.353 9.38683 11.4997 9.44683C11.6463 9.50683 11.7997 9.5935 11.9663 9.70683L14.173 11.2735C14.3463 11.3935 14.4663 11.5335 14.5397 11.7002C14.6063 11.8668 14.6463 12.0335 14.6463 12.2202Z"
                                            stroke="#6E7191" stroke-width="1.5" stroke-miterlimit="10" />
                                        <path
                                            d="M12.3333 6.00033C12.3333 5.60033 12.02 4.98699 11.5533 4.48699C11.1267 4.02699 10.56 3.66699 10 3.66699"
                                            stroke="#6E7191" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M14.6667 6.00016C14.6667 3.42016 12.58 1.3335 10 1.3335"
                                            stroke="#6E7191" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span class="text-sm text-[#374151]">{{ $order->mobile ?? null }}</span>
                                </li>
                            </ul>
                            
                            @if ($order->order_type != 2)
                                <div class="flex items-start gap-3 pt-4 border-t border-[#EFF0F6]">
                                    <svg class="flex-shrink-0" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M20.6201 8.45C19.5701 3.83 15.5401 1.75 12.0001 1.75C12.0001 1.75 12.0001 1.75 11.9901 1.75C8.4601 1.75 4.4201 3.82 3.3701 8.44C2.2001 13.6 5.3601 17.97 8.2201 20.72C9.2801 21.74 10.6401 22.25 12.0001 22.25C13.3601 22.25 14.7201 21.74 15.7701 20.72C18.6301 17.97 21.7901 13.61 20.6201 8.45ZM12.0001 13.46C10.2601 13.46 8.8501 12.05 8.8501 10.31C8.8501 8.57 10.2601 7.16 12.0001 7.16C13.7401 7.16 15.1501 8.57 15.1501 10.31C15.1501 12.05 13.7401 13.46 12.0001 13.46Z"
                                            fill="#1F1F39" />
                                    </svg>
                                    <span
                                        class="text-sm w-full max-w-[200px] leading-6 text-[#374151]">{{ orderAddress($order->address) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--====================================
                CONTENT PART END
    =====================================-->

    <section class="section">

        <div id="invoice-print" class="hidden">
            <div class="invoice">
                <style>
                    .invoice {
                        max-width: 390px;
                        width: 100%;
                        margin: auto;
                        padding: 8px;
                        font-family: 'OpenSauceOne', sans-serif;
                    }
        
                    p {
                        margin-top: 4px;
                        margin-bottom: 0px;
                    }
        
                    h2,
                    h3 {
                        font-size: 32px;
                        font-weight: bolder;
                        margin-top: 4px;
                        margin-bottom: 2px;
                    }
        
                    h3 {
                        font-size: 28px;
                        margin-bottom: 8px;
                    }
        
                    p,
                    td {
                        font-size: 16px;
                    }
        
                    .invoiceFooter p {
                        font-size: 14px;
                        font-weight: 400;
        
                    }
        
                    .invoiceFooter small {
                        font-size: 12px;
                        margin-top: 24px;
                    }
        
                    .border-dashed {
                        border-top: 1px dashed gainsboro;
                        margin: 0 25px 0 25px;
                    }
        
                    .text-center {
                        text-align: center;
                    }
        
                    .text-start {
                        text-align: start;
                    }
        
                    .text-end {
                        text-align: end;
                    }
        
                    .align-top {
                        vertical-align: top;
                    }
        
                    .min-w-80 {
                        min-width: 80px;
                        width: 80px;
                    }
        
                    ul {
                        list-style: none;
                        padding-left: 0px;
                    }
                    .ps-5{
                        padding-left: 80px;
                    }
                    table{
                        margin: auto;
                        width: 90%;
                        border-top: 1px dashed gainsboro;
                    }
                    .mt-2{
                        margin-top: 4px;
                    }
                    .mb-2{
                        margin-bottom: 10px;
                    }
                    .pb-2{
                        padding-bottom: 10px;
                    }
                    .pt-3{
                        padding-top: 8px;
                    }
                </style>
                <div class="text-center pb-2">
                    <h2> {{ setting('site_name') ? setting('site_name') : '' }} {{ __('frontend.restaurant') }} </h2>
                    <h3>{{ __('frontend.food_ordering_system') }}</h3>
                    <p> {{ __('frontend.email') }}: {{ setting('site_email') }}</p>
                    <p class="mt-2 mb-2"> {{ __('frontend.tel') }}: {{ setting('site_phone_number') }}</p>
                </div>
                <div class="border-dashed">
                    <ul>
                        <li class="pt-1">#{{ $order->order_code }}</li>
                        <li class="pb-1 d-flex justify-content-between align-items-center">
                            <span class="text-start mt-1"> {{ $order->created_at->format('d M Y') }}</span>
                            <span class="text-end">{{ $order->created_at->format('h:i A') }}</span>
                        </li>
                    </ul>
                </div>
                <table class="pt-3">
                    <thead>
                        <td class="text-start pb-3">{{ __('frontend.quantity') }}</td>
                        <td class="text-center pb-3">{{ __('frontend.item') }} </td>
                        <td class="text-end pb-2">{{ __('frontend.totals') }}</td>
                    </thead>
                    <tbody>

                    @foreach ($items as $itemKey => $item)
                        <tr>
                            <td class="text-start align-top pb-2 min-w-80"> {{ $item->quantity }}</td>
                            <td class="text-start pb-2"> 
                                {{ $item->menuItem->name }} {{ $item->variation ? ' ( ' . $item->variation['name'] . ' )' : '' }} 
                                @if (!blank($item->options))
                                    @foreach (json_decode($item->options, true) as $option)
                                    <span class="block">
                                        <small>{{ $option['name'] }}</small>
                                    </span>
                                    @endforeach
                                @endif
                            </td>
                            <td class="text-end align-top pb-2 min-w-80"> {{ currencyFormat($item->item_total) }}</td>
                        </tr>
                    @endforeach
                        
                    </tbody>
                </table>
                <table class="border-dashed ps-5 pt-3">
                    <tbody>
                        <tr>
                            <td class="text-start"> {{ __('frontend.subtotal') }}:</td>
                            <td class="text-end align-top">{{ currencyFormat($order->sub_total) }}</td>
                        </tr>

                        @if ($order->discount && $order->discount->amount > 0 && Schema::hasColumn('coupons', 'slug'))
                            <tr>
                                <td class="text-start"> {{ __('frontend.discount') }}: </td>
                                <td class="text-end align-top"> {{ currencyFormat($order->discount->amount) }}</td>
                            </tr>
                        @endif

                        @if ($order->tax_amount > 0)
                            <tr>
                                <td class="text-start"> {{ __('frontend.tax') }} ({{ $order->formatted_tax_rate }}): </td>
                                <td class="text-end align-top"> {{ currencyFormat($order->tax_amount) }}</td>
                            </tr>
                        @endif

                        <tr>
                            <td class="text-start"> {{ __('frontend.total') }}:</td>
                            <td class="text-end align-top"> {{ currencyFormat($order->total) }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="border-dashed">
                    <tbody class="">
                        <tr>
                            <td class="">{{ __('levels.order_type') }}:</td>
                            <td class="text-end">{{ $order->getOrderType }}</td>
                        </tr>
                        <tr>
                            <td class="">{{ __('frontend.payment_status') }}:</td>
                            <td class="text-end">{{ trans('payment_status.' . $order->payment_status) ?? null }}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="border-dashed">
                    <tbody class="pt-1 pb-1">
                        <tr class="">
                            <td class="">{{ __('levels.customer') }}:</td>
                            <td class="text-end">{{ $order->user->name ?? '' }}</td>
                        </tr>
                        <tr class="">
                            <td class="">{{ __('frontend.phone') }}: </td>
                            <td class="text-end">{{ $order->mobile ?? '' }}</td>
                        </tr>
                        <tr class="">
                            <td class="">{{ __('frontend.address') }}:</td>
                            <td class="text-end">{{ orderAddress($order->address) }}</td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-center border-dashed pt-3"> {{ __('levels.thank_you') }} </p>
                <div class="text-end invoiceFooter mt-4">
                    <small>{{ setting('site_name') ? setting('site_name') : '' }}</small>
                    <p>{{ __('frontend.restaurant') }} {{ __('frontend.food_ordering_system') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@php
    $baseUrl = env('APP_URL');
@endphp
@endsection

@push('css')
<style>
    .status-btn {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--tw-bg-opacity) 0%, rgba(0,0,0,0.1) 100%);
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .status-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.5s;
    }
    
    .status-btn:hover:before {
        left: 100%;
    }
    
    .status-btn:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px) scale(1.05);
    }
    
    .status-btn:active {
        transform: translateY(0) scale(0.98);
        transition: all 0.1s;
    }
    
    .status-btn:disabled {
        transform: none !important;
        opacity: 0.7 !important;
        cursor: not-allowed !important;
    }
    
    /* Success animation */
    @keyframes successPulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
    
    .status-btn.success-animation {
        animation: successPulse 1s ease-out;
    }
    
    /* Status-specific gradient backgrounds */
    .bg-green-500 {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .bg-green-600 {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
    }
    
    .bg-red-500 {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    
    .bg-blue-500 {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    
    .bg-yellow-500 {
        background: linear-gradient(135deg, #eab308 0%, #d97706 100%);
    }
    
    .bg-purple-500 {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    
    .bg-orange-500 {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }
    
    .bg-gray-500 {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    }
    
    /* Status display container styling */
    .bg-gray-50 {
        background-color: #f9fafb;
        border: 1px solid #e5e7eb;
    }
    
    /* Current status badge styling */
    .bg-blue-100 {
        background-color: #dbeafe;
    }
    
    .text-blue-800 {
        color: #1e40af;
    }
    
    /* Responsive button layout */
    @media (max-width: 640px) {
        .status-btn {
            width: 100%;
            justify-content: center;
        }
    }
    
    /* Loading spinner animation */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .fa-spin {
        animation: spin 1s linear infinite;
    }

    /* Enhanced Timer animations */
    .countdown-display {
        transition: all 0.3s ease-in-out;
        font-variant-numeric: tabular-nums;
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
    }
    
    .progress-bar {
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.3s ease-in-out;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    @keyframes pulse {
        0%, 100% { 
            opacity: 1; 
            transform: scale(1);
        }
        50% { 
            opacity: 0.8; 
            transform: scale(1.02);
        }
    }
    
    .animate-pulse {
        animation: pulse 1.5s ease-in-out infinite;
    }
    
    @keyframes slideIn {
        0% { 
            transform: translateY(-5px); 
            opacity: 0.8; 
        }
        100% { 
            transform: translateY(0); 
            opacity: 1; 
        }
    }
    
    .timer-update {
        animation: slideIn 0.2s ease-out;
    }
    
    /* Overdue warning animations */
    @keyframes warning-glow {
        0%, 100% { 
            box-shadow: 0 0 5px rgba(239, 68, 68, 0.3);
        }
        50% { 
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.6);
        }
    }
    
    .overdue-glow {
        animation: warning-glow 2s ease-in-out infinite;
    }
    
    /* Smooth color transitions */
    .text-green-600, .text-orange-500, .text-red-600 {
        transition: color 0.4s ease-in-out;
    }
    
    .bg-green-500, .bg-orange-500, .bg-red-500 {
        transition: background-color 0.4s ease-in-out;
    }
</style>
@endpush

@push('js')
    <script ></script>
    <script>
        // Function to handle order status changes
        function changeOrderStatus(orderId, status, statusName) {
            // Show confirmation dialog
            if (!confirm(`Are you sure you want to change order status to "${statusName}"?`)) {
                return;
            }
            
            // Find the clicked button and disable it
            let clickedButton = event.target.closest('button');
            let originalContent = clickedButton.innerHTML;
            clickedButton.disabled = true;
            clickedButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Updating...';
            
            let baseUrl = '{{ url('/') }}/admin/order/change-status';
            let url = baseUrl + '/' + orderId + '/' + status;
            
            console.log('Changing order status to:', statusName, 'URL:', url);
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    console.log('Order status changed successfully');
                    clickedButton.innerHTML = '<i class="fas fa-check mr-1"></i>Success!';
                    clickedButton.classList.remove('bg-green-500', 'bg-red-500', 'bg-blue-500', 'bg-yellow-500', 'bg-gray-500', 'bg-green-600');
                    clickedButton.classList.add('bg-green-600');
                    
                    // Show success message
                    showNotification('Order status updated successfully!', 'success');
                    
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.log('Error changing order status:', error);
                    clickedButton.disabled = false;
                    clickedButton.innerHTML = originalContent;
                    
                    // Show error message
                    let errorMessage = 'Error updating order status. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification(errorMessage, 'error');
                }
            });
        }

        // Function to handle delivery status changes
        function updateDeliveryStatus(orderId, status, statusName) {
            // Show confirmation dialog
            if (!confirm(`Are you sure you want to mark this order as "${statusName}"?`)) {
                return;
            }
            
            // Find the clicked button and disable it
            let clickedButton = event.target.closest('button');
            let originalContent = clickedButton.innerHTML;
            clickedButton.disabled = true;
            clickedButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Updating...';
            
            let baseUrl = '{{ url('/') }}/admin/orders/product-receive';
            let url = baseUrl + '/' + orderId + '/' + status;
            
            console.log('Updating delivery status to:', statusName, 'URL:', url);
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    console.log('Delivery status updated successfully');
                    clickedButton.innerHTML = '<i class="fas fa-check mr-1"></i>Success!';
                    clickedButton.classList.remove('bg-red-500', 'bg-green-500');
                    clickedButton.classList.add('bg-green-600');
                    
                    // Show success message
                    showNotification('Delivery status updated successfully!', 'success');
                    
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.log('Error updating delivery status:', error);
                    clickedButton.disabled = false;
                    clickedButton.innerHTML = originalContent;
                    
                    // Show error message
                    let errorMessage = 'Error updating delivery status. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotification(errorMessage, 'error');
                }
            });
        }

        // Function to show notifications
        function showNotification(message, type = 'info') {
            // Create notification element
            let notification = $(`
                <div class="fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-x-full opacity-0 ${
                    type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 
                    'bg-blue-500'
                }">
                    <div class="flex items-center gap-2">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `);
            
            // Add to body and animate in
            $('body').append(notification);
            setTimeout(() => {
                notification.removeClass('translate-x-full opacity-0');
            }, 100);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.addClass('translate-x-full opacity-0');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Add hover effects and animations to status buttons
        $(document).ready(function() {
            $('.status-btn').hover(
                function() {
                    $(this).addClass('shadow-xl');
                },
                function() {
                    $(this).removeClass('shadow-xl');
                }
            );
        });

        // Real-time countdown timer for order preparation
        @if($order->status == App\Enums\OrderStatus::PROCESS && $order->process_started_at)
        let timerInterval;
        let processStartTime = new Date('{{ $order->process_started_at->toISOString() }}');
        let estimatedTime = {{ $order->estimated_wait_time }}; // in minutes
        let orderId = {{ $order->id }};
        let lastNotificationTime = 0;
        let isOverdue = false;

        function updateTimer() {
            let now = new Date();
            let elapsedSeconds = Math.floor((now - processStartTime) / 1000);
            let elapsedMinutes = Math.floor(elapsedSeconds / 60);
            let estimatedSeconds = estimatedTime * 60;
            let remainingSeconds = Math.max(0, estimatedSeconds - elapsedSeconds);
            
            // Calculate remaining time in minutes and seconds
            let remainingMinutes = Math.floor(remainingSeconds / 60);
            let remainingSecsOnly = remainingSeconds % 60;
            
            // Calculate progress percentage with smooth transitions
            let progressPercentage = Math.min(100, (elapsedSeconds / estimatedSeconds) * 100);
            
            // Format time display (mm:ss)
            let timeDisplay;
            if (remainingSeconds <= 0) {
                let overdueSeconds = elapsedSeconds - estimatedSeconds;
                let overdueMinutes = Math.floor(overdueSeconds / 60);
                let overdueSecs = overdueSeconds % 60;
                timeDisplay = `-${overdueMinutes}:${overdueSecs.toString().padStart(2, '0')}`;
            } else {
                timeDisplay = `${remainingMinutes}:${remainingSecsOnly.toString().padStart(2, '0')}`;
            }
            
            let countdownElement = $('#countdown-' + orderId);
            let progressBarElement = $('#progress-bar-' + orderId);
            let progressTextElement = $('#progress-text-' + orderId);
            let elapsedElement = $('#elapsed-' + orderId);
            
            // Smooth animations for updates
            if (countdownElement.text() !== timeDisplay) {
                countdownElement.addClass('animate-pulse').text(timeDisplay);
                setTimeout(() => countdownElement.removeClass('animate-pulse'), 200);
            }
            
            progressTextElement.text(Math.round(progressPercentage) + '%');
            elapsedElement.text(elapsedMinutes);
            
            // Smooth progress bar transition
            progressBarElement.css({
                'width': progressPercentage + '%',
                'transition': 'width 0.5s ease-in-out'
            });
            
            // Dynamic color updates with smooth transitions
            if (remainingSeconds <= 0) {
                // Overdue - red and pulsing
                if (!isOverdue) {
                    isOverdue = true;
                    showOverdueNotification();
                }
                countdownElement.removeClass('text-green-600 text-orange-500').addClass('text-red-600');
                progressBarElement.removeClass('bg-green-500 bg-orange-500').addClass('bg-red-500');
                
                // Pulse effect for overdue
                if (Math.floor(elapsedSeconds) % 2 === 0) {
                    countdownElement.addClass('animate-pulse');
                } else {
                    countdownElement.removeClass('animate-pulse');
                }
            } else if (remainingSeconds <= 300) { // Last 5 minutes
                // Almost due - orange
                countdownElement.removeClass('text-green-600 text-red-600 animate-pulse').addClass('text-orange-500');
                progressBarElement.removeClass('bg-green-500 bg-red-500').addClass('bg-orange-500');
            } else {
                // Normal - green
                countdownElement.removeClass('text-red-600 text-orange-500 animate-pulse').addClass('text-green-600');
                progressBarElement.removeClass('bg-red-500 bg-orange-500').addClass('bg-green-500');
            }
        }

        function showOverdueNotification() {
            // Play notification sound (using Web Audio API for better browser support)
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);
            } catch (e) {
                console.log('Audio notification not supported');
            }
            
            // Browser notification
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('🚨 Order Overdue!', {
                    body: `Order #{{ $order->order_code }} is overdue. Please check kitchen status.`,
                    icon: '{{ asset("backend/images/logo.png") }}',
                    badge: '{{ asset("backend/images/logo.png") }}',
                    tag: 'overdue-{{ $order->id }}',
                    requireInteraction: true
                });
            }
            
            // Enhanced visual notification
            showNotification('🚨 Order #{{ $order->order_code }} is OVERDUE! Check if ready for pickup.', 'error');
            
            // Flash the page title with more dramatic effect
            let originalTitle = document.title;
            let flashCount = 0;
            let flashInterval = setInterval(() => {
                document.title = flashCount % 2 === 0 ? '🚨 OVERDUE ORDER!' : originalTitle;
                flashCount++;
                if (flashCount >= 10) {
                    clearInterval(flashInterval);
                    document.title = '🚨 ' + originalTitle;
                }
            }, 500);
            
            // Add visual glow effect to timer
            $('#countdown-' + orderId).parent().addClass('overdue-glow');
        }

        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // Start the timer with real-time updates
        updateTimer(); // Initial call
        timerInterval = setInterval(updateTimer, 1000); // Update every second for smooth countdown

        // Add smooth animations and enhanced visual effects
        $('.countdown-display, .progress-bar').css({
            'transition': 'all 0.3s ease-in-out'
        });
        
        // Request notification permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    showNotification('✅ Notifications enabled for order updates!', 'success');
                }
            });
        }
        
        // Add visual feedback for progress milestones
        let lastProgressMilestone = 0;
        let originalUpdateTimer = updateTimer;
        
        updateTimer = function() {
            originalUpdateTimer();
            
            // Add milestone notifications
            let currentProgress = Math.round((new Date() - processStartTime) / (estimatedTime * 60 * 1000) * 100);
            
            if (currentProgress >= 50 && lastProgressMilestone < 50) {
                showNotification('⏰ Order #{{ $order->order_code }} is 50% complete', 'info');
                lastProgressMilestone = 50;
            } else if (currentProgress >= 75 && lastProgressMilestone < 75) {
                showNotification('⚡ Order #{{ $order->order_code }} is 75% complete', 'info');
                lastProgressMilestone = 75;
            } else if (currentProgress >= 90 && lastProgressMilestone < 90) {
                showNotification('🔥 Order #{{ $order->order_code }} is almost ready!', 'info');
                lastProgressMilestone = 90;
            }
        }
        
        // Add page visibility change handling
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Page is hidden, reduce update frequency to save resources
                clearInterval(timerInterval);
                timerInterval = setInterval(updateTimer, 5000); // Update every 5 seconds when hidden
            } else {
                // Page is visible, resume full frequency updates
                clearInterval(timerInterval);
                timerInterval = setInterval(updateTimer, 1000); // Back to every second
                updateTimer(); // Immediate update when page becomes visible
            }
        });

        // Clean up timer when page unloads
        window.addEventListener('beforeunload', function() {
            if (timerInterval) {
                clearInterval(timerInterval);
            }
        });
        @endif

    </script>
    <script src="{{ asset('backend/js/print.js') }}"></script>
@endpush
