@extends('layouts.login')

@section('content')
    <main class="flex items-center justify-center min-h-screen bg-gray-100" aria-label="Halaman Login">
        <section class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 flex flex-col items-center"
            aria-labelledby="login-title">
            <header class="w-48 h-32 flex items-center justify-center bg-green-50 rounded mb-8" aria-label="Logo">
                <span class="text-gray-700 text-lg font-semibold" id="login-title">Logo</span>
            </header>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 w-full" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="w-full space-y-6" aria-label="Form Login">
                @csrf

                <!-- User Field -->
                <div>
                    <label for="email" class="block text-center font-medium text-gray-700 mb-2">User</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus
                        autocomplete="username"
                        class="block w-full px-4 py-3 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-green-600 border border-gray-300 text-center"
                        aria-label="User" />
                    @error('email')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-center font-medium text-gray-700 mb-2">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block w-full px-4 py-3 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-green-600 border border-gray-300 text-center"
                        aria-label="Password" />
                    @error('password')
                        <x-input-error :message="$message" />
                    @enderror
                </div>

                <!-- Login Button -->
                <div class="flex flex-col items-center mt-6">
                    <button type="submit"
                        class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition text-lg"
                        aria-label="Login">
                        Login
                    </button>
                </div>
            </form>
        </section>
    </main>
@endsection
