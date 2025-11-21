<x-dashboard-layout title="Dashboard">
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden bg-gradient-to-br from-red-600 to-red-700 rounded-2xl p-6 sm:p-8 mb-8 shadow-xl">
        <div class="absolute top-0 right-0 -mt-4 -mr-16 w-64 h-64 bg-red-500 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-16 w-64 h-64 bg-red-800 rounded-full opacity-20 blur-3xl"></div>
        
        <div class="relative z-10">
            <h2 class="text-2xl sm:text-3xl font-black text-white mb-2">Welcome back, {{ auth()->user()->name ?? 'Admin' }}! 👋</h2>
            <p class="text-red-50 text-sm sm:text-base max-w-2xl">Here's what's happening with your Najaah platform today. Track cards, manage users, and monitor your emergency response system.</p>
            
            <div class="flex flex-wrap gap-3 mt-6">
                <button class="px-6 py-3 bg-white text-red-600 font-bold rounded-xl hover:bg-red-50 transition-all transform hover:scale-105 shadow-lg">
                    View Analytics
                </button>
                <button class="px-6 py-3 bg-red-500 bg-opacity-20 text-white font-bold rounded-xl hover:bg-opacity-30 transition-all backdrop-blur-sm border border-white border-opacity-20">
                    Download Reports
                </button>
            </div>
        </div>
    </div>

    </div>
</x-dashboard-layout>