#!/bin/bash
echo "Testing all 14 routes..."
echo ""

routes=(
  "/login|Đăng nhập"
  "/|Dashboard"
  "/dashboard|Dashboard"
  "/products|Sản phẩm"
  "/inventory|Kho"
  "/batches|Lô hàng"
  "/batches/1|Chi tiết lô"
  "/customers|Khách hàng"
  "/customers/1|Chi tiết KH"
  "/contracts|Hợp đồng"
  "/devices|Thiết bị"
  "/devices/1|Chi tiết TB"
  "/employees|Nhân viên"
  "/activities|Hoạt động"
  "/profile|Hồ sơ"
)

success=0
fail=0

for route_pair in "${routes[@]}"; do
  IFS='|' read -r route title <<< "$route_pair"
  response=$(curl -s -w "\n%{http_code}" http://localhost:8899$route)
  http_code=$(echo "$response" | tail -1)
  html=$(echo "$response" | head -n -1)
  
  if [ "$http_code" = "200" ]; then
    page_title=$(echo "$html" | grep -o "<title>[^<]*</title>" | sed 's/<[^>]*>//g')
    echo "✓ $route (HTTP $http_code) - $page_title"
    ((success++))
  else
    echo "✗ $route (HTTP $http_code)"
    ((fail++))
  fi
done

echo ""
echo "Summary: $success/15 routes working"
