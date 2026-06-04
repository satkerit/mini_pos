<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-rose-500 to-rose-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-lg shadow-rose-200 hover:shadow-xl hover:shadow-rose-300 hover:from-rose-600 hover:to-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition-all duration-200 active:scale-[0.98]']) }}>
    {{ $slot }}
</button>
