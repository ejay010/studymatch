<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Parent Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Subscription Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ isset($studentProfile) && $studentProfile->is_premium ? 'border-purple-500' : 'border-gray-300' }}">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold">StudyMatch Premium</h3>
                        <p class="text-gray-600 text-sm mt-1">
                            @if(isset($studentProfile) && $studentProfile->is_premium)
                                You are a Premium subscriber! Enjoy priority search rankings and free physical worksheets.
                            @else
                                Upgrade to Premium for $9.99/mo to get priority search rankings and free physical worksheets shipped to you.
                            @endif
                        </p>
                    </div>
                    <div>
                        @if(isset($studentProfile) && $studentProfile->is_premium)
                            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm hover:bg-gray-50 font-semibold">Manage Subscription</button>
                        @else
                            <button class="bg-purple-600 text-white px-4 py-2 rounded shadow-sm hover:bg-purple-700 font-semibold">Upgrade Now</button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Upcoming Classes -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-bold mb-4">Upcoming Classes</h3>
                        
                        <div class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-200">
                            <div class="text-4xl mb-3">📅</div>
                            <h4 class="font-medium text-gray-900 mb-1">No upcoming classes</h4>
                            <p class="text-sm text-gray-500 mb-4">You haven't booked any 1-on-1 sessions or group classes yet.</p>
                            <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded font-medium hover:bg-blue-700">Find a Tutor</a>
                        </div>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-bold mb-4">My Digital Resources</h3>
                        <div class="bg-gray-50 rounded-lg p-8 text-center border-2 border-dashed border-gray-200">
                            <p class="text-sm text-gray-500">Your purchased worksheets and lesson plans will appear here.</p>
                        </div>
                    </div>
                </div>

                <!-- Actions / Profile -->
                <div class="space-y-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                        <h3 class="text-lg font-bold mb-4">My Students</h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-md border border-gray-200">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ auth()->user()->name }}'s Child</div>
                                    <div class="text-xs text-gray-500">Grade level not set</div>
                                </div>
                            </div>
                            <button class="w-full text-center px-4 py-2 bg-white hover:bg-gray-50 rounded-md transition text-sm font-medium border border-gray-200 border-dashed text-gray-600">
                                + Add Student
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
