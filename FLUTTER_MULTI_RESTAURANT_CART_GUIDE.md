# Flutter Mobile App - Multi-Restaurant Cart Integration Guide

## Overview
This guide explains how to implement multi-restaurant cart functionality in the Flutter mobile app. The backend now supports adding items from multiple restaurants to a single cart and creating separate orders for each restaurant during checkout.

---

## Key Changes

### 1. Cart Persistence Across Restaurants
- **Previous Behavior**: Cart was cleared when user switched to a different restaurant
- **New Behavior**: Cart persists all items regardless of which restaurant page the user is viewing
- Items from different restaurants coexist in the same cart session

### 2. Per-Restaurant Tax & Service Fee Calculation
- Each restaurant has its own `tax_rate` and `service_fee_rate`
- Tax and service fees are calculated proportionally for each restaurant's items
- Final amounts are aggregated from all restaurants in the cart

### 3. Multi-Order Checkout
- Single checkout creates multiple orders (one per restaurant)
- Each order contains only items from that specific restaurant
- Payment is processed once for the total amount, then split across orders

---

## Cart Data Structure

### Session Cart Format
The cart is stored in the session with the following structure:

```json
{
  "items": [
    {
      "id": 1,
      "name": "Burger",
      "price": 10.99,
      "qty": 2,
      "restaurant_id": 5,
      "restaurant_name": "Restaurant A",
      "variationID": 0,
      "variation": null,
      "totalPrice": 21.98
    },
    {
      "id": 3,
      "name": "Pizza",
      "price": 15.99,
      "qty": 1,
      "restaurant_id": 8,
      "restaurant_name": "Restaurant B",
      "variationID": 0,
      "variation": null,
      "totalPrice": 15.99
    }
  ],
  "subTotalAmount": 37.97,
  "totalQty": 3,
  "tax_rate": 7.5,
  "tax_amount": 2.85,
  "service_fee_rate": 5.0,
  "service_fee_amount": 1.90,
  "delivery_charge": 3.00,
  "coupon_amount": 0,
  "totalAmount": 37.97,
  "totalPayAmount": 45.72,
  "delivery_type": false,
  "free_delivery": false,
  "couponID": 0
}
```

---

## Flutter Implementation

### 1. Cart Model Updates

Update your cart model to include `restaurant_id` and `restaurant_name`:

```dart
class CartItem {
  final int id;
  final String name;
  final double price;
  int qty;
  final int restaurantId;  // ADD THIS
  final String restaurantName;  // ADD THIS
  final int variationId;
  final String? variation;
  double totalPrice;

  CartItem({
    required this.id,
    required this.name,
    required this.price,
    required this.qty,
    required this.restaurantId,  // ADD THIS
    required this.restaurantName,  // ADD THIS
    this.variationId = 0,
    this.variation,
    this.totalPrice = 0,
  });

  factory CartItem.fromJson(Map<String, dynamic> json) {
    return CartItem(
      id: json['id'],
      name: json['name'],
      price: double.parse(json['price'].toString()),
      qty: json['qty'],
      restaurantId: json['restaurant_id'],  // ADD THIS
      restaurantName: json['restaurant_name'],  // ADD THIS
      variationId: json['variationID'] ?? 0,
      variation: json['variation'],
      totalPrice: double.parse(json['totalPrice'].toString()),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'price': price,
      'qty': qty,
      'restaurant_id': restaurantId,  // ADD THIS
      'restaurant_name': restaurantName,  // ADD THIS
      'variationID': variationId,
      'variation': variation,
      'totalPrice': totalPrice,
    };
  }
}
```

### 2. Cart Provider/State Management

```dart
class CartProvider extends ChangeNotifier {
  List<CartItem> _items = [];
  double _subTotal = 0;
  double _taxRate = 0;
  double _taxAmount = 0;
  double _serviceFeeRate = 0;
  double _serviceFeeAmount = 0;
  double _deliveryCharge = 0;
  double _couponAmount = 0;
  double _totalAmount = 0;
  double _totalPayAmount = 0;

  // Group items by restaurant for display
  Map<int, List<CartItem>> get itemsByRestaurant {
    Map<int, List<CartItem>> grouped = {};
    for (var item in _items) {
      if (!grouped.containsKey(item.restaurantId)) {
        grouped[item.restaurantId] = [];
      }
      grouped[item.restaurantId]!.add(item);
    }
    return grouped;
  }

  // Get restaurant names for grouped display
  Map<int, String> get restaurantNames {
    Map<int, String> names = {};
    for (var item in _items) {
      names[item.restaurantId] = item.restaurantName;
    }
    return names;
  }

  // DON'T clear cart when switching restaurants
  void addItem(CartItem item) {
    // Check if item already exists
    int existingIndex = _items.indexWhere(
      (i) => i.id == item.id && i.variationId == item.variationId
    );

    if (existingIndex != -1) {
      // Update quantity
      _items[existingIndex].qty += item.qty;
      _items[existingIndex].totalPrice = 
        _items[existingIndex].price * _items[existingIndex].qty;
    } else {
      // Add new item
      _items.add(item);
    }

    notifyListeners();
  }

  void removeItem(int index) {
    _items.removeAt(index);
    notifyListeners();
  }

  void updateQuantity(int index, int newQty) {
    if (newQty <= 0) {
      removeItem(index);
    } else {
      _items[index].qty = newQty;
      _items[index].totalPrice = _items[index].price * newQty;
      notifyListeners();
    }
  }

  void clearCart() {
    _items.clear();
    notifyListeners();
  }

  // Update cart from API response
  void updateFromApi(Map<String, dynamic> cartData) {
    if (cartData['items'] != null) {
      _items = (cartData['items'] as List)
          .map((item) => CartItem.fromJson(item))
          .toList();
    }
    
    _subTotal = double.parse(cartData['subTotalAmount']?.toString() ?? '0');
    _taxRate = double.parse(cartData['tax_rate']?.toString() ?? '0');
    _taxAmount = double.parse(cartData['tax_amount']?.toString() ?? '0');
    _serviceFeeRate = double.parse(cartData['service_fee_rate']?.toString() ?? '0');
    _serviceFeeAmount = double.parse(cartData['service_fee_amount']?.toString() ?? '0');
    _deliveryCharge = double.parse(cartData['delivery_charge']?.toString() ?? '0');
    _couponAmount = double.parse(cartData['coupon_amount']?.toString() ?? '0');
    _totalAmount = double.parse(cartData['totalAmount']?.toString() ?? '0');
    _totalPayAmount = double.parse(cartData['totalPayAmount']?.toString() ?? '0');
    
    notifyListeners();
  }
}
```

### 3. UI - Grouped Cart Display

Display cart items grouped by restaurant:

```dart
Widget buildCartList() {
  final cartProvider = Provider.of<CartProvider>(context);
  final groupedItems = cartProvider.itemsByRestaurant;
  final restaurantNames = cartProvider.restaurantNames;

  return ListView.builder(
    itemCount: groupedItems.length,
    itemBuilder: (context, index) {
      int restaurantId = groupedItems.keys.elementAt(index);
      List<CartItem> items = groupedItems[restaurantId]!;
      String restaurantName = restaurantNames[restaurantId] ?? 'Unknown';

      return Card(
        margin: EdgeInsets.all(8),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Restaurant Header
            Container(
              padding: EdgeInsets.all(12),
              color: Colors.grey[200],
              child: Row(
                children: [
                  Icon(Icons.restaurant, size: 20),
                  SizedBox(width: 8),
                  Text(
                    restaurantName,
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            ),
            // Restaurant Items
            ...items.map((item) => ListTile(
              title: Text(item.name),
              subtitle: Text('\$${item.price.toStringAsFixed(2)}'),
              trailing: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  IconButton(
                    icon: Icon(Icons.remove),
                    onPressed: () => cartProvider.updateQuantity(
                      cartProvider.items.indexOf(item),
                      item.qty - 1,
                    ),
                  ),
                  Text('${item.qty}'),
                  IconButton(
                    icon: Icon(Icons.add),
                    onPressed: () => cartProvider.updateQuantity(
                      cartProvider.items.indexOf(item),
                      item.qty + 1,
                    ),
                  ),
                ],
              ),
            )).toList(),
          ],
        ),
      );
    },
  );
}
```

### 4. Cart Summary Display

```dart
Widget buildCartSummary() {
  final cartProvider = Provider.of<CartProvider>(context);

  return Column(
    children: [
      _buildSummaryRow('Subtotal', cartProvider.subTotal),
      if (cartProvider.couponAmount > 0)
        _buildSummaryRow('Discount', -cartProvider.couponAmount),
      _buildSummaryRow(
        'Tax (${cartProvider.taxRate.toStringAsFixed(1)}%)',
        cartProvider.taxAmount,
      ),
      _buildSummaryRow(
        'Service Fee (${cartProvider.serviceFeeRate.toStringAsFixed(1)}%)',
        cartProvider.serviceFeeAmount,
      ),
      _buildSummaryRow('Delivery Charge', cartProvider.deliveryCharge),
      Divider(thickness: 2),
      _buildSummaryRow(
        'Total',
        cartProvider.totalPayAmount,
        isBold: true,
        fontSize: 18,
      ),
    ],
  );
}

Widget _buildSummaryRow(String label, double amount, {bool isBold = false, double fontSize = 14}) {
  return Padding(
    padding: EdgeInsets.symmetric(vertical: 4, horizontal: 16),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            fontSize: fontSize,
            fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
          ),
        ),
        Text(
          '\$${amount.toStringAsFixed(2)}',
          style: TextStyle(
            fontSize: fontSize,
            fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
          ),
        ),
      ],
    ),
  );
}
```

---

## API Integration

### 1. Add to Cart API

**Endpoint**: `POST /api/v1/cart/add`

**Request Body**:
```json
{
  "item_id": 1,
  "restaurant_id": 5,
  "restaurant_name": "Restaurant A",
  "name": "Burger",
  "price": 10.99,
  "qty": 2,
  "variation_id": 0,
  "variation": null
}
```

**Response**: Returns updated cart data with all items

**Flutter Implementation**:
```dart
Future<void> addToCart(CartItem item) async {
  try {
    final response = await http.post(
      Uri.parse('$baseUrl/api/v1/cart/add'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: json.encode({
        'item_id': item.id,
        'restaurant_id': item.restaurantId,
        'restaurant_name': item.restaurantName,
        'name': item.name,
        'price': item.price,
        'qty': item.qty,
        'variation_id': item.variationId,
        'variation': item.variation,
      }),
    );

    if (response.statusCode == 200) {
      final cartData = json.decode(response.body);
      cartProvider.updateFromApi(cartData);
    }
  } catch (e) {
    print('Error adding to cart: $e');
  }
}
```

### 2. Get Cart API

**Endpoint**: `GET /api/v1/cart`

**Response**: Returns current cart with all items and calculations

```dart
Future<void> fetchCart() async {
  try {
    final response = await http.get(
      Uri.parse('$baseUrl/api/v1/cart'),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      final cartData = json.decode(response.body);
      cartProvider.updateFromApi(cartData);
    }
  } catch (e) {
    print('Error fetching cart: $e');
  }
}
```

### 3. Checkout API

**Endpoint**: `POST /checkout`

**Important**: The checkout will create **multiple orders** (one per restaurant)

**Response Format**:
```json
{
  "success": true,
  "orders": [
    {
      "order_id": 101,
      "restaurant_id": 5,
      "restaurant_name": "Restaurant A",
      "amount": 25.50,
      "items_count": 2
    },
    {
      "order_id": 102,
      "restaurant_id": 8,
      "restaurant_name": "Restaurant B",
      "amount": 20.22,
      "items_count": 1
    }
  ],
  "total_amount": 45.72,
  "payment_id": "pay_123456"
}
```

**Flutter Implementation**:
```dart
Future<CheckoutResult> checkout({
  required String address,
  required String paymentMethod,
  String? specialInstructions,
}) async {
  try {
    final response = await http.post(
      Uri.parse('$baseUrl/checkout'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: json.encode({
        'address': address,
        'payment_method': paymentMethod,
        'special_instructions': specialInstructions,
      }),
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      
      // Handle multiple orders
      List<Order> orders = (data['orders'] as List)
          .map((o) => Order.fromJson(o))
          .toList();
      
      // Clear cart after successful checkout
      cartProvider.clearCart();
      
      return CheckoutResult(
        success: true,
        orders: orders,
        totalAmount: data['total_amount'],
        paymentId: data['payment_id'],
      );
    }
  } catch (e) {
    print('Error during checkout: $e');
    return CheckoutResult(success: false, error: e.toString());
  }
}
```

---

## Important Notes for Flutter Implementation

### 1. **DO NOT Clear Cart When Switching Restaurants**
```dart
// ❌ BAD - Don't do this
void onRestaurantChanged(int newRestaurantId) {
  cartProvider.clearCart(); // DON'T CLEAR!
}

// ✅ GOOD - Just switch restaurant view
void onRestaurantChanged(int newRestaurantId) {
  // Cart persists, just update UI
  setState(() {
    currentRestaurantId = newRestaurantId;
  });
}
```

### 2. **Always Include restaurant_id When Adding Items**
Every cart item MUST have a `restaurant_id` to enable multi-restaurant functionality.

### 3. **Show Multi-Order Confirmation**
After checkout, inform users that multiple orders were created:

```dart
void showCheckoutSuccess(CheckoutResult result) {
  showDialog(
    context: context,
    builder: (context) => AlertDialog(
      title: Text('Orders Placed Successfully!'),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text('${result.orders.length} orders created:'),
          SizedBox(height: 10),
          ...result.orders.map((order) => Padding(
            padding: EdgeInsets.symmetric(vertical: 4),
            child: Text(
              '${order.restaurantName}: Order #${order.orderId}',
              style: TextStyle(fontSize: 14),
            ),
          )).toList(),
          SizedBox(height: 10),
          Text(
            'Total: \$${result.totalAmount.toStringAsFixed(2)}',
            style: TextStyle(fontWeight: FontWeight.bold),
          ),
        ],
      ),
      actions: [
        TextButton(
          onPressed: () {
            Navigator.of(context).pop();
            // Navigate to orders screen
          },
          child: Text('View Orders'),
        ),
      ],
    ),
  );
}
```

### 4. **Tax & Service Fee Display**
The `tax_rate` and `service_fee_rate` in the cart response are **weighted averages** based on the proportion of items from each restaurant. The actual amounts (`tax_amount` and `service_fee_amount`) are the correctly calculated sums.

### 5. **Order Tracking**
Users will have multiple order IDs to track. Update your orders list screen to handle multiple simultaneous orders from the same checkout session.

---

## Testing Checklist

- [ ] Add items from Restaurant A to cart
- [ ] Navigate to Restaurant B (cart should NOT be cleared)
- [ ] Add items from Restaurant B to cart
- [ ] Verify cart shows items from both restaurants grouped separately
- [ ] Verify tax/service fee calculations are correct
- [ ] Proceed to checkout
- [ ] Verify multiple orders are created (one per restaurant)
- [ ] Verify total payment amount is correct
- [ ] Verify each order contains only items from its restaurant
- [ ] Verify order tracking shows all orders
- [ ] Test with 3+ restaurants in one cart

---

## Backend Configuration

### Restaurant Settings
Each restaurant has these fields in the database:
- `tax_rate` (decimal) - Restaurant's tax percentage
- `service_fee_rate` (decimal) - Restaurant's service fee percentage

These are configured per restaurant in the admin panel and automatically applied during cart calculation.

---

## Support

For questions or issues:
1. Check Laravel logs: `/var/www/foodbank/storage/logs/laravel.log`
2. Review this documentation
3. Contact backend team for API clarifications

---

**Last Updated**: December 24, 2025
**Version**: 1.0
