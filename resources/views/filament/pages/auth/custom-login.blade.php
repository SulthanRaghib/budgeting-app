<div class="custom-login-wrapper">
    @php
        $primaryColor = \Filament\Support\Colors\Color::Amber;
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f9fafb;
        }

        /* Override Filament Default Layout Styles */
        .fi-simple-main {
            max-width: 100% !important;
            margin-block: 0 !important;
            background-color: transparent !important;
        }

        .login-card {
            transition: box-shadow 0.3s ease;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }

        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .input-field:focus {
            outline: none;
            transform: translateY(-2px);
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2);
        }

        .login-button {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            background-color: #fbbf24;
            color: #000;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(251, 191, 36, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .logo-icon {
            font-size: 3rem;
            display: inline-block;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .loading-spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            margin-right: 8px;
            vertical-align: middle;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <div
        class="min-h-screen w-full flex items-center justify-center px-4 py-8 bg-gradient-to-br from-amber-50 to-amber-100">
        <div class="login-card bg-white rounded-3xl p-8 md:p-12 w-full max-w-md border-amber-100">

            <div class="text-center mb-8">
                <div class="logo-icon mb-4 text-4xl">💰</div>
                <h1 class="text-3xl md:text-4xl font-bold mb-2 text-amber-500 tracking-tight">BudgetKu</h1>
            </div>

            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold mb-2 text-gray-900">Selamat Datang</h2>
                <p class="text-sm md:text-base text-gray-500">Masuk untuk mengelola keuangan Anda</p>
            </div>

            <form wire:submit="authenticate" class="space-y-6" novalidate>

                <div>
                    <label for="email" class="block text-sm font-bold mb-2 text-gray-700">Email Address</label>
                    <input type="email" wire:model="data.email" id="email"
                        class="input-field w-full px-4 py-3.5 rounded-xl text-base bg-gray-50 focus:bg-white transition-colors @error('data.email') border-red-500 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        placeholder="nama@email.com" autofocus autocomplete="email">
                    @error('data.email')
                        <p class="text-red-500 text-sm mt-1 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-bold mb-2 text-gray-700">Password</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" wire:model="data.password" id="password"
                            class="input-field w-full px-4 py-3.5 rounded-xl text-base bg-gray-50 focus:bg-white transition-colors pr-12 @error('data.password') border-red-500 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                            placeholder="••••••••" autocomplete="current-password">
                        <button type="button" @click="show = !show"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none p-1">
                            <template x-if="!show">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </template>
                            <template x-if="show">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </template>
                        </button>
                    </div>
                    @error('data.password')
                        <p class="text-red-500 text-sm mt-1 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 cursor-pointer group">
                        <input type="checkbox" wire:model="data.remember"
                            class="w-5 h-5 rounded border-gray-300 text-amber-500 focus:ring-amber-500 transition-colors">
                        <span
                            class="text-sm text-gray-600 font-medium group-hover:text-gray-800 transition-colors">Ingat
                            saya</span>
                    </label>
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="login-button w-full py-4 rounded-xl font-bold text-base md:text-lg shadow-lg transition-all duration-200 hover:shadow-xl disabled:opacity-70 disabled:cursor-not-allowed text-center">
                    <span wire:loading.remove wire:target="authenticate">Masuk</span>
                    <span wire:loading wire:target="authenticate" class="loading-text"><svg class="loading-spinner"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path style="opacity:0.75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>Memproses...</span>
                </button>
            </form>

        </div>
    </div>

    @livewire('notifications')
</div>
