<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 space-y-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 border-b border-gray-200">
                <h3 class="text-lg font-medium">Selamat Datang, {{ $user->name ?? Auth::user()->name }}!</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Ini adalah dashboard standar untuk user biasa. Saat ini anda login dengan hak akses regular.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>