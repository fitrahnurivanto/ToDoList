<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas - ToDoList</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .animate-fade-in { animation: fade-in 0.7s cubic-bezier(.4,0,.2,1) both; }
        ::-webkit-scrollbar { width: 10px; background: #d1fae5; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 8px; }
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
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-emerald-500 hover:text-emerald-700 mr-6 p-2 rounded-xl hover:bg-emerald-100 transition">
                    <i class="fas fa-arrow-left text-2xl"></i>
                </a>
                <div class="bg-emerald-100 rounded-xl p-3 mr-4">
                    <svg class="h-8 w-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-emerald-700 tracking-wide">
                        Edit Tugas
                    </h1>
                    <p class="text-sm text-gray-500">Ubah detail tugas sesuai kebutuhanmu</p>
                </div>
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

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Form Card -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden animate-fade-in">
            <div class="bg-emerald-500 p-8 text-white rounded-t-3xl">
                <h2 class="text-2xl font-bold mb-2">
                    Edit Detail Tugas
                </h2>
                <p class="opacity-90">Perbarui form di bawah untuk mengedit tugas pada ToDoList kamu.</p>
            </div>
            <div class="p-8">
                <form action="{{ route('todos.update', $todo) }}" method="POST" class="space-y-8" id="editForm">
                    @csrf
                    @method('PUT')

                    <!-- Title Field -->
                    <div>
                        <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                            Judul Tugas *
                        </label>
                        <input type="text"
                               id="title"
                               name="title"
                               value="{{ old('title', $todo->title) }}"
                               class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition text-lg @error('title') border-red-500 @enderror"
                               placeholder="Masukkan judul tugas..."
                               required maxlength="255">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 p-3 rounded-xl">
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description Field -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition text-lg resize-none @error('description') border-red-500 @enderror"
                                  placeholder="Deskripsi tugas (opsional)" maxlength="1000">{{ old('description', $todo->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 p-3 rounded-xl">
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Deadline Field -->
                    <div>
                        <label for="deadline" class="block text-sm font-semibold text-gray-700 mb-2">
                            Deadline
                        </label>
                        <input type="date"
                               id="deadline"
                               name="deadline"
                               value="{{ old('deadline', $todo->deadline ? \Carbon\Carbon::parse($todo->deadline)->format('Y-m-d') : '') }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition text-lg @error('deadline') border-red-500 @enderror">
                        @error('deadline')
                            <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 p-3 rounded-xl">
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                            </p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Kosongkan jika tidak ada deadline khusus.</p>
                    </div>

                    <!-- Status Field -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                            Status *
                        </label>
                        <select id="status"
                                name="status"
                                class="w-full px-6 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition text-lg @error('status') border-red-500 @enderror"
                                required>
                            <option value="pending" {{ old('status', $todo->status) === 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="late" {{ old('status', $todo->status) === 'late' ? 'selected' : '' }}>
                                Terlambat
                            </option>
                            <option value="completed" {{ old('status', $todo->status) === 'completed' ? 'selected' : '' }}>
                                Selesai
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 flex items-center bg-red-50 p-3 rounded-xl">
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-between pt-8 border-t-2 border-gray-100">
                        <a href="{{ route('home') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-8 py-4 rounded-2xl font-semibold transition flex items-center shadow">
                            <i class="fas fa-times mr-3"></i>Batal
                        </a>
                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-10 py-4 rounded-2xl font-semibold transition flex items-center shadow">
                            <i class="fas fa-save mr-3"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Motivasi Quote -->
        <div class="mt-10 text-center">
            <div class="bg-white rounded-2xl shadow p-6 max-w-xl mx-auto border-t-4 border-emerald-500">
                <i class="fas fa-quote-left text-emerald-500 text-2xl mb-4"></i>
                <p class="text-gray-700 italic text-lg mb-3">
                    "Perubahan kecil hari ini bisa membawa hasil besar di masa depan."
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

    <!-- Dark Mode Script -->
    <script>
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
