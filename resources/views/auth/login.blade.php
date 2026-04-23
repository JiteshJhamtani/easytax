<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold mb-1 text-[#182b49] dark:text-white">Agent Portal</h1>
        <p class="text-[15px] text-[#8ba3b5] dark:text-gray-400">Secure Access to Dashboard</p>
    </div>
    
    <form id="authForm" method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div class="space-y-1.5">
            <label for="email" class="block text-[15px] font-bold text-[#182b49] dark:text-gray-200">Email Address</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="block w-full rounded-lg border py-3 px-4 focus:outline-none focus:ring-1 sm:text-sm transition-colors bg-white border-[#d1d5db] text-gray-900 focus:ring-[#5da565] focus:border-[#5da565] dark:bg-slate-700/50 dark:border-slate-600 dark:text-white dark:focus:ring-[#5da565] dark:focus:border-[#5da565]" 
            />
            @error('email') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
        
        <div class="space-y-1.5">
            <label for="password" class="block text-[15px] font-bold text-[#182b49] dark:text-gray-200">Password</label>
            <div class="relative rounded-md shadow-sm">
                <input type="password" name="password" id="password" required
                    class="block w-full rounded-lg border py-3 px-4 pr-16 focus:outline-none focus:ring-1 sm:text-sm transition-colors bg-white border-[#5da565] text-gray-900 focus:ring-[#5da565] focus:border-[#5da565] dark:bg-slate-700/50 dark:border-[#5da565] dark:text-white dark:focus:ring-[#5da565] dark:focus:border-[#5da565]" 
                />
                <button type="button" class="toggle-password-btn absolute inset-y-0 right-0 flex items-center pr-4 text-sm font-medium transition-colors text-[#8ba3b5] hover:text-[#182b49] dark:text-gray-400 dark:hover:text-gray-200">Show</button>
            </div>
            @error('password') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>
        
        <div class="flex items-center justify-end py-1">
            <div class="flex items-center gap-3">
                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#5da565] focus:ring-[#5da565] bg-white cursor-pointer" />
                <label for="remember_me" class="text-sm font-bold leading-tight cursor-pointer text-[#182b49] dark:text-gray-300">Remember<br/>Me</label>
            </div>
        </div>
        
        <button type="submit" id="submitBtn" class="flex w-full justify-center items-center rounded-lg py-3.5 px-4 text-[15px] font-bold text-white shadow-sm transition-all duration-300 bg-[#5da565] hover:bg-[#4d8c52] focus-visible:outline focus-visible:outline-[#5da565]">
            <span id="btnText">Login</span>
            <svg id="btnSpinner" class="hidden ml-2 h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
    </form>
</x-guest-layout>