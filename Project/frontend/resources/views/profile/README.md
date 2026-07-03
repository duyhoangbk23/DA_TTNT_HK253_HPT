# resources/views/profile/ — User profile page

Hồ sơ cá nhân người dùng.

## 📌 index.blade.php

**Route:** `/profile`
**Controller:** ProfileController@index

### Layout (3 columns on desktop)

#### Column 1 (Left): User Card & Activity

1. **User Card:**
   - Avatar image (large)
   - Full name
   - Position/title
   - Role badge

2. **Change Avatar Button:**
   - Upload new avatar (demo, not functional)

3. **Recent Activity Timeline:**
   - Last 5 activities
   - Time, action description
   - Scroll-friendly

#### Column 2-3 (Right): Forms

1. **Personal Information Form:**
   - Name (text)
   - Position (disabled)
   - Email (email)
   - Phone (tel)
   - Address (text)
   - **"Lưu thay đổi" button** (demo, not functional)

2. **Change Password Form:**
   - Current password
   - New password
   - Confirm password
   - **"Cập nhật mật khẩu" button** (demo, not functional)

---

## 📊 Data

```php
$user = MockData::currentUser();
$activities = MockData::activities()
    ->where('user_id', auth()->id())  // User's own activities
    ->take(5);  // Last 5
```

---

## 📋 User Fields

| Field | Type | Value |
|-------|------|-------|
| Name | String | From currentUser |
| Position | String | Disabled (read-only) |
| Email | String | Editable |
| Phone | String | Editable |
| Address | String | Editable |
| Avatar | Image | Current avatar |

---

## 🎨 Responsive

- **Desktop (>= 1200px):** 3 columns (1-3-8 ratio)
- **Tablet (768-1199px):** 2 columns (avatar card top, forms below)
- **Mobile (< 768px):** 1 column (stacked vertically)

---

## ⚠️ Features

✅ Display current user profile
✅ Timeline of user's activities
✅ Form fields with proper labels
✅ Avatar upload UI (visual)
✅ Responsive layout
❌ **Form submissions NOT functional** — "Save" buttons don't process (demo only)
❌ Avatar upload NOT implemented
❌ Password change NOT validated

---

## 💡 Future Enhancement

To make forms functional:
1. Add form validation (email, phone format, password strength)
2. Add CSRF token (for security)
3. Add POST routes: `/profile/update`, `/profile/change-password`
4. Add avatar upload handler
5. Add success/error toast notifications

---

## 🔗 Related

- User (currentUser via View Composer)
- Activities (user's own activity timeline)
- Settings (might extend this page for settings)
