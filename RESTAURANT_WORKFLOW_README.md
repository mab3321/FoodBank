# 🍽️ Restaurant Order Management Workflow

## 📋 Overview
This documentation covers the complete restaurant order management workflow implemented in the Foodbank API, allowing restaurant owners to manage orders through their entire lifecycle.

## 🔐 Authentication
Restaurant owners must login with role `3` to access restaurant management endpoints:

```bash
curl -X POST "https://foodbank.dev.platco.ai/api/v1/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"saadzafar@gmail.com","password":"123456789","role":3}'
```

## 📊 Order Status Codes

| Status Code | Name | Description | Action |
|-------------|------|-------------|---------|
| `5` | Pending | New order received | Initial state |
| `10` | Cancel | Order cancelled | Terminal state |
| `12` | Reject | Order rejected | Terminal state |
| `14` | Accept | Order accepted | Restaurant approved |
| `15` | Process | Order processing | Kitchen preparing |
| `17` | On the Way | Out for delivery | Delivery in progress |
| `20` | Completed | Order delivered | Terminal state |

## 🔄 Order Lifecycle Flow

```
Pending (5) → Accept (14) → Process (15) → On the Way (17) → Completed (20)
            ↘ Reject (12)
            ↘ Cancel (10)
```

## 🏪 Restaurant Order Management Endpoints

### 1. Get Restaurant Orders
**Endpoint:** `GET /api/v1/restaurant-order`
**Purpose:** List all orders for the authenticated restaurant owner's restaurant

```bash
curl -X GET "https://foodbank.dev.platco.ai/api/v1/restaurant-order" \
  -H "Authorization: Bearer {restaurant_owner_token}"
```

### 2. Get Order Details
**Endpoint:** `GET /api/v1/restaurant-order/{order_id}`
**Purpose:** Get detailed information about a specific order

```bash
curl -X GET "https://foodbank.dev.platco.ai/api/v1/restaurant-order/4" \
  -H "Authorization: Bearer {restaurant_owner_token}"
```

### 3. Update Order Status
**Endpoint:** `PUT /api/v1/orders/{order_id}`
**Purpose:** Update order to any valid status (restaurant owners have full permissions)

#### Accept Order
```bash
curl -X PUT "https://foodbank.dev.platco.ai/api/v1/orders/4" \
  -H "Authorization: Bearer {restaurant_owner_token}" \
  -H "Content-Type: application/json" \
  -d '{"status": 14}'
```

#### Start Processing
```bash
curl -X PUT "https://foodbank.dev.platco.ai/api/v1/orders/4" \
  -H "Authorization: Bearer {restaurant_owner_token}" \
  -H "Content-Type: application/json" \
  -d '{"status": 15}'
```

#### Mark as On the Way
```bash
curl -X PUT "https://foodbank.dev.platco.ai/api/v1/orders/4" \
  -H "Authorization: Bearer {restaurant_owner_token}" \
  -H "Content-Type: application/json" \
  -d '{"status": 17}'
```

#### Complete Order
```bash
curl -X PUT "https://foodbank.dev.platco.ai/api/v1/orders/4" \
  -H "Authorization: Bearer {restaurant_owner_token}" \
  -H "Content-Type: application/json" \
  -d '{"status": 20}'
```

#### Reject Order
```bash
curl -X PUT "https://foodbank.dev.platco.ai/api/v1/orders/4" \
  -H "Authorization: Bearer {restaurant_owner_token}" \
  -H "Content-Type: application/json" \
  -d '{"status": 12}'
```

## 🔒 Permission System

### Restaurant Owner Permissions:
- ✅ View all orders for their restaurant
- ✅ Update orders to ANY status (5, 10, 12, 14, 15, 17, 20)
- ✅ Access order details and customer information
- ❌ Cannot access orders from other restaurants

### Customer Permissions:
- ✅ View their own orders
- ✅ Cancel their orders (status: 10)
- ❌ Cannot change to other statuses
- ❌ Cannot access other customers' orders

## 📱 Example Workflow

### Typical Order Processing Flow:

1. **Customer places order** → Status: `5` (Pending)
2. **Restaurant receives notification** → Get order details
3. **Restaurant accepts order** → Update status to `14` (Accept)
4. **Kitchen starts preparing** → Update status to `15` (Process)
5. **Order ready for delivery** → Update status to `17` (On the Way)
6. **Order delivered** → Update status to `20` (Completed)

### Alternative Scenarios:

- **Restaurant busy** → Update status to `12` (Reject)
- **Customer cancels** → Update status to `10` (Cancel)

## 🧪 Testing with Postman

The updated Postman collection includes:

1. **🍽️ Restaurant Order Management** section with all endpoints
2. **Pre-configured requests** for each status update
3. **Working authentication** with real restaurant owner token
4. **Complete examples** for the entire order lifecycle

### Collection Variables:
- `base_url`: https://foodbank.dev.platco.ai/api/v1
- `restaurant_owner_token`: Live token for saadzafar@gmail.com (KFC restaurant)

## 🏆 Key Features

### ✅ Enhanced Capabilities:
- **Full Status Control:** Restaurant owners can update orders to any status
- **Security:** Role-based permissions prevent unauthorized access
- **Real-time Updates:** Changes are immediate, no server restart required
- **Comprehensive API:** Both restaurant-specific and general order endpoints
- **Complete Documentation:** Postman collection with all workflows

### 🔧 Technical Implementation:
- Modified `OrderController.php` with role-based status validation
- Added `UserRole` enum support for clean permission checking
- Implemented restaurant ownership validation
- Enhanced error messages for better debugging

## 🚀 Ready to Use!

The restaurant workflow is now fully implemented and tested. Restaurant owners can:
- Login and manage their restaurant's orders
- Update order status through the complete lifecycle
- Track order progress in real-time
- Provide excellent customer service with proper order management

All endpoints are documented in the Postman collection and ready for immediate use! 🎯