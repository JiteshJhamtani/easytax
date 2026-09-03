<section>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        {{-- Preserve required user name and email so validation passes if only updating preference --}}
        <input type="hidden" name="name" value="{{ $user->name }}" />
        <input type="hidden" name="email" value="{{ $user->email }}" />

        @php
            $currentPref = old('notification_preference', $user->notification_preference?->value ?? 'ON');
        @endphp

        <div class="et-radio-cards space-y-3">
            {{-- Option 1: ON --}}
            <label class="et-radio-card {{ $currentPref === 'ON' ? 'active' : '' }}">
                <div class="d-flex align-items-start gap-3">
                    <input type="radio" name="notification_preference" value="ON" class="mt-1"
                        {{ $currentPref === 'ON' ? 'checked' : '' }} onchange="updateRadioCardStyles(this)">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="font-weight-bold text-dark">
                                <i class="fas fa-bell text-success mr-1"></i> Full Alerts (Email & Dashboard)
                            </span>
                            <span class="badge badge-success-soft font-weight-bold">Recommended</span>
                        </div>
                        <p class="text-muted small mb-0 mt-1">
                            Receive real-time email notifications and dashboard updates whenever a client submits an application or payment.
                        </p>
                    </div>
                </div>
            </label>

            {{-- Option 2: SILENT --}}
            <label class="et-radio-card {{ $currentPref === 'SILENT' ? 'active' : '' }}">
                <div class="d-flex align-items-start gap-3">
                    <input type="radio" name="notification_preference" value="SILENT" class="mt-1"
                        {{ $currentPref === 'SILENT' ? 'checked' : '' }} onchange="updateRadioCardStyles(this)">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="font-weight-bold text-dark">
                                <i class="fas fa-comment-alt text-info mr-1"></i> Silent Mode (Dashboard Only)
                            </span>
                            <span class="badge badge-info-soft font-weight-bold">In-App</span>
                        </div>
                        <p class="text-muted small mb-0 mt-1">
                            Save all notifications directly in your dashboard activity log without sending emails to your inbox.
                        </p>
                    </div>
                </div>
            </label>

            {{-- Option 3: OFF --}}
            <label class="et-radio-card {{ $currentPref === 'OFF' ? 'active' : '' }}">
                <div class="d-flex align-items-start gap-3">
                    <input type="radio" name="notification_preference" value="OFF" class="mt-1"
                        {{ $currentPref === 'OFF' ? 'checked' : '' }} onchange="updateRadioCardStyles(this)">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="font-weight-bold text-dark">
                                <i class="fas fa-bell-slash text-danger mr-1"></i> Disabled
                            </span>
                            <span class="badge badge-danger-soft font-weight-bold">Muted</span>
                        </div>
                        <p class="text-muted small mb-0 mt-1">
                            Completely disable automated alerts for incoming submissions and payments.
                        </p>
                    </div>
                </div>
            </label>
        </div>

        @error('notification_preference')
            <span class="et-error-msg"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
        @enderror

        <div class="d-flex align-items-center justify-content-end pt-3 border-top">
            <button type="submit" class="et-btn et-btn-primary">
                <i class="fas fa-check mr-1"></i> Update Notification Settings
            </button>
        </div>
    </form>
</section>
