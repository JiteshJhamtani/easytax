<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold mb-1 text-[#182b49] dark:text-white">Create Account</h1>
        <p class="text-[15px] text-[#8ba3b5] dark:text-gray-400">Join the Agent Portal</p>
    </div>
    
    <form id="authForm" method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div class="space-y-1.5">
            <label for="name" class="block text-[14px] font-bold text-[#182b49] dark:text-gray-200">Full Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                class="block w-full rounded-lg border py-2.5 px-4 focus:outline-none focus:ring-1 sm:text-sm transition-colors bg-white border-[#d1d5db] text-gray-900 focus:ring-[#5da565] focus:border-[#5da565] dark:bg-slate-700/50 dark:border-slate-600 dark:text-white dark:focus:ring-[#5da565] dark:focus:border-[#5da565]" 
            />
            @error('name') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>

        {{-- Email Address --}}
        <div class="space-y-1.5">
            <label for="email" class="block text-[14px] font-bold text-[#182b49] dark:text-gray-200">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="block w-full rounded-lg border py-2.5 px-4 focus:outline-none focus:ring-1 sm:text-sm transition-colors bg-white border-[#d1d5db] text-gray-900 focus:ring-[#5da565] focus:border-[#5da565] dark:bg-slate-700/50 dark:border-slate-600 dark:text-white dark:focus:ring-[#5da565] dark:focus:border-[#5da565]" 
            />
            @error('email') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
        
        {{-- Password --}}
        <div class="space-y-1.5">
            <label for="password" class="block text-[14px] font-bold text-[#182b49] dark:text-gray-200">Password</label>
            <div class="relative rounded-md shadow-sm">
                <input type="password" name="password" id="password" required
                    class="block w-full rounded-lg border py-2.5 px-4 pr-16 focus:outline-none focus:ring-1 sm:text-sm transition-colors bg-white border-[#5da565] text-gray-900 focus:ring-[#5da565] focus:border-[#5da565] dark:bg-slate-700/50 dark:border-[#5da565] dark:text-white dark:focus:ring-[#5da565] dark:focus:border-[#5da565]" 
                />
                <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium transition-colors text-[#8ba3b5] hover:text-[#182b49] dark:text-gray-400 dark:hover:text-gray-200">Show</button>
            </div>
            @error('password') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="space-y-1.5">
            <label for="password_confirmation" class="block text-[14px] font-bold text-[#182b49] dark:text-gray-200">Confirm Password</label>
            <div class="relative rounded-md shadow-sm">
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="block w-full rounded-lg border py-2.5 px-4 pr-16 focus:outline-none focus:ring-1 sm:text-sm transition-colors bg-white border-[#d1d5db] text-gray-900 focus:ring-[#5da565] focus:border-[#5da565] dark:bg-slate-700/50 dark:border-slate-600 dark:text-white dark:focus:ring-[#5da565] dark:focus:border-[#5da565]" 
                />
                <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium transition-colors text-[#8ba3b5] hover:text-[#182b49] dark:text-gray-400 dark:hover:text-gray-200">Show</button>
            </div>
            @error('password_confirmation') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
        
        <div class="flex items-center justify-between pt-3">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-[#5da565] hover:text-[#4d8c52] transition-colors">
                Already registered?
            </a>
            
            <button type="submit" id="submitBtn" class="flex justify-center items-center rounded-lg py-2.5 px-6 text-[14px] font-bold text-white shadow-sm transition-all duration-300 bg-[#5da565] hover:bg-[#4d8c52] focus-visible:outline focus-visible:outline-[#5da565]">
                <span id="btnText">Register</span>
                <svg id="btnSpinner" class="hidden ml-2 h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </button>
        </div>
    </form>
</x-guest-layout>