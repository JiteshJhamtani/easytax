<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('Account Settings') }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">Manage your personal profile, security credentials, and system preferences.</p>
            </div>
            <div class="hidden sm:block">
                <div class="h-14 w-14 rounded-full bg-gradient-to-tr from-green-500 to-emerald-400 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-green-500/30 border-2 border-white dark:border-slate-800">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 dark:bg-slate-900/50 min-h-screen relative">
        <!-- Decorative Background Blob -->
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-green-50/50 to-transparent dark:from-green-900/10 dark:to-transparent -z-10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Left Column (Profile & Notifications) --}}
                <div class="lg:col-span-7 space-y-8">
                    
                    {{-- Profile Info Card --}}
                    <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-700 p-8 sm:p-10 transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 overflow-hidden group">
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full opacity-[0.08] blur-2xl group-hover:opacity-20 transition-opacity duration-500"></div>
                        
                        <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-green-400 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10 w-full">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- Notifications Card --}}
                    @if(auth()->user()->isAdmin())
                    <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-700 p-8 sm:p-10 transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 overflow-hidden group">
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full opacity-[0.08] blur-2xl group-hover:opacity-20 transition-opacity duration-500"></div>
                        
                        <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-400 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10 w-full">
                            @include('profile.partials.update-notification-preference-form')
                        </div>
                    </div>
                    @endif

                </div>

                {{-- Right Column (Security & Danger Zone) --}}
                <div class="lg:col-span-5 space-y-8">
                    
                    {{-- Password Card --}}
                    <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-100 dark:border-slate-700 p-8 sm:p-10 transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 overflow-hidden group">
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full opacity-[0.08] blur-2xl group-hover:opacity-20 transition-opacity duration-500"></div>
                        
                        <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-purple-400 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10 w-full">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- Danger Zone Card --}}
                    <div class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-red-100 dark:border-red-900/30 p-8 sm:p-10 transition-all duration-300 hover:shadow-[0_8px_30px_rgb(220,38,38,0.1)] hover:-translate-y-1 overflow-hidden group">
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-gradient-to-br from-red-400 to-rose-500 rounded-full opacity-[0.08] blur-2xl group-hover:opacity-[0.15] transition-opacity duration-500"></div>
                        
                        <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-red-400 to-rose-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10 w-full">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
