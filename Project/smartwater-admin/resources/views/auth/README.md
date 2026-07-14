# resources/views/auth/ — Authentication views

Chứa các view liên quan tới authentication.

## 📌 login.blade.php

**Công dụng:** Màn hình đăng nhập (demo/UI-only, không validate thực).

### Cách hoạt động

1. User bấm "Đăng nhập"
2. Form submit tới `/` (dashboard) **mà không validate mật khẩu**
3. Chuyển thẳng sang dashboard (không có authentication thực)

### Bao gồm

- Email input
- Password input
- "Đăng nhập" button
- "Quên mật khẩu?" link (dead link)
- SmartWater logo

### Styling

- Bootstrap form
- Center layout
- Blue accent (Truliva theme)
- Responsive (mobile-friendly)

---

## ⚠️ Ghi chú

- **KHÔNG có backend validation** — input bất kỳ đều được
- **KHÔNG có session/cookie** — reload trang sẽ quay lại login (chỉ là UI demo)
- Nếu muốn thêm authentication thực, cần thêm login middleware + session management

### Để test Demo

1. Goto `/login`
2. Nhập bất kỳ email/password
3. Bấm "Đăng nhập"
4. Chuyển sang `/` dashboard

Hoặc trực tiếp truy cập `/` để skip login.
