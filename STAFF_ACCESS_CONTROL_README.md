# Staff Role Access Control System

## Quick Summary

Staff members are now **restricted from accessing sensitive administrative functions**. The system enforces these restrictions at multiple security levels:

### What Staff CANNOT Do:
- ❌ Create, edit, or delete user accounts
- ❌ View activity logs
- ❌ Access the admin dashboard
- ❌ Manage other users
- ❌ Change user roles or permissions
- ❌ Access admin-only functions

### What Staff CAN Do:
- ✅ Manage properties (create, edit, view)
- ✅ Record property purchases and rentals
- ✅ Access their own profile
- ✅ Use the staff dashboard
- ✅ Submit contact forms

## Security Implementation Overview

### 1. Route-Level Protection (Firewall)
The application's security firewall in `config/packages/security.yaml` blocks unauthorized access to protected routes:

```yaml
access_control:
  - { path: ^/admin/users, roles: ROLE_ADMIN }      # User management
  - { path: ^/admin/logs, roles: ROLE_ADMIN }       # Activity logs
  - { path: ^/admin/dashboard, roles: ROLE_ADMIN }  # Dashboard
  - { path: ^/admin, roles: ROLE_ADMIN }            # Admin area
  - { path: ^/staff, roles: ROLE_STAFF }            # Staff area
```

### 2. Controller-Level Verification
Each sensitive controller method includes explicit role checks:

```php
if (!$this->isGranted('ROLE_ADMIN')) {
    throw new AccessDeniedException('Access denied. Only administrators can...');
}
```

This ensures that even if someone bypasses the firewall, the controller will deny access.

### 3. Form-Level Validation
When editing users, the system validates that roles weren't modified:

```php
$originalRoles = $user->getRoles();
// ... form handling ...
if ($newRoles !== $originalRoles) {
    throw new AccessDeniedException('Unauthorized role change attempt');
}
```

### 4. Exception Handling
The `ExceptionListener` catches `AccessDeniedException` and:
- Returns HTTP 403 Forbidden status
- Displays a user-friendly error page
- Logs the unauthorized attempt
- Provides navigation options

### 5. Audit Logging
All access attempts are logged in the `activity_log` table:
- Successful admin actions
- Failed/unauthorized attempts (marked as "UNAUTHORIZED_ATTEMPT")
- User information (ID, email, role)
- IP address for security audit trail
- Timestamp of the attempt

## Testing the Restrictions

### Manual Testing

1. **Login as Staff Member:**
   - Use staff credentials to log in
   - Navigate to staff dashboard

2. **Test User Management Access:**
   ```
   Try to navigate to: http://127.0.0.1:8000/admin/users
   Expected: 403 Forbidden error page
   ```

3. **Test Activity Logs Access:**
   ```
   Try to navigate to: http://127.0.0.1:8000/admin/logs
   Expected: 403 Forbidden error page
   ```

4. **Test Admin Dashboard Access:**
   ```
   Try to navigate to: http://127.0.0.1:8000/admin/dashboard
   Expected: 403 Forbidden error page
   ```

5. **Verify Allowed Access:**
   - Staff dashboard: `http://127.0.0.1:8000/staff` ✅
   - Property management: `http://127.0.0.1:8000/property` ✅
   - Profile: `http://127.0.0.1:8000/profile` ✅

### Automated Testing

Run the security test script:

```bash
bash test_staff_access_control.sh
```

This script tests:
- All unauthorized access attempts (should return 403 or 302)
- All authorized access points (should return 200 or 302)
- Reports overall security status

## Response Types

### 403 Forbidden (Primary Response)
When staff tries to access restricted routes:

**HTTP Status:** 403 Forbidden

**Response Body:** Custom error page with:
- Clear message: "Access Denied"
- Explanation of restriction
- Navigation buttons (Back to Home, Go to Profile, Login)
- Professional styling

### 302 Redirect (Alternative Response)
If the session expires or user is logged out:

**HTTP Status:** 302 Found

**Redirect To:** `/login` (login page)

This ensures staff members are automatically logged out and can't access protected resources.

## Error Messages

### User Management Access
```
Access denied. Only administrators can manage users.
```

### Activity Logs Access
```
Access denied. Only administrators can view activity logs.
```

### Admin Dashboard Access
```
Access denied. Only administrators can view the dashboard.
```

### Unauthorized Role Changes
```
Unauthorized attempt to change user roles.
```

All messages are:
- Clear and actionable
- Non-technical for end users
- Logged with full context for administrators

## Security Architecture

```
┌─────────────────────────────────────────────────────┐
│                  HTTP Request                        │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│  1. Firewall (security.yaml)                        │
│     - Route-level access control                    │
│     - Blocks unauthorized paths                     │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│  2. Controller Method                               │
│     - Explicit role verification                    │
│     - Throws AccessDeniedException if denied        │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│  3. Form/Data Layer                                 │
│     - Validates role integrity                      │
│     - Prevents unauthorized modifications           │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│  4. Exception Listener                              │
│     - Catches exceptions                            │
│     - Returns 403 Forbidden                         │
│     - Renders error template                        │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│  5. Activity Logger                                 │
│     - Records attempt in database                   │
│     - Logs as "UNAUTHORIZED_ATTEMPT"                │
│     - Captures IP and timestamp                     │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│              403 Response to Client                  │
└─────────────────────────────────────────────────────┘
```

## Files Involved

### Configuration
- `config/packages/security.yaml` - Firewall rules

### Controllers
- `src/Controller/AdminUserController.php` - User management (ADMIN-ONLY)
- `src/Controller/AdminActivityController.php` - Activity logs (ADMIN-ONLY)
- `src/Controller/AdminController.php` - Admin dashboard (ADMIN-ONLY)
- `src/Controller/ContactController.php` - Admin contact routes (ADMIN-ONLY)
- `src/Controller/StaffController.php` - Staff dashboard (STAFF-ONLY)

### Event Listeners
- `src/EventListener/ExceptionListener.php` - Exception handling

### Templates
- `templates/error/403.html.twig` - Error page

### Documentation
- `SECURITY_STAFF_RESTRICTIONS.md` - Detailed restrictions
- `STAFF_ACCESS_CONTROL_IMPLEMENTATION.md` - Implementation details

## Audit Trail

All unauthorized access attempts are logged in the `activity_log` table:

```sql
SELECT * FROM activity_log 
WHERE action = 'UNAUTHORIZED_ATTEMPT' 
ORDER BY created_at DESC;
```

Each log entry includes:
- `user_id` - Staff member's ID
- `username` - Staff member's email
- `role` - User's role(s)
- `action` - "UNAUTHORIZED_ATTEMPT"
- `target_data` - Details of what was attempted
- `ip_address` - IP address of the request
- `created_at` - Timestamp of the attempt

This creates a complete audit trail for security investigations.

## Troubleshooting

### Issue: Staff can still access `/admin/users`
**Solution:** 
1. Clear cache: `php bin/console cache:clear`
2. Check that staff user has `ROLE_STAFF` not `ROLE_ADMIN`
3. Verify `config/packages/security.yaml` has correct routes

### Issue: 404 error instead of 403
**Solution:**
1. Verify `templates/error/403.html.twig` exists
2. Check `src/EventListener/ExceptionListener.php` is properly configured
3. Ensure listener is registered in services

### Issue: Activity logs not recording
**Solution:**
1. Verify `activity_log` table exists in database
2. Check `ActivityLog` entity is properly mapped
3. Run migrations if table was just created

## Best Practices for Administrators

1. **Monitor Activity Logs Regularly**
   - Check `/admin/logs` for unauthorized access attempts
   - Investigate unusual patterns
   - Look for "UNAUTHORIZED_ATTEMPT" actions

2. **Audit User Roles**
   - Verify staff members don't have ROLE_ADMIN
   - Review user permissions quarterly
   - Remove unnecessary roles immediately

3. **Update Documentation**
   - Keep team informed of restrictions
   - Provide this guide to staff
   - Clarify what staff can and cannot do

4. **Test Regularly**
   - Run `test_staff_access_control.sh` periodically
   - Verify restrictions are still in place
   - Test after major updates

## Compliance

This implementation ensures:

✅ **Principle of Least Privilege** - Staff only have access they need
✅ **Separation of Duties** - Admin functions isolated from staff
✅ **Audit Trail** - All access attempts logged
✅ **Clear Policies** - Well-documented restrictions
✅ **User Feedback** - Clear error messages
✅ **Defense in Depth** - Multiple security layers

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review `SECURITY_STAFF_RESTRICTIONS.md` for detailed specifications
3. Check activity logs for unauthorized access patterns
4. Consult with system administrator

---

**Last Updated:** December 11, 2025
**Version:** 1.0
**Status:** Active
