<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $job->title }} - Công ty TNHH THT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold logo-text">Công ty TNHH THT</a>
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
                @endauth
            </div>
        </div>
    </nav>

    <!-- Job Detail -->
    <div class="max-w-3xl mx-auto px-4 py-12">
        <div class="register-card rounded-lg p-8 border">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold logo-text mb-2">{{ $job->title }}</h1>
                    <p class="text-gray-600 text-lg">{{ $job->department }}</p>
                </div>
                <span class="text-green-600 font-bold text-xl">{{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }} VND</span>
            </div>

            <!-- Meta -->
            <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b">
                <div>
                    <p class="text-sm text-gray-600">Vị trí</p>
                    <p class="font-semibold">{{ $job->title }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Hạn nộp</p>
                    <p class="font-semibold">{{ $job->deadline?->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Địa điểm</p>
                    <p class="font-semibold">{{ $job->location ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Trạng thái</p>
                    <p class="font-semibold text-green-600">{{ $job->status }}</p>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <h2 class="text-xl font-bold mb-3">Mô tả công việc</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $job->description }}</p>
            </div>

            <!-- Requirements -->
            <div class="mb-6">
                <h2 class="text-xl font-bold mb-3">Yêu cầu</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $job->requirements }}</p>
            </div>

            <!-- Apply Button -->
            <a href="{{ route('jobs.index') }}" class="inline-block login-btn px-6 py-3 text-white font-medium rounded-lg mb-4">
                ← Quay lại
            </a>
        </div>
    </div>
</body>
</html>