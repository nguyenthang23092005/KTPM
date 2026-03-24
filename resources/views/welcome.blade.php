<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Công ty TNHH THT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold logo-text">Công ty TNHH THT</h1>
            <div class="space-x-4">
                <a href="{{ route('jobs.index') }}" class="text-gray-600 hover:text-purple-600">Danh sách việc làm</a>
                @auth
                    <span class="text-gray-600">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('auth.logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('auth.login') }}" class="text-purple-600 hover:text-purple-800">Đăng nhập</a>
                    <a href="{{ route('auth.register') }}" class="text-purple-600 hover:text-purple-800">Đăng ký</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <div class="min-h-screen gradient-bg flex items-center justify-center px-4">
        <div class="text-center text-white">
            <h1 class="text-5xl font-bold mb-4">Công ty TNHH THT</h1>
            <p class="text-xl mb-8">Hệ thống quản lý tuyển dụng và nhân sự</p>
            <a href="{{ route('jobs.index') }}" class="login-btn px-8 py-3 text-white font-medium rounded-lg inline-block">
                Xem danh sách việc làm
            </a>
        </div>
    </div>
</body>
</html>