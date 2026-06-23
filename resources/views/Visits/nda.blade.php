<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📋 {{ __('NDA and house rules') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Waarschuwing -->
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-r">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-medium text-red-700 dark:text-red-400">⚠️ {{ __('Required!') }}</p>
                                <p class="text-sm text-red-600 dark:text-red-300">{{ __('You must agree to the NDA and house rules to continue your visit.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bezoek Info -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">📋 {{ __('Your visit details') }}</h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Visitor') }}:</span>
                                <span class="font-medium">{{ $visit->visitor->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Company') }}:</span>
                                <span class="font-medium">{{ $visit->visitor->company_name ?? __('Not provided') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Host') }}:</span>
                                <span class="font-medium">{{ $visit->employee->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Date/time') }}:</span>
                                <span class="font-medium">{{ $visit->expected_arrival_time->format('d-m-Y H:i') }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('Reason') }}:</span>
                                <span class="font-medium">{{ $visit->reason_of_visit }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- NDA Tekst -->
                    <div class="mb-6">
                        <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-3">📜 {{ __('NDA and house rules') }}</h3>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 max-h-[400px] overflow-y-auto">
                            <div class="space-y-4 text-sm">
                                <div class="pb-3 border-b border-gray-200 dark:border-gray-600">
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200">1. {{ __('Confidentiality (NDA)') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                                        {{ __('All information, documents, conversations, and materials you obtain during this visit are strictly confidential. You may not share this information with third parties without written permission. This confidentiality obligation remains in force after your visit.') }}
                                    </p>
                                </div>

                                <div class="pb-3 border-b border-gray-200 dark:border-gray-600">
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200">2. {{ __('Safety') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                                        {{ __('Always follow staff instructions. Stay in the designated areas and carry a valid form of identification. In an emergency, follow the evacuation instructions and report to the assembly point.') }}
                                    </p>
                                </div>

                                <div class="pb-3 border-b border-gray-200 dark:border-gray-600">
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200">3. {{ __('Photography and recordings') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                                        {{ __('It is not allowed to take photos, videos, or audio recordings without explicit permission from the host. This applies to the building, equipment, and the content of conversations.') }}
                                    </p>
                                </div>

                                <div class="pb-3 border-b border-gray-200 dark:border-gray-600">
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200">4. {{ __('Code of conduct') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                                        {{ __('Behave respectfully and professionally. Smoking is not allowed in the building. Mobile phone use is restricted to designated areas. Take other attendees and business operations into account.') }}
                                    </p>
                                </div>

                                <div class="pb-3 border-b border-gray-200 dark:border-gray-600">
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200">5. {{ __('Data protection (GDPR)') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                                        {{ __('Your personal data is processed in accordance with the General Data Protection Regulation (GDPR). You have the right to inspect, correct, or request deletion of your data. Your data is not stored longer than necessary.') }}
                                    </p>
                                </div>

                                <div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200">6. {{ __('Liability') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed mt-1">
                                        {{ __('You are responsible for complying with these rules. In case of violation, measures may be taken, including ending the visit and taking legal action. The organization is not liable for damage caused by violating these rules.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulier -->
                    <form method="POST" action="{{ route('visitor.nda.accept', $visit) }}" id="ndaForm">
                        @csrf

                        <div id="nda-translations"
                            data-ready-message="{{ __('You can now agree') }}"
                            data-confirm-message="{{ __('Are you sure you want to agree to the NDA and house rules?') }}"
                            class="hidden"></div>
                        
                        <!-- Timer -->
                        <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                    📖 {{ __('Please read the terms carefully.') }}
                                </p>
                                <span id="timerDisplay" class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                    {{ __('Wait') }} <span id="countdown" class="font-bold text-lg">10</span>s
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mt-2">
                                <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-1000" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Checkbox -->
                        <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="agreed_to_rules" 
                                       id="ndaCheckbox"
                                       value="1"
                                       class="mt-1 rounded border-gray-300 dark:border-gray-600 text-red-600 shadow-sm focus:ring-red-500"
                                       required
                                       disabled>
                                <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ __('I confirm that I have fully read and understood the NDA and house rules') }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ __('I agree to all of the above terms. This agreement is recorded with date, time, and IP address (:ip).', ['ip' => request()->ip()]) }}
                                    </p>
                                    <p class="text-xs text-red-500 dark:text-red-400 mt-1">
                                        ⚠️ {{ __('Without agreement, you cannot continue your visit.') }}
                                    </p>
                                </div>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('visits.myvisits') }}" 
                               class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                ← {{ __('Back to my visits') }}
                            </a>
                            <button type="submit" 
                                    id="ndaSubmitBtn"
                                    disabled
                                    class="px-8 py-3 bg-gray-400 dark:bg-gray-600 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors">
                                <span id="btnText">✅ {{ __('I agree') }}</span>
                                <span id="btnSpinner" class="hidden">⏳ {{ __('Processing...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts - direct in de pagina -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('NDA page loaded!'); // Debug
            const ndaTranslations = document.getElementById('nda-translations');
            const checkbox = document.getElementById('ndaCheckbox');
            const submitBtn = document.getElementById('ndaSubmitBtn');
            const countdownEl = document.getElementById('countdown');
            const progressBar = document.getElementById('progressBar');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const timerDisplay = document.getElementById('timerDisplay');
            const readyMessage = ndaTranslations?.dataset.readyMessage ?? 'You can now agree';
            const confirmMessage = ndaTranslations?.dataset.confirmMessage ?? 'Are you sure you want to agree to the NDA and house rules?';
            let seconds = 10;
            const totalSeconds = 10;

            // Timer countdown
            const timer = setInterval(function() {
                seconds--;
                const progress = ((totalSeconds - seconds) / totalSeconds) * 100;
                
                if (countdownEl) {
                    countdownEl.textContent = seconds;
                }
                if (progressBar) {
                    progressBar.style.width = progress + '%';
                }

                if (seconds <= 0) {
                    clearInterval(timer);
                    if (countdownEl) {
                        countdownEl.textContent = '0';
                    }
                    if (progressBar) {
                        progressBar.style.width = '100%';
                    }
                    if (checkbox) {
                        checkbox.disabled = false;
                    }
                    if (timerDisplay) {
                        timerDisplay.innerHTML = '✅ ' + readyMessage;
                        timerDisplay.className = 'text-sm font-medium text-green-600 dark:text-green-400';
                    }
                    
                    // Flash effect
                    const yellowBox = document.querySelector('.bg-yellow-50');
                    if (yellowBox) {
                        yellowBox.classList.remove('bg-yellow-50', 'border-yellow-200');
                        yellowBox.classList.add('bg-green-50', 'border-green-200');
                    }
                }
            }, 1000);

            // Enable submit when checkbox is checked
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    if (submitBtn) {
                        submitBtn.disabled = !this.checked;
                        if (this.checked) {
                            submitBtn.classList.remove('bg-gray-400', 'dark:bg-gray-600');
                            submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        } else {
                            submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                            submitBtn.classList.add('bg-gray-400', 'dark:bg-gray-600');
                        }
                    }
                });
            }

            // Form submit protection
            const form = document.getElementById('ndaForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (submitBtn && submitBtn.disabled) {
                        e.preventDefault();
                        return;
                    }
                    
                    // Extra confirmatie
                    if (!confirm(confirmMessage)) {
                        e.preventDefault();
                        return;
                    }
                    
                    // Show spinner
                    if (submitBtn) {
                        submitBtn.disabled = true;
                    }
                    if (btnText) {
                        btnText.classList.add('hidden');
                    }
                    if (btnSpinner) {
                        btnSpinner.classList.remove('hidden');
                    }
                });
            }

            // Warn when leaving page without accepting
            window.addEventListener('beforeunload', function(e) {
                if (checkbox && !checkbox.checked) {
                    e.preventDefault();
                    e.returnValue = 'Je hebt nog geen akkoord gegeven op de NDA. Weet je zeker dat je wilt vertrekken?';
                }
            });
        });
    </script>
</x-layouts.app>