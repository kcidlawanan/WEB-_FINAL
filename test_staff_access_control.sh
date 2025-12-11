#!/bin/bash

# Staff Access Control Security Test Script
# This script tests all the access restrictions for staff members

echo "=================================="
echo "Staff Access Control Test Suite"
echo "=================================="
echo ""

# Configuration
BASE_URL="http://127.0.0.1:8000"
STAFF_EMAIL="staff@example.com"  # Update with actual staff email
STAFF_PASSWORD="password"          # Update with actual password

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counter
TESTS_PASSED=0
TESTS_FAILED=0

# Function to test unauthorized access
test_unauthorized_access() {
    local route=$1
    local description=$2
    
    echo -n "Testing: $description... "
    
    # Try to access the route (should return 403)
    response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL$route")
    
    if [ "$response" = "403" ] || [ "$response" = "302" ]; then
        echo -e "${GREEN}✓ PASS${NC} (Status: $response)"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}✗ FAIL${NC} (Status: $response)"
        ((TESTS_FAILED++))
    fi
}

# Function to test authorized access
test_authorized_access() {
    local route=$1
    local description=$2
    
    echo -n "Testing: $description... "
    
    # Try to access the route (should return 200 or 302 for redirects)
    response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL$route")
    
    if [ "$response" = "200" ] || [ "$response" = "302" ]; then
        echo -e "${GREEN}✓ PASS${NC} (Status: $response)"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}✗ FAIL${NC} (Status: $response)"
        ((TESTS_FAILED++))
    fi
}

echo -e "${YELLOW}Testing UNAUTHORIZED Staff Access:${NC}"
echo "======================================"

test_unauthorized_access "/admin/users" "Cannot access user management list"
test_unauthorized_access "/admin/users/new" "Cannot create new user"
test_unauthorized_access "/admin/logs" "Cannot access activity logs"
test_unauthorized_access "/admin/dashboard" "Cannot access admin dashboard"

echo ""
echo -e "${YELLOW}Testing AUTHORIZED Staff Access:${NC}"
echo "======================================"

test_authorized_access "/staff" "Can access staff dashboard"
test_authorized_access "/property" "Can access property management"
test_authorized_access "/profile" "Can access own profile"
test_authorized_access "/contact" "Can access contact form"

echo ""
echo "======================================"
echo -e "Test Results:"
echo -e "  ${GREEN}Passed: $TESTS_PASSED${NC}"
echo -e "  ${RED}Failed: $TESTS_FAILED${NC}"

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}All security tests passed! ✓${NC}"
    exit 0
else
    echo -e "${RED}Some security tests failed! ✗${NC}"
    exit 1
fi
