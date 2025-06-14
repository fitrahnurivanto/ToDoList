<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8B4513',      // Saddle Brown
                        secondary: '#D2691E',    // Chocolate
                        accent: '#DAA520',       // Goldenrod
                        gold: '#FFD700',         // Gold
                        cream: '#F5F5DC',        // Beige
                        darkBrown: '#654321',    // Dark Brown
                        lightGold: '#FFF8DC',    // Cornsilk
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .animate-fade-in { animation: fade-in 0.7s cubic-bezier(.4,0,.2,1) both; }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            background: #d1fae5;
        }
        ::-webkit-scrollbar-thumb {
            background: #10b981;
            border-radius: 8px;
        }

        /* Dark mode styles */
        .dark body { background: #1f2937; }
        .dark .bg-white { background: #23272f !important; }
        .dark .text-gray-700, .dark .text-gray-900 { color: #e5e7eb !important; }
        .dark .text-gray-500 { color: #9ca3af !important; }
        .dark .border-gray-100 { border-color: #374151 !important; }
        .dark .shadow { box-shadow: 0 2px 8px rgba(0,0,0,0.7) !important; }
        .dark .bg-emerald-50 { background: #064e3b !important; }
        .dark .bg-green-50 { background: #065f46 !important; }
        .dark .bg-yellow-50 { background: #78350f !important; }
        .dark .bg-red-50 { background: #7f1d1d !important; }
        .dark .bg-gray-50 { background: #374151 !important; }
        .dark .bg-gray-100 { background: #1f2937 !important; }
        .dark .border-emerald-500 { border-color: #10b981 !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-emerald-50 to-emerald-100 min-h-screen font-poppins">
    <!-- Header -->
    <header class="bg-white shadow-lg border-b border-emerald-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-20">
            <div class="flex items-center space-x-4">
                <div class="bg-emerald-100 rounded-xl p-3">
                    <svg class="h-8 w-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-emerald-700 tracking-wide">ToDoList</span>
            </div>
            <div class="flex items-center space-x-6">
                <!-- Avatar User -->
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=10b981&color=fff&rounded=true&size=40"
                     alt="Avatar" class="w-10 h-10 rounded-full border-2 border-emerald-400 shadow mr-2">
                <div class="text-right">
                    <span class="text-gray-500 text-sm block">Halo,</span>
                    <span class="text-emerald-700 font-semibold">{{ Auth::user()->name }}</span>
                </div>
                <!-- Dark Mode Toggle -->
                <button id="darkModeToggle" class="ml-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-full p-2 transition" title="Toggle Dark Mode">
                    <i id="darkModeIcon" class="fas fa-moon"></i>
                </button>
                <a href="{{ route('logout') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl transition font-semibold shadow">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 px-6 py-4 rounded-xl mb-6 flex items-center shadow">
                <i class="fas fa-check-circle mr-3 text-xl"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Progress Ring di Stats Card Total -->
            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-emerald-500 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ $stats['total'] }}</p>
                </div>
                <div class="relative flex items-center justify-center">
                    <svg class="w-14 h-14" viewBox="0 0 36 36">
                        <circle class="text-emerald-100" stroke-width="4" stroke="currentColor" fill="none" cx="18" cy="18" r="16"/>
                        <circle class="text-emerald-500" stroke-width="4" stroke-dasharray="100,100"
                            :stroke-dasharray="'{{ round(($stats['completed']/$stats['total'])*100) }},100'"
                            stroke-linecap="round" stroke="currentColor" fill="none" cx="18" cy="18" r="16"
                            style="stroke-dasharray: {{ round(($stats['completed']/$stats['total'])*100) }},100;"/>
                        <text x="18" y="22" text-anchor="middle" class="text-base fill-emerald-700 font-bold">{{ $stats['total'] > 0 ? round(($stats['completed']/$stats['total'])*100) : 0 }}%</text>
                    </svg>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Selesai</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="p-4 rounded-full bg-green-50 text-green-500">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-yellow-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                        <p class="text-3xl font-bold text-yellow-500">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="p-4 rounded-full bg-yellow-50 text-yellow-500">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow p-6 border-l-4 border-red-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Terlambat</p>
                        <p class="text-3xl font-bold text-red-500">{{ $stats['late'] }}</p>
                    </div>
                    <div class="p-4 rounded-full bg-red-50 text-red-500">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-emerald-700 mb-1">Daftar Tugas</h2>
                <p class="text-gray-500">Kelola aktivitas harianmu dengan mudah</p>
            </div>
            <a href="{{ route('todos.create') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-xl font-semibold transition shadow flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Tugas
            </a>
        </div>

        <!-- Todo List -->
        <div class="bg-white rounded-3xl shadow border border-gray-100 overflow-hidden">
            @if($todos->count() > 0)
                @php
                    $incompleteTodos = $todos->where('status', '!=', 'completed');
                    $completedTodos = $todos->where('status', 'completed');
                @endphp

                <div class="divide-y divide-gray-100">
                    <!-- Active Tasks Section -->
                    @if($incompleteTodos->count() > 0)
                        @foreach($incompleteTodos as $todo)
                            <div class="p-8 hover:bg-emerald-50 transition group animate-fade-in">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-6 flex-1">
                                        <!-- Status Toggle -->
                                        <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-2xl transition hover:scale-125">
                                                <i class="far fa-circle text-gray-400 group-hover:text-emerald-500"></i>
                                            </button>
                                        </form>
                                        <!-- Todo Content -->
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-emerald-700 transition">
                                                {{ $todo->title }}
                                            </h3>
                                            @if($todo->description)
                                                <p class="text-gray-600 mb-4 leading-relaxed">
                                                    {{ $todo->description }}
                                                </p>
                                            @endif
                                            <div class="flex items-center space-x-6">
                                                @if($todo->status === 'late')
                                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700 border border-red-200">
                                                        <i class="fas fa-fire mr-2"></i>Terlambat
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                                        <i class="fas fa-clock mr-2"></i>Pending
                                                    </span>
                                                @endif
                                                @if($todo->deadline)
                                                    <span class="text-sm text-gray-500 flex items-center bg-gray-50 px-3 py-1 rounded-full">
                                                        <i class="fas fa-calendar-alt mr-2"></i>
                                                        {{ \Carbon\Carbon::parse($todo->deadline)->format('d M Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Actions -->
                                    <div class="flex items-center space-x-3 ml-6">
                                        <a href="{{ route('todos.edit', $todo) }}"
                                           class="text-emerald-600 hover:text-emerald-800 p-3 rounded-xl hover:bg-emerald-50 transition"
                                           title="Edit">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                        <form action="{{ route('todos.destroy', $todo) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-3 rounded-xl hover:bg-red-50 transition">
                                                <i class="fas fa-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Completed Tasks Section -->
                    @if($completedTodos->count() > 0)
                        @if($incompleteTodos->count() > 0)
                            <div class="bg-emerald-50 px-8 py-4 border-t-2 border-emerald-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-emerald-700 flex items-center">
                                        <i class="fas fa-check-circle text-emerald-500 mr-3 text-xl"></i>
                                        Selesai ({{ $completedTodos->count() }})
                                    </h3>
                                    <button onclick="toggleCompletedTasks()" id="toggleCompletedBtn" class="text-sm text-emerald-700 hover:text-emerald-900 flex items-center bg-emerald-100 hover:bg-emerald-200 px-4 py-2 rounded-full transition">
                                        <span id="toggleText">Sembunyikan</span>
                                        <i id="toggleIcon" class="fas fa-chevron-up ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div id="completedTasksContainer">
                            @foreach($completedTodos as $todo)
                                <div class="p-8 hover:bg-green-50 transition bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 animate-fade-in">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-6 flex-1">
                                            <div class="mt-2">
                                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-xl font-bold text-gray-700 line-through mb-2 flex items-center">
                                                    {{ $todo->title }}
                                                    <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-800">
                                                        <i class="fas fa-star mr-1"></i>Selesai
                                                    </span>
                                                </h3>
                                                @if($todo->description)
                                                    <p class="text-gray-500 mb-4 line-through leading-relaxed">
                                                        {{ $todo->description }}
                                                    </p>
                                                @endif
                                                <div class="flex items-center space-x-6">
                                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-500 text-white">
                                                        <i class="fas fa-trophy mr-2"></i>Done
                                                    </span>
                                                    @if($todo->deadline)
                                                        <span class="text-sm text-gray-500 flex items-center bg-gray-100 px-3 py-1 rounded-full">
                                                            <i class="fas fa-calendar-alt mr-2"></i>
                                                            {{ \Carbon\Carbon::parse($todo->deadline)->format('d M Y') }}
                                                        </span>
                                                    @endif
                                                    @if($todo->completed_at)
                                                        <span class="text-sm text-green-700 flex items-center bg-green-100 px-3 py-1 rounded-full font-medium">
                                                            <i class="fas fa-check mr-2"></i>
                                                            Selesai {{ \Carbon\Carbon::parse($todo->completed_at)->format('d M Y') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3 ml-6">
                                            <form action="{{ route('todos.toggle', $todo) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-blue-600 hover:text-blue-800 p-3 rounded-xl hover:bg-blue-50 transition"
                                                        title="Buka kembali">
                                                    <i class="fas fa-undo text-lg"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('todos.destroy', $todo) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus tugas selesai ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 p-3 rounded-xl hover:bg-red-50 transition">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20">
                    <div class="mb-8">
                        <svg class="mx-auto h-20 w-20 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-emerald-700 mb-4">Belum ada tugas</h3>
                    <p class="text-gray-500 mb-2">Yuk mulai tambah tugas pertamamu!</p>
                    <a href="{{ route('todos.create') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-xl font-semibold transition shadow inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>Buat Tugas Baru
                    </a>
                </div>
            @endif
        </div>

        <!-- Progress Summary -->
        @if($todos->count() > 0)
            <div class="mt-8 bg-white rounded-3xl p-8 shadow text-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold mb-2 flex items-center text-emerald-700">
                            <i class="fas fa-chart-line mr-3"></i>
                            Progress
                        </h3>
                        <p class="text-gray-500">
                            Kamu telah menyelesaikan {{ $stats['completed'] }} dari {{ $stats['total'] }} tugas
                            @if($stats['total'] > 0)
                                ({{ round(($stats['completed'] / $stats['total']) * 100) }}%)
                            @endif
                        </p>
                    </div>
                </div>
                @if($stats['total'] > 0)
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm text-gray-500 mb-2">
                            <span class="font-medium">Progress Keseluruhan</span>
                            <span class="font-bold">{{ round(($stats['completed'] / $stats['total']) * 100) }}%</span>
                        </div>
                        <div class="w-full bg-emerald-100 rounded-full h-4 overflow-hidden">
                            <div class="bg-emerald-500 h-4 rounded-full transition-all duration-1000 ease-out shadow"
                                 style="width: {{ ($stats['completed'] / $stats['total']) * 100 }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Footer Quote -->
        <div class="mt-12 text-center">
            <div class="bg-white rounded-2xl shadow p-6 max-w-2xl mx-auto border-t-4 border-emerald-500">
                <i class="fas fa-quote-left text-emerald-500 text-2xl mb-4"></i>
                <p class="text-gray-700 italic text-lg mb-3">
                    "Kerjakan tugasmu dengan konsisten, hasil tidak akan mengkhianati usaha."
                </p>
                <p class="text-emerald-700 font-semibold">- ToDoList App</p>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    @if(session('toast'))
        <div id="toastNotif" class="fixed top-6 right-6 z-50 bg-emerald-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center animate-fade-in">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <span class="font-medium">{{ session('toast') }}</span>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('toastNotif').style.display = 'none';
            }, 3000);
        </script>
    @endif

    <script>
        // Toggle completed tasks
        function toggleCompletedTasks() {
            const container = document.getElementById('completedTasksContainer');
            const toggleBtn = document.getElementById('toggleCompletedBtn');
            const toggleText = document.getElementById('toggleText');
            const toggleIcon = document.getElementById('toggleIcon');

            if (container.style.display === 'none' || container.style.maxHeight === '0px') {
                container.style.display = 'block';
                container.style.maxHeight = '2000px';
                container.style.opacity = '1';
                toggleText.textContent = 'Sembunyikan';
                toggleIcon.className = 'fas fa-chevron-up ml-2';
                localStorage.setItem('completedTasksVisible', 'true');
            } else {
                container.style.opacity = '0';
                toggleText.textContent = 'Tampilkan';
                toggleIcon.className = 'fas fa-chevron-down ml-2';
                localStorage.setItem('completedTasksVisible', 'false');
                setTimeout(() => {
                    container.style.display = 'none';
                    container.style.maxHeight = '0px';
                }, 300);
            }
        }

        // Restore completed tasks visibility state
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('completedTasksContainer');
            const toggleText = document.getElementById('toggleText');
            const toggleIcon = document.getElementById('toggleIcon');
            const isVisible = localStorage.getItem('completedTasksVisible');

            if (isVisible === 'false' && container) {
                container.style.display = 'none';
                container.style.maxHeight = '0px';
                toggleText.textContent = 'Tampilkan';
                toggleIcon.className = 'fas fa-chevron-down ml-2';
            }
        });

        const toggle = document.getElementById('darkModeToggle');
        const icon = document.getElementById('darkModeIcon');
        const html = document.documentElement;

        if(localStorage.getItem('theme') === 'dark') {
            html.classList.add('dark');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }

        toggle.addEventListener('click', function() {
            html.classList.toggle('dark');
            if(html.classList.contains('dark')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            }
        });
    </script>
</body>
</html>
