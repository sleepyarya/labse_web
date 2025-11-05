# 🔒 Admin Security Features

## Implementasi Keamanan Admin Dashboard

### 1. **Direct Access Prevention** ✅
Admin HARUS login ulang jika:
- Membuka tab baru dan ketik `localhost/labse_web/admin/`
- Copy-paste URL admin dashboard ke browser baru
- Akses direct ke admin area tanpa melalui login flow

### 2. **Session Timeout** ✅
- **Timeout Duration:** 10 menit (600 detik)
- **Behavior:** Jika tidak ada aktivitas selama 10 menit, session otomatis expired
- **Redirect:** Login page dengan pesan "Sesi Anda telah berakhir"

### 3. **Activity Tracking** ✅
- Setiap request ke admin page = update `last_activity` timestamp
- Auto-logout jika inactivity > timeout duration

### 4. **Fresh Login Flag** ✅
- Set saat berhasil login
- Clear setelah first load dashboard
- Prevent direct access bypass

### 5. **Session Token** ✅
- Unique token generated per login session
- Stored in session untuk security verification
- Regenerate session ID setelah login (prevent session fixation)

---

## Flow Diagram

### ✅ Normal Login Flow
```
User → login.php
  ↓ (enter credentials)
  ↓ (success)
Set: $_SESSION['fresh_login'] = true
Set: $_SESSION['admin_session_token'] = unique_token
  ↓
Redirect → index.php
  ↓ (auth_check.php)
Check: fresh_login == true? YES
  ↓
✅ ALLOW ACCESS
Clear: fresh_login flag
```

### ❌ Direct Access / Tab Baru
```
User → Type "localhost/labse_web/admin/" in new tab
  ↓
index.php → auth_check.php
  ↓
Check: fresh_login == true? NO
Check: referer from /admin/? NO (empty or external)
  ↓
❌ BLOCK ACCESS
Clear: entire session
Redirect → login.php?direct=1
Show message: "Untuk keamanan, Anda harus login..."
```

### ✅ Navigate Within Dashboard
```
User → Di dashboard → Klik menu (manage_artikel.php, dll)
  ↓
manage_artikel.php → auth_check.php
  ↓
Check: fresh_login == true? NO
Check: referer from /admin/? YES
  ↓
✅ ALLOW ACCESS
Update: last_activity timestamp
```

### ⏰ Session Timeout
```
User → Login → Do nothing for 10+ minutes
  ↓
Next request → auth_check.php
  ↓
Calculate: time() - last_activity
Result: > 600 seconds
  ↓
❌ TIMEOUT
Clear: entire session
Redirect → login.php?timeout=1
Show message: "Sesi Anda telah berakhir..."
```

---

## Test Scenarios

### Test 1: Login Normal ✅
```
1. Buka: localhost/labse_web/admin/
2. Login dengan username & password
3. Redirect ke dashboard → SUCCESS
4. Browse admin pages → WORKS
```

### Test 2: Direct Access - Tab Baru ❌
```
1. Login ke admin (Tab 1)
2. Buka Tab 2 (new tab)
3. Ketik: localhost/labse_web/admin/
4. Redirect ke login page ✅
5. Message: "Untuk keamanan, Anda harus login..."
```

### Test 3: Copy-Paste URL ❌
```
1. Login ke admin
2. Copy URL: localhost/labse_web/admin/manage_artikel.php
3. Buka browser baru (atau incognito)
4. Paste URL
5. Redirect ke login page ✅
```

### Test 4: Navigate Within Dashboard ✅
```
1. Login ke admin
2. Klik "Kelola Artikel"
3. Klik "Kelola Mahasiswa"
4. Klik "Dashboard"
5. Semua halaman accessible ✅
```

### Test 5: Session Timeout ⏰
```
1. Login ke admin
2. Tunggu 10 menit tanpa aktivitas
3. Klik menu apapun
4. Redirect ke login page ✅
5. Message: "Sesi Anda telah berakhir..."
```

### Test 6: Refresh Page ✅
```
1. Login ke admin
2. Di dashboard, tekan F5 (refresh)
3. Redirect ke login page ✅ (karena referer dari same page)
```

---

## Configuration

### Adjust Timeout Duration

Edit `admin/auth_check.php`:
```php
// Default: 10 menit (600 detik)
define('SESSION_TIMEOUT', 600);

// Ubah sesuai kebutuhan:
// 5 menit = 300
// 15 menit = 900
// 30 menit = 1800
```

### Disable Direct Access Check (NOT RECOMMENDED)

Edit `admin/auth_check.php`, comment out lines 32-59:
```php
/*
// Cek apakah ini direct access...
if (in_array($current_page, ['index.php', ''])) {
    ...
}
*/
```

---

## Security Benefits

### 🔒 Prevent Unauthorized Access
- User tidak bisa akses admin dashboard dengan hanya punya session ID
- Force login setiap kali direct access

### 🛡️ Session Hijacking Protection
- Session token regenerated setiap login
- Session ID regenerated (prevent fixation)
- Timeout auto-clear session

### 🚪 Multi-Tab Prevention
- Satu login = satu active flow
- Tab baru = harus login ulang
- Prevent session sharing

### ⏰ Auto-Logout Inactive Sessions
- Admin lupa logout? Auto-logout setelah 10 menit
- Reduce risk jika komputer ditinggal

---

## User Messages

### Login Page Messages:

1. **Success Logout** (green)
   ```
   ✓ Anda telah berhasil logout.
   ```

2. **Session Timeout** (yellow/warning)
   ```
   ⏰ Sesi Anda telah berakhir karena tidak ada aktivitas. 
      Silakan login kembali.
   ```

3. **Direct Access** (blue/info)
   ```
   ℹ️ Untuk keamanan, Anda harus login untuk mengakses 
      dashboard admin.
   ```

4. **Login Failed** (red)
   ```
   ⚠️ Username atau password salah!
   ```

---

## Technical Implementation

### Files Modified:

1. **admin/login.php**
   - Generate session token
   - Set fresh_login flag
   - Regenerate session ID

2. **admin/auth_check.php**
   - Check session validity
   - Detect direct access
   - Enforce timeout
   - Track activity

3. **admin/logout.php**
   - Clear all session data
   - Destroy session
   - Redirect with success message

---

## Session Variables

### Set on Login:
```php
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_username'] = $admin['username'];
$_SESSION['admin_nama'] = $admin['nama_lengkap'];
$_SESSION['admin_email'] = $admin['email'];
$_SESSION['admin_session_token'] = bin2hex(random_bytes(32));
$_SESSION['admin_last_activity'] = time();
$_SESSION['fresh_login'] = true;
```

### Updated on Each Request:
```php
$_SESSION['admin_last_activity'] = time();
$_SESSION['fresh_login'] = false; // after first dashboard load
```

### Cleared on Logout/Timeout:
```php
session_unset();
session_destroy();
```

---

## Best Practices

### ✅ DO:
- Always logout when done
- Don't share admin credentials
- Use strong passwords
- Keep browser updated

### ❌ DON'T:
- Leave admin dashboard open and unattended
- Use admin on public computers without logout
- Share session cookies
- Disable timeout for convenience

---

## Troubleshooting

### Problem: "Harus login terus-menerus"
**Solution:** Timeout terlalu pendek, increase SESSION_TIMEOUT

### Problem: "Bisa akses dari tab baru"
**Solution:** Direct access check tidak aktif, verify auth_check.php

### Problem: "Session hilang saat refresh"
**Solution:** Normal behavior untuk index.php, use menu navigation

---

## Summary

Sistem keamanan admin sekarang memiliki:
- ✅ Direct access prevention
- ✅ Activity-based timeout (10 menit)
- ✅ Session token validation
- ✅ Fresh login verification
- ✅ Multi-tab restriction
- ✅ Auto-logout inactive sessions

**User Experience:**
- Admin harus login untuk akses dashboard
- Tab baru = login ulang (security)
- Navigate dalam dashboard = smooth
- Inactivity 10 menit = auto-logout

**Security Level: 🔒🔒🔒 HIGH**
