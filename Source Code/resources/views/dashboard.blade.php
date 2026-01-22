<x-app-layout>
    <main aria-labelledby="dashboard-title" role="main" tabindex="-1">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <header role="banner" class="mb-6">
                    <h1 class="font-semibold text-xl text-gray-800 leading-tight" id="dashboard-title">
                        {{ __('Dashboard') }}
                    </h1>
                </header>
                <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg" aria-label="Status Login">
                    <div class="p-6 text-gray-900">
                        <span class="inline-flex items-center gap-2">
                                <i class="fas fa-check-circle w-5 h-5 text-green-600" aria-hidden="true"></i>
                            <span>{{ __("Anda berhasil login!") }}</span>
                        </span>
                    </div>
                </section>
            </div>
        </div>
    </main>
</x-app-layout>
