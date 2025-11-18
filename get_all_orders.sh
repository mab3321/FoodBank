#!/bin/bash

# Script to fetch all orders from admin panel
echo "🔐 Getting admin session..."

# Step 1: Get CSRF token and session cookie
curl -c cookies.txt -s -L "https://foodbank.dev.platco.ai/admin/login" -o /dev/null

# Step 2: Extract XSRF token
XSRF_TOKEN=$(awk '$6 == "XSRF-TOKEN" {print $7}' cookies.txt | head -n1 | sed 's/%3D/=/g' | sed 's/%2B/+/g' | sed 's/%2F/\//g')
echo "📋 XSRF Token: $XSRF_TOKEN"

# Step 3: Login as admin
echo "🔑 Logging in as admin..."
curl -b cookies.txt -c cookies.txt -s -L \
  -H "X-Requested-With: XMLHttpRequest" \
  -H "X-XSRF-TOKEN: $XSRF_TOKEN" \
  -H "Accept: application/json" \
  -d "email=admin@example.com&password=123456" \
  "https://foodbank.dev.platco.ai/admin/login" -o login_response.json

# Check login success
if grep -q "error" login_response.json 2>/dev/null; then
    echo "❌ Login failed:"
    cat login_response.json
    exit 1
fi

echo "✅ Login successful!"

# Step 4: Get all orders
echo "📦 Fetching all orders..."
curl -b cookies.txt -s -G \
  -H "Accept: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  "https://foodbank.dev.platco.ai/admin/get-orders" \
  --data-urlencode "start=0" \
  --data-urlencode "length=50" \
  --data-urlencode "search[value]=" \
  --data-urlencode "order[0][column]=0" \
  --data-urlencode "order[0][dir]=desc" | jq .

echo "🧹 Cleaning up..."
rm -f cookies.txt login_response.json