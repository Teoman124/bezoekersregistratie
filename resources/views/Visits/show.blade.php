<x-layouts.app>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Visit details') }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('View all information for this visit.') }}</p>
            </div>
            @if(in_array(auth()->user()?->role, ['admin', 'employee'], true))
            <a href="{{ route('visits.edit', $visit) }}" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white">{{ __('Edit') }}</a>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Visitor') }}</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $visit->visitor?->user?->name ?? __('Unknown') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Employee') }}</p>
                <p class="text-lg font-medium text-gray-800 dark:text-gray-100">{{ $visit->employee?->user?->name ?? __('Unknown') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Reason') }}</p>
                <p class="text-gray-800 dark:text-gray-100">{{ $visit->reason_of_visit ?: __('No reason provided') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Expected arrival') }}</p>
                    <p class="text-gray-800 dark:text-gray-100">{{ $visit->expected_arrival_time?->format('d-m-Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Expected departure') }}</p>
                    <p class="text-gray-800 dark:text-gray-100">{{ $visit->expected_departure_time?->format('d-m-Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Check-in time') }}</p>
                    <p class="text-gray-800 dark:text-gray-100">{{ $visit->check_in_time?->format('d-m-Y H:i') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Check-out time') }}</p>
                    <p class="text-gray-800 dark:text-gray-100">{{ $visit->check_out_time?->format('d-m-Y H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>

        @if(! $visit->check_in_time && isset($checkinQrUrl))
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 shadow-sm printable-badge" data-qr-url="{{ $checkinQrUrl }}">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ __('Check-in badge') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Scan this QR code to quickly check in this visit, or print the badge.') }}</p>
                </div>
                <button type="button" onclick="window.printBadge()"
                    class="px-4 py-2 rounded-md bg-slate-600 hover:bg-slate-700 text-white text-sm">{{ __('Print') }}</button>
            </div>

            <div class="grid gap-4 lg:grid-cols-[auto_1fr] items-center">
                <div class="bg-white p-4 rounded-lg border border-gray-200 dark:border-gray-700 mx-auto">
                    <img id="qr-image" src="" alt="QR-code check-in badge" class="w-80 h-80 block mx-auto rounded-lg" />
                </div>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Check-in link') }}</p>
                        <p class="text-xs break-words text-gray-700 dark:text-gray-200">{{ $checkinQrUrl }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-4 border border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Visitor') }}</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $visit->visitor?->user?->name ?? __('Unknown') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Employee') }}</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $visit->employee?->user?->name ?? __('Unknown') }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ __('Arrival') }}</p>
                        <p class="text-gray-800 dark:text-gray-100">{{ $visit->expected_arrival_time?->format('d-m-Y H:i') ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(! $visit->check_in_time && isset($checkinQrUrl))
        <style>
            @media print {
                body * {
                    visibility: hidden !important;
                }

                .printable-badge,
                .printable-badge * {
                    visibility: visible !important;
                }

                .printable-badge {
                    position: fixed !important;
                    left: 0;
                    top: 0;
                    width: 100%;
                    padding: 1rem;
                }

                .printable-badge img {
                    width: 320px !important;
                    height: 320px !important;
                }
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const badgeContainer = document.querySelector('[data-qr-url]');
                if (!badgeContainer) return;

                const qrUrl = badgeContainer.dataset.qrUrl;
                const qrImg = document.getElementById('qr-image');

                if (qrImg && window.QRCode) {
                    window.QRCode.toDataURL(qrUrl, {
                        width: 320,
                        margin: 1
                    }, function(err, dataUrl) {
                        if (err) {
                            console.error('QR generation error', err);
                            return;
                        }
                        qrImg.src = dataUrl;
                    });
                }

                window.printBadge = function() {
                    const img = document.getElementById('qr-image');
                    const doPrint = () => window.print();

                    if (!img) return doPrint();
                    if (img.src && img.complete) return doPrint();

                    const onLoad = () => {
                        img.removeEventListener('load', onLoad);
                        doPrint();
                    };
                    img.addEventListener('load', onLoad);
                    setTimeout(() => {
                        img.removeEventListener('load', onLoad);
                        doPrint();
                    }, 3000);
                };
            });
        </script>
        @endif

        <div class="flex gap-3">
            @if(in_array(auth()->user()?->role, ['admin'], true))
            <a href="{{ route('visits.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">{{ __('Back') }}</a>
            @endif
            @if(in_array(auth()->user()?->role, ['visitor', 'employee'], true))
            <a href="{{ route('visits.myvisits') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700">{{ __('Back') }}</a>
            @endif
            @if(in_array(auth()->user()?->role, ['admin', 'employee'], true))
            @if(!$visit->check_in_time)
            <form action="{{ route('visits.checkin', $visit) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-md bg-green-600 hover:bg-green-700 text-white">{{ __('Check in') }}</button>
            </form>
            @endif
            @if($visit->check_in_time && !$visit->check_out_time)
            <a href="{{ route('visits.checkout', $visit) }}" class="px-4 py-2 rounded-md bg-amber-600 hover:bg-amber-700 text-white">{{ __('Check out') }}</a>
            @endif
            @endif
        </div>
    </div>
</x-layouts.app>