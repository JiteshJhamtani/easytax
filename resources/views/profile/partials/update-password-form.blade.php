<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div class="form-group mb-3">
            <label class="et-form-label" for="update_password_current_password">
                Current Password <span class="text-danger">*</span>
            </label>
            <div class="et-input-group">
                <span class="et-input-icon">
                    <i class="fas fa-lock"></i>
                </span>
                <input id="update_password_current_password" name="current_password" type="password"
                    class="et-input @error('current_password', 'updatePassword') is-invalid @enderror"
                    autocomplete="current-password" placeholder="••••••••" required />
                <button type="button" class="et-password-toggle" onclick="togglePasswordVisibility('update_password_current_password', this)">
                    <i class="fas fa-eye text-muted"></i>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <span class="et-error-msg"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
            @enderror
        </div>

        {{-- New Password --}}
        <div class="form-group mb-3">
            <label class="et-form-label" for="update_password_password">
                New Password <span class="text-danger">*</span>
            </label>
            <div class="et-input-group">
                <span class="et-input-icon">
                    <i class="fas fa-key"></i>
                </span>
                <input id="update_password_password" name="password" type="password"
                    class="et-input @error('password', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password" placeholder="At least 8 characters" required />
                <button type="button" class="et-password-toggle" onclick="togglePasswordVisibility('update_password_password', this)">
                    <i class="fas fa-eye text-muted"></i>
                </button>
            </div>
            @error('password', 'updatePassword')
                <span class="et-error-msg"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group mb-4">
            <label class="et-form-label" for="update_password_password_confirmation">
                Confirm New Password <span class="text-danger">*</span>
            </label>
            <div class="et-input-group">
                <span class="et-input-icon">
                    <i class="fas fa-shield-alt"></i>
                </span>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="et-input @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password" placeholder="Repeat new password" required />
                <button type="button" class="et-password-toggle" onclick="togglePasswordVisibility('update_password_password_confirmation', this)">
                    <i class="fas fa-eye text-muted"></i>
                </button>
            </div>
            @error('password_confirmation', 'updatePassword')
                <span class="et-error-msg"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
            @enderror
        </div>

        {{-- Submit & Feedback --}}
        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
            <div>
                @if (session('status') === 'password-updated')
                    <span class="et-status-pill text-success font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Password changed securely!
                    </span>
                @endif
            </div>

            <button type="submit" class="et-btn et-btn-slate">
                <i class="fas fa-lock mr-1"></i> Update Password
            </button>
        </div>
    </form>
</section>
