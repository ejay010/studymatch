<x-layouts.public>
    <!-- Hero Section -->
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 tracking-tight leading-tight mb-6">
                Find the perfect tutor for your child's <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">learning
                    style.</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto mb-10">
                StudyMatch connects K-12 students with background-checked, top-tier educators for 1-on-1 tutoring and
                small group classes.
            </p>

            <div class="text-left max-w-4xl mx-auto">
                <livewire:find-tutor />
            </div>
        </div>
    </header>

    <!-- Value Props -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div
                        class="w-14 h-14 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Verified Educators</h3>
                    <p class="text-gray-600">Every teacher undergoes a strict background check and credential
                        verification process.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div
                        class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Seamless Scheduling</h3>
                    <p class="text-gray-600">No more email tag. Book sessions instantly based on real-time calendar
                        availability.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div
                        class="w-14 h-14 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Digital Resources</h3>
                    <p class="text-gray-600">Access thousands of worksheets, lesson plans, and flashcards curated by our
                        teachers.</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
