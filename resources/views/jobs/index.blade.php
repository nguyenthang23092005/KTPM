<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách việc làm - Công ty TNHH THT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-bold logo-text">Công ty TNHH THT</a>
            <div class="space-x-4">
                <a href="{{ route('jobs.index') }}" class="text-purple-600 font-medium">Danh sách việc làm</a>
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

    <!-- Jobs List -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-8">Danh sách việc làm</h1>

        @if ($jobs->count() > 0)
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-1">
                @foreach ($jobs as $job)
                    <div class="register-card rounded-lg p-6 border">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold logo-text">{{ $job->title }}</h3>
                                <p class="text-gray-600">{{ $job->department }}</p>
                            </div>
                            <span class="text-green-600 font-bold">{{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }} VND</span>
                        </div>
                        <p class="text-gray-700 mb-4">{{ Str::limit($job->description, 200) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Hạn nộp: {{ $job->deadline?->format('d/m/Y') ?? 'N/A' }}</span>
                            <a href="{{ route('jobs.show', $job->id) }}" class="login-btn px-4 py-2 text-white font-medium rounded text-sm">
                                Chi tiết
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $jobs->links() }}
            </div>
        @else
            <p class="text-gray-600">Không có việc làm nào.</p>
        @endif
    </div>
</body>
</html>