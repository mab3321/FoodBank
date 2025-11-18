@if(auth()->user()->myrole == 3 || auth()->user()->myrole == 1)
<div class="order-status-management">
    <div class="current-status">
        <h4>Current Status: <span class="badge {{ $statusColor }}">{{ $currentStatus }}</span></h4>
    </div>
    
    @if(!empty($availableStatuses))
    <div class="status-actions mt-3">
        <h5>Update Order Status:</h5>
        <div class="row">
            @foreach($availableStatuses as $statusCode => $statusName)
                <div class="col-md-4 col-sm-6 mb-2">
                    <a href="{{ route('admin.order.change-status', [$order->id, $statusCode]) }}" 
                       class="btn btn-outline-{{ getStatusButtonClass($statusCode) }} btn-block status-change-btn"
                       data-status="{{ $statusCode }}"
                       data-order="{{ $order->id }}">
                        <i class="fas {{ getStatusIcon($statusCode) }}"></i>
                        {{ $statusName }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@php
function getStatusButtonClass($status) {
    switch($status) {
        case 5: return 'secondary'; // Pending
        case 10: return 'danger'; // Cancel
        case 12: return 'danger'; // Reject  
        case 14: return 'success'; // Accept
        case 15: return 'primary'; // Process
        case 17: return 'warning'; // On the Way
        case 20: return 'success'; // Completed
        default: return 'secondary';
    }
}

function getStatusIcon($status) {
    switch($status) {
        case 5: return 'fa-clock'; // Pending
        case 10: return 'fa-times'; // Cancel
        case 12: return 'fa-ban'; // Reject  
        case 14: return 'fa-check'; // Accept
        case 15: return 'fa-cog'; // Process
        case 17: return 'fa-truck'; // On the Way
        case 20: return 'fa-check-circle'; // Completed
        default: return 'fa-question';
    }
}
@endphp

@else
<div class="current-status">
    <h4>Order Status: <span class="badge {{ $statusColor }}">{{ $currentStatus }}</span></h4>
    <p class="text-muted">You don't have permission to change order status.</p>
</div>
@endif

<style>
.status-change-btn {
    transition: all 0.3s ease;
    margin-bottom: 10px;
}

.status-change-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.order-status-management {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
}

.current-status h4 {
    margin-bottom: 15px;
}

.status-actions h5 {
    color: #495057;
    margin-bottom: 15px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation dialog for status changes
    document.querySelectorAll('.status-change-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const statusName = this.textContent.trim();
            const orderId = this.dataset.order;
            
            if (confirm(`Are you sure you want to change the order status to "${statusName}"?`)) {
                window.location.href = this.href;
            }
        });
    });
});
</script>