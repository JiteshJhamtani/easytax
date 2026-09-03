<section>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        {{-- Row 1: Name & Email --}}
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label class="et-form-label" for="name">
                    Full Name <span class="text-danger">*</span>
                </label>
                <div class="et-input-group">
                    <span class="et-input-icon">
                        <i class="fas fa-user"></i>
                    </span>
                    <input id="name" name="name" type="text"
                        class="et-input @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}" required autocomplete="name"
                        placeholder="e.g. Rahul Sharma" />
                </div>
                @error('name')
                    <span class="et-error-msg"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6 form-group mb-3">
                <label class="et-form-label" for="email">
                    Email Address <span class="text-danger">*</span>
                </label>
                <div class="et-input-group">
                    <span class="et-input-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input id="email" name="email" type="email"
                        class="et-input @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}" required autocomplete="username"
                        placeholder="you@example.com" />
                </div>
                @error('email')
                    <span class="et-error-msg"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 p-2 rounded bg-light border text-xs text-muted">
                        <i class="fas fa-info-circle text-warning mr-1"></i> Email is unverified.
                    </div>
                @endif
            </div>
        </div>

        {{-- Row 2: Mobile & WhatsApp --}}
        <div class="row">
            <div class="col-md-6 form-group mb-3">
                <label class="et-form-label" for="mobile_number">
                    Mobile Number
                </label>
                <div class="et-input-group">
                    <span class="et-input-icon">
                        <i class="fas fa-phone"></i>
                    </span>
                    <input id="mobile_number" name="mobile_number" type="tel"
                        class="et-input @error('mobile_number') is-invalid @enderror"
                        value="{{ old('mobile_number', $user->mobile_number) }}"
                        placeholder="e.g. +91 98765 43210" />
                </div>
                @error('mobile_number')
                    <span class="et-error-msg"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6 form-group mb-3">
                <label class="et-form-label" for="whatsapp_no">
                    WhatsApp Number
                </label>
                <div class="et-input-group">
                    <span class="et-input-icon text-success">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                    <input id="whatsapp_no" name="whatsapp_no" type="tel"
                        class="et-input @error('whatsapp_no') is-invalid @enderror"
                        value="{{ old('whatsapp_no', $user->whatsapp_no) }}"
                        placeholder="e.g. +91 98765 43210" />
                </div>
                @error('whatsapp_no')
                    <span class="et-error-msg"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Row 3: Address --}}
        <div class="form-group mb-4">
            <label class="et-form-label" for="address">
                Full Address / Office Location
            </label>
            <div class="et-input-group align-items-start">
                <span class="et-input-icon pt-2">
                    <i class="fas fa-map-marker-alt"></i>
                </span>
                <textarea id="address" name="address" rows="2"
                    class="et-input et-textarea @error('address') is-invalid @enderror"
                    placeholder="Enter complete street address, city, state, and pincode">{{ old('address', $user->address) }}</textarea>
            </div>
            @error('address')
                <span class="et-error-msg"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
            @enderror
        </div>

        {{-- Submit & Feedback --}}
        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
            <div>
                @if (session('status') === 'profile-updated')
                    <span class="et-status-pill text-success font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Profile updated successfully!
                    </span>
                @endif
            </div>

            <button type="submit" class="et-btn et-btn-primary">
                <i class="fas fa-save mr-1"></i> Save Changes
            </button>
        </div>
    </form>
</section>
