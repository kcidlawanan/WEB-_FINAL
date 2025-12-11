# Staff Role Access Restrictions

This document outlines the security measures implemented to restrict staff member access to sensitive administrative functions.

## Staff Access Restrictions

### ✅ Staff CAN Access:
- `/staff` - Staff dashboard
- `/property` - Property management (listing, creating, editing properties)
- `/property/*/purchase` - Purchase properties
- `/property/*/rent` - Rent properties
- `/profile` - User profile
- `/contact` - Contact form
- `/service` - Service pages

### ❌ Staff CANNOT Access:

#### 1. User Management (`/admin/users/*`)
- ❌ `/admin/users` - Cannot view user list
- ❌ `/admin/users/new` - Cannot create users
- ❌ `/admin/users/edit/{id}` - Cannot edit users
- ❌ `/admin/users/delete/{id}` - Cannot delete users
- ❌ `/admin/users/disable/{id}` - Cannot disable users
- ❌ `/admin/users/create` - Cannot create user accounts
- ❌ `/admin/users` (list) - Cannot list users

**Response:** 403 Access Denied or redirect to login

#### 2. Activity Logs (`/admin/logs/*`)
- ❌ `/admin/logs` - Cannot view activity logs
- ❌ `/admin/logs/` - Cannot access activity log index

**Response:** 403 Access Denied or redirect to login

#### 3. Admin Dashboard (`/admin/dashboard`)
- ❌ Cannot view admin dashboard with business metrics

**Response:** 403 Access Denied or redirect to login

#### 4. System Administration (`/admin/*`)
- ❌ Most admin routes require ROLE_ADMIN
- ❌ Cannot manage system configuration
- ❌ Cannot access admin-only sections

**Response:** 403 Access Denied or redirect to login

## Security Implementation

### 1. Route-Level Access Control (security.yaml)
```yaml
access_control:
  - { path: ^/admin/users, roles: ROLE_ADMIN }
  - { path: ^/admin/logs, roles: ROLE_ADMIN }
  - { path: ^/admin/dashboard, roles: ROLE_ADMIN }
  - { path: ^/admin, roles: ROLE_ADMIN }
  - { path: ^/staff, roles: ROLE_STAFF }
```

### 2. Controller-Level Access Checks
All sensitive endpoints include explicit role verification:
```php
if (!$this->isGranted('ROLE_ADMIN')) {
    throw new AccessDeniedException('Access denied. Only administrators can...');
}
```

### 3. Role Change Prevention
When editing users, the system:
- Stores original roles before form submission
- Compares new roles with original roles
- Prevents unauthorized role modifications
- Logs any unauthorized role change attempts

### 4. Exception Handling
- AccessDeniedException triggers 403 Forbidden response
- Custom ExceptionListener handles the exception
- 403 error template displays user-friendly message
- Redirects to home or login based on user state

### 5. Activity Logging
All access attempts are logged:
- Successful actions recorded with full details
- Failed/unauthorized attempts logged with "UNAUTHORIZED_ATTEMPT" action
- IP addresses captured for audit trail
- Timestamp recorded for all activities

## Testing Access Restrictions

### Test 1: Try to access user management as staff
```
Navigate to: http://127.0.0.1:8000/admin/users
Expected: 403 Access Denied
```

### Test 2: Try to access activity logs as staff
```
Navigate to: http://127.0.0.1:8000/admin/logs
Expected: 403 Access Denied
```

### Test 3: Try to access admin dashboard as staff
```
Navigate to: http://127.0.0.1:8000/admin/dashboard
Expected: 403 Access Denied
```

### Test 4: Try to create user as staff
```
Navigate to: http://127.0.0.1:8000/admin/users/new
Expected: 403 Access Denied
```

### Test 5: Try to delete user as staff
```
POST to: http://127.0.0.1:8000/admin/users/delete/1
Expected: 403 Access Denied
```

## Files Modified

### Controllers
- `src/Controller/AdminUserController.php` - Added explicit role checks in all methods
- `src/Controller/AdminActivityController.php` - Added role verification
- `src/Controller/AdminController.php` - Added dashboard access control
- `src/Controller/ContactController.php` - Added admin route access control

### Configuration
- `config/packages/security.yaml` - Added granular access_control rules

### Event Listeners
- `src/EventListener/ExceptionListener.php` - NEW - Handles AccessDeniedException

### Templates
- `templates/error/403.html.twig` - NEW - 403 Forbidden error page

## Best Practices Implemented

✅ **Defense in Depth**
- Multiple layers of security (routes, controllers, role checks)
- Each layer independently enforces restrictions

✅ **Explicit Deny**
- All admin routes explicitly deny staff access
- No implicit permissions given

✅ **Audit Trail**
- All access attempts logged
- Unauthorized attempts recorded

✅ **User Feedback**
- Clear 403 error messages
- Helpful navigation options

✅ **Role Integrity**
- Role changes are validated
- Modification attempts are logged

## Future Enhancements

1. **Rate Limiting** - Add rate limiting for failed access attempts
2. **Two-Factor Authentication** - Require 2FA for admin accounts
3. **IP Whitelist** - Restrict admin access to specific IPs
4. **Session Monitoring** - Monitor concurrent sessions
5. **Anomaly Detection** - Alert on unusual access patterns
