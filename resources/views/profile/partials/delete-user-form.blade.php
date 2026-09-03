<section class="space-y-4">
    <div class="p-3 rounded-lg border border-red-200 bg-red-50/50 mb-3">
        <div class="d-flex align-items-start gap-2">
            <i class="fas fa-exclamation-triangle text-danger mt-1"></i>
            <div>
                <span class="font-weight-bold text-danger d-block">Warning: Permanent Action</span>
                <p class="text-muted small mb-0 mt-1">
                    Once your account is deleted, your access will be revoked immediately and your profile marked as inactive.
                </p>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center pt-2">
        <span class="text-xs text-muted">Requires password confirmation</span>
        <button type="button" class="et-btn et-btn-danger" data-toggle="modal" data-target="#deleteAccountModal"
            onclick="openDeleteModal()">
            <i class="fas fa-trash-alt mr-1"></i> Delete Account
        </button>
    </div>

    {{-- Confirmation Modal (Bootstrap 4 & Alpine Compatible) --}}
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg rounded-2xl overflow-hidden">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-header bg-red-50 border-bottom border-red-100 py-3 px-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-red-100 text-danger d-flex align-items-center justify-content-center">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h5 class="modal-title font-weight-bold text-danger mb-0" id="deleteModalTitle">
                                Confirm Account Deletion
                            </h5>
                        </div>
                        <button type="button" class="close text-muted" data-dismiss="modal" aria-label="Close" onclick="closeDeleteModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        <p class="text-secondary mb-3">
                            Are you sure you want to delete your account? All your personal data and active sessions will be permanently terminated.
                        </p>
                        <p class="text-sm font-weight-bold text-dark mb-2">
                            Please enter your current password to authorize this action:
                        </p>

                        <div class="et-input-group mb-2">
                            <span class="et-input-icon">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input id="delete_password" name="password" type="password"
                                class="et-input @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="Enter your current password" required />
                        </div>

                        @error('password', 'userDeletion')
                            <span class="et-error-msg d-block mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary font-weight-bold rounded-lg px-3" data-dismiss="modal" onclick="closeDeleteModal()">
                            Cancel
                        </button>
                        <button type="submit" class="et-btn et-btn-danger">
                            <i class="fas fa-trash-alt mr-1"></i> Yes, Delete My Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@if ($errors->userDeletion->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            openDeleteModal();
        });
    </script>
@endif
