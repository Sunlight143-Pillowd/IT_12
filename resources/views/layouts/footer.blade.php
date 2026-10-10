<footer class="mt-auto bg-[#111827] py-10 text-gray-300">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 text-sm sm:px-6 lg:grid-cols-3 lg:items-start lg:px-8">
        <div>
            <h2 class="font-bold text-white">Address</h2>
            <p class="mt-2 leading-relaxed text-gray-400">{{ config('store.address') }}</p>
<<<<<<< HEAD
            <a href="https://maps.google.com/?q={{ urlencode(config('store.address')) }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-2 text-gray-400 transition hover:text-white">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                    <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="10" r="2.5"/>
                </svg>
                <span>Open in Google Maps</span>
            </a>
=======
>>>>>>> f3ac0bb2f8c156e46e87a9aef60a47e16a08f462
        </div>
        <div>
            <h2 class="font-bold text-white">Email</h2>
            <a href="mailto:admin@davaobosscomputer.com" class="mt-2 inline-block text-gray-400 transition hover:text-white">admin@davaobosscomputer.com</a>
<<<<<<< HEAD
            <a href="https://www.facebook.com/computerboss.ph" target="_blank" rel="noopener noreferrer" class="mt-3 flex w-fit items-center gap-2 text-gray-400 transition hover:text-white">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                    <path d="M13.5 22v-8h2.7l.4-3.2h-3.1V7.2c0-.9.3-1.6 1.7-1.6H17V2.7c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.4-4 4.2v4H8v3.2h2.6v8h2.9Z"/>
                </svg>
                <span>Computer Boss Davao</span>
            </a>
=======
>>>>>>> f3ac0bb2f8c156e46e87a9aef60a47e16a08f462
        </div>
        <div>
            <h2 class="font-bold text-white">Contact</h2>
            <a href="tel:{{ config('store.phone') }}" class="mt-2 inline-block text-gray-400 transition hover:text-white">{{ config('store.phone') }}</a>
        </div>
<<<<<<< HEAD
        <p class="border-t border-white/10 pt-5 text-center text-xs text-gray-500 lg:col-span-3">© {{ date('Y') }} {{ config('store.name') }}. All Rights Reserved.</p>
=======
        <p class="border-t border-white/10 pt-5 text-xs text-gray-500 lg:col-span-3">© {{ date('Y') }} {{ config('store.name') }}. All Rights Reserved.</p>
>>>>>>> f3ac0bb2f8c156e46e87a9aef60a47e16a08f462
    </div>
</footer>
