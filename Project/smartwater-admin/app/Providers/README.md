# `app/Providers/`

`AppServiceProvider` đăng ký view composer cho mọi view thuộc `layouts.*`.

Composer cung cấp `currentUser` và `navNotifications` từ `App\Support\MockData` để layout có sẵn dữ liệu navbar. Database configuration, migration và seeding không được cấu hình ở provider này.
