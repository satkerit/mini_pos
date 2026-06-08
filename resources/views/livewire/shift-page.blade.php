<div class="h-screen flex flex-col bg-gradient-to-br from-slate-50 via-indigo-50/30 to-cyan-50/20">
    {{-- Header --}}
    <header class="bg-white/80 backdrop-blur-xl border-b border-slate-200/60 px-4 py-3 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center space-x-3">
            <a href="/pos" class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center hover:bg-slate-200 transition-all">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="font-bold text-slate-800 text-lg leading-tight">{{ __('Shift Management') }}</h1>
                <p class="text-xs text-slate-400">{{ __('Start & End of Day') }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button wire:click="changeLocale('en')" class="px-2 py-1 rounded-lg text-xs font-medium {{ $locale === 'en' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-400 hover:bg-slate-100' }}">EN</button>
            <button wire:click="changeLocale('id')" class="px-2 py-1 rounded-lg text-xs font-medium {{ $locale === 'id' ? 'bg-indigo-100 text-indigo-700' : 'text-slate-400 hover:bg-slate-100' }}">ID</button>
            <button wire:click="logout" class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 hover:bg-rose-100 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </button>
        </div>
    </header>

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto scrollbar-thin p-4 space-y-4">
        {{-- No Open Shift --}}
        @if(!$currentShift)
        <div class="max-w-md mx-auto space-y-6 pt-8">
            <div class="text-center space-y-3">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-indigo-400 to-cyan-500 rounded-3xl shadow-lg shadow-indigo-200 flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800">{{ __('No Open Shift') }}</h2>
                <p class="text-sm text-slate-500">{{ __('Start your shift to begin transactions') }}</p>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 p-6 space-y-4">
                <div class="flex items-center space-x-2 text-sm font-medium text-slate-700">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>{{ __('Opening Balance (Saldo Awal)') }}</span>
                </div>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-semibold">Rp</span>
                    <input type="number" wire:model="openingBalance"
                           class="w-full pl-14 pr-4 py-4 text-xl font-bold text-slate-800 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-indigo-500 focus:ring-0 focus:outline-none transition-all text-right"
                           placeholder="0" min="0" step="1000">
                </div>
                <button wire:click="openShift" class="w-full py-4 bg-gradient-to-r from-indigo-500 to-cyan-500 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ __('Open Shift') }}</span>
                </button>
            </div>
        </div>

        {{-- Open Shift Active --}}
        @else
        <div class="space-y-4">
            {{-- Shift Status Card --}}
            <div class="bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-3xl p-5 text-white shadow-lg shadow-indigo-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse"></div>
                        <span class="font-bold text-lg">{{ __('Shift Active') }}</span>
                    </div>
                    <span class="text-xs bg-white/20 px-3 py-1 rounded-full">{{ $currentShift->opened_at?->format('H:i') }}</span>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white/10 rounded-2xl p-3 text-center">
                        <div class="text-xs opacity-80">{{ __('Opening') }}</div>
                        <div class="font-bold text-lg">Rp {{ number_format($currentShift->opening_balance, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-3 text-center">
                        <div class="text-xs opacity-80">{{ __('Transactions') }}</div>
                        <div class="font-bold text-lg">{{ $todaySummary['transaction_count'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-3 text-center">
                        <div class="text-xs opacity-80">{{ __('Total Sales') }}</div>
                        <div class="font-bold text-lg">Rp {{ number_format($todaySummary['total_sales'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-xs font-medium text-slate-500">{{ __('Cash Sales') }}</span>
                    </div>
                    <div class="text-lg font-bold text-emerald-600">Rp {{ number_format($todaySummary['cash_sales'] ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <span class="text-xs font-medium text-slate-500">{{ __('Non-Cash Sales') }}</span>
                    </div>
                    <div class="text-lg font-bold text-sky-600">Rp {{ number_format($todaySummary['non_cash_sales'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Today's Transactions --}}
            <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800">{{ __('Today\'s Transactions') }}</h3>
                </div>
                @if(empty($todaySales))
                <div class="p-8 text-center">
                    <div class="w-16 h-16 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p class="text-sm text-slate-400">{{ __('No transactions yet') }}</p>
                </div>
                @else
                <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto scrollbar-thin">
                    @foreach($todaySales as $sale)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-cyan-100 flex items-center justify-center">
                                <span class="text-xs font-bold text-indigo-600">#{{ substr($sale['order_number'], -4) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-700">{{ $sale['order_number'] }}</div>
                                <div class="text-xs text-slate-400">{{ $sale['customer_name'] ?? __('Walk-in') }} &middot; {{ \Carbon\Carbon::parse($sale['created_at'])->format('H:i') }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-slate-800">Rp {{ number_format($sale['final_amount'], 0, ',', '.') }}</div>
                            <span class="badge @if($sale['payment_method'] === 'cash') bg-emerald-100 text-emerald-700 @else bg-sky-100 text-sky-700 @endif text-[10px] px-2 py-0.5 rounded-full font-medium">
                                {{ strtoupper($sale['payment_method']) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Close Shift Section --}}
            <div class="bg-white rounded-3xl border border-rose-200/60 shadow-sm p-5 space-y-4">
                <div class="flex items-center space-x-2 text-sm font-medium text-rose-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>{{ __('Close Shift (Akhir Hari)') }}</span>
                </div>

                <div class="bg-amber-50 rounded-2xl p-3 border border-amber-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-amber-700">{{ __('Expected Cash in Drawer') }}</span>
                        <span class="font-bold text-amber-800">Rp {{ number_format($todaySummary['expected_cash'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-xs text-amber-600 mt-1">{{ __('Opening: :amount + Cash Sales: :cash', ['amount' => number_format($todaySummary['opening_balance'] ?? 0, 0, ',', '.'), 'cash' => number_format($todaySummary['cash_sales'] ?? 0, 0, ',', '.')]) }}</div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-medium text-slate-500">{{ __('Actual Cash Counted') }}</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-semibold">Rp</span>
                        <input type="number" wire:model="actualCash"
                               class="w-full pl-14 pr-4 py-3 text-lg font-bold text-slate-800 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-rose-500 focus:ring-0 focus:outline-none transition-all text-right"
                               placeholder="0" min="0" step="1000">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-medium text-slate-500">{{ __('Notes (Optional)') }}</label>
                    <textarea wire:model="notes" rows="2"
                              class="w-full px-4 py-3 text-sm text-slate-700 bg-slate-50 border-2 border-slate-200 rounded-2xl focus:border-rose-500 focus:ring-0 focus:outline-none transition-all resize-none"
                              placeholder="{{ __('Any notes for this shift...') }}"></textarea>
                </div>

                <button wire:click="closeShift" onclick="return confirm('{{ __('Are you sure you want to close this shift? This action cannot be undone.') }}')"
                        class="w-full py-4 bg-gradient-to-r from-rose-500 to-orange-500 text-white font-bold rounded-2xl shadow-lg shadow-rose-200 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all">
                    {{ __('Close Shift & Reconcile') }}
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
