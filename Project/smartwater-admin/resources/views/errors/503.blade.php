<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hệ thống tạm thời không khả dụng</title>
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #f5f7fb; color: #1f2937; }
        main { max-width: 560px; margin: 12vh auto; padding: 2.5rem; text-align: center; background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgb(15 23 42 / 10%); }
        a { display: inline-block; margin-top: 1.5rem; padding: .7rem 1rem; border-radius: 6px; color: #fff; background: #0d6efd; text-decoration: none; }
    </style>
</head>
<body>
    <main>
        <h1>Hệ thống tạm thời không khả dụng</h1>
        <p>Không thể kết nối đến dịch vụ dữ liệu. Vui lòng thử lại sau.</p>
        <a href="{{ url()->current() }}">Thử lại</a>
    </main>
</body>
</html>
