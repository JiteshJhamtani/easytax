<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold mb-1 text-[#182b49] dark:text-white">Forgot Password?</h1>
        <p class="text-[15px] text-[#8ba3b5] dark:text-gray-400">No problem. Enter your email and we'll send you a reset link.</p>
    </div>

    <x-auth-session-status class="mb-6 text-sm font-bold text-[#5da565] bg-[#eef6ef] p-4 rounded-lg border border-[#5da565]/20 text-center" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div class="space-y-1.5">
            <label for="email" class="block text-[15px] font-bold text-[#182b49] dark:text-gray-200">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="block w-full rounded-lg border py-3 px-4 focus:outline-none focus:ring-1 sm:text-sm transition-colors bg-white border-[#d1d5db] text-gray-900 focus:ring-[#5da565] focus:border-[#5da565] dark:bg-slate-700/50 dark:border-slate-600 dark:text-white dark:focus:ring-[#5da565] dark:focus:border-[#5da565]" 
            />
            @error('email') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="flex w-full justify-center items-center rounded-lg py-3.5 px-4 text-[15px] font-bold text-white shadow-sm transition-all duration-300 bg-[#5da565] hover:bg-[#4d8c52] focus-visible:outline focus-visible:outline-[#5da565]">
                Send Password Reset Link
            </button>
        </div>

        <div class="text-center mt-2">
            <a href="{{ route('login') }}" class="text-sm font-bold text-[#8ba3b5] hover:text-[#182b49] transition-colors dark:hover:text-white">
                &larr; Back to Login
            </a>
        </div>
    </form>
</x-guest-layout>