<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold mb-1 text-[#182b49] dark:text-white">Create New Password</h1>
        <p class="text-[15px] text-[#8ba3b5] dark:text-gray-400">Please enter your new secure password below.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-1.5">
            <label for="email" class="block text-[15px] font-bold text-[#182b49] dark:text-gray-200">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}" required readonly
                class="block w-full rounded-lg border py-3 px-4 sm:text-sm bg-gray-100 border-[#d1d5db] text-gray-500 cursor-not-allowed dark:bg-slate-800 dark:border-slate-700 dark:text-gray-400" 
            />
            @error('email') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label for="password" class="block text-[15px] font-bold text-[#182b49] dark:text-gray-200">New Password</label>
            <input type="password" name="password" id="password" required autofocus autocomplete="new-password"
                class="block w-full rounded-lg border py-3 px-4 focus:outline-none focus:ring-1 sm:text-sm transition-colors bg-white border-[#d1d5db] text-gray-900 focus:ring-[#5da565] focus:border-[#5da565] dark:bg-slate-700/50 dark:border-slate-600 dark:text-white" 
            />
            @error('password') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <label for="password_confirmation" class="block text-[15px] font-bold text-[#182b49] dark:text-gray-200">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                class="block w-full rounded-lg border py-3 px-4 focus:outline-none focus:ring-1 sm:text-sm transition-colors bg-white border-[#d1d5db] text-gray-900 focus:ring-[#5da565] focus:border-[#5da565] dark:bg-slate-700/50 dark:border-slate-600 dark:text-white" 
            />
            @error('password_confirmation') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="flex w-full justify-center items-center rounded-lg py-3.5 px-4 text-[15px] font-bold text-white shadow-sm transition-all duration-300 bg-[#5da565] hover:bg-[#4d8c52] focus-visible:outline focus-visible:outline-[#5da565]">
                Reset Password
            </button>
        </div>
    </form>
</x-guest-layout>