@extends('layouts.admin')

@section('title', 'Bulk Password Reset | EasyTax Admin')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .modern-card {
        border-radius: var(--radius);
        box-shadow: var(--shadow);
    }
    .check-all {
        cursor: pointer;
    }
    /* SweetAlert EasyTax Theme customization */
    div:where(.swal2-container) div:where(.swal2-popup) {
        border-radius: 16px !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        padding: 2rem !important;
    }
    .swal-confirm-btn {
        background-color: var(--green, #1E9C5D) !important;
        border-color: var(--green, #1E9C5D) !important;
        font-weight: 700 !important;
        padding: 0.6rem 1.6rem !important;
        border-radius: 50px !important;
        font-size: 0.9rem !important;
        box-shadow: 0 4px 12px rgba(30, 156, 93, 0.25) !important;
    }
    .swal-confirm-btn:hover {
        background-color: var(--green-dark, #157a48) !important;
    }
    .swal-cancel-btn {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600 !important;
        padding: 0.6rem 1.4rem !important;
        border-radius: 50px !important;
        font-size: 0.9rem !important;
    }
    .swal-cancel-btn:hover {
        background-color: #e2e8f0 !important;
        color: #1e293b !important;
    }
</style>
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <div>
        <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.5rem;">Bulk Password Reset</h1>
        <p class="text-muted mb-0 mt-1">Select agents or sub-admins below to safely update their passwords at once.</p>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i> <strong>Error:</strong> Please check the form for errors.
            <ul class="mb-0 mt-1 pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.bulk-passwords.update') }}" method="POST" id="bulkPasswordForm">
        @csrf
        <div class="card modern-card border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold text-dark mb-0">
                        <i class="fas fa-users mr-1 text-primary"></i> Available Accounts ({{ $users->count() }})
                    </h6>
                    <span id="selectedBadge" class="badge badge-light border text-muted px-3 py-2" style="font-size: 0.8rem;">
                        0 selected
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="usersTable">
                        <thead class="bg-light text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAll">
                                        <label class="custom-control-label check-all" for="selectAll"></label>
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            @php
                                $roleName = $user->roles->first()?->name ?? $user->role ?? 'User';
                                $roleLower = strtolower($roleName);
                                $badgeClass = match(true) {
                                    str_contains($roleLower, 'agent') => 'badge-primary',
                                    str_contains($roleLower, 'team') || str_contains($roleLower, 'operator') => 'badge-info',
                                    str_contains($roleLower, 'sub') => 'badge-warning text-dark',
                                    str_contains($roleLower, 'market') => 'badge-secondary',
                                    default => 'badge-dark'
                                };
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="custom-control-input user-checkbox" id="user_{{ $user->id }}">
                                        <label class="custom-control-label" style="cursor: pointer;" for="user_{{ $user->id }}"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $user->name }}</div>
                                    @if($user->agent_code)
                                        <small class="text-muted">Code: {{ $user->agent_code }}</small>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $badgeClass }} px-2 py-1 font-weight-bold">{{ strtoupper($roleName) }}</span>
                                </td>
                                <td>{{ $user->mobile_number ?? '-' }}</td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge badge-success-soft" style="background:#e6f4ea; color:#137333; padding: 4px 8px; border-radius: 4px; font-weight: 600;">Active</span>
                                    @else
                                        <span class="badge badge-danger-soft" style="background:#fce8e6; color:#c5221f; padding: 4px 8px; border-radius: 4px; font-weight: 600;">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card modern-card border-0 mb-4">
            <div class="card-body p-4 bg-light rounded-bottom">
                <h5 class="font-weight-bold text-dark mb-1"><i class="fas fa-key mr-2 text-warning"></i> Set New Password for Selected Users</h5>
                <p class="text-muted text-sm mb-3">Enter the new password below. It will be securely hashed and updated for all selected accounts.</p>
                
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="password" class="font-weight-bold text-dark">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required minlength="6" placeholder="Enter at least 6 characters">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="password_confirmation" class="font-weight-bold text-dark">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="6" placeholder="Repeat new password">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="password_confirmation">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary btn-block font-weight-bold py-2" id="submitBtn" style="background: var(--green, #1E9C5D); border-color: var(--green, #1E9C5D); border-radius: 8px;">
                                <i class="fas fa-save mr-1"></i> Update
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let table = $('#usersTable').DataTable({
        pageLength: 25,
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 5] }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search by name, email, or role..."
        }
    });

    function updateSelectedCounter() {
        var count = table.$('input.user-checkbox:checked').length;
        if (count > 0) {
            $('#selectedBadge')
                .removeClass('badge-light text-muted')
                .addClass('badge-success text-white')
                .css('background-color', '#1E9C5D')
                .text(count + ' user(s) selected');
        } else {
            $('#selectedBadge')
                .removeClass('badge-success text-white')
                .addClass('badge-light text-muted')
                .css('background-color', '')
                .text('0 selected');
        }
    }

    // Toggle password visibility
    $('.toggle-pwd').on('click', function() {
        let targetId = $(this).data('target');
        let $input = $('#' + targetId);
        let $icon = $(this).find('i');
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Handle Select All across all pages using DataTables API
    $('#selectAll').on('click', function(){
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input.user-checkbox', rows).prop('checked', this.checked);
        updateSelectedCounter();
    });

    // Update counter on individual click
    $('#usersTable').on('change', 'input.user-checkbox', function() {
        updateSelectedCounter();
    });

    // Form submission with EasyTax-styled SweetAlert2 confirmation modal
    $('#bulkPasswordForm').on('submit', function(e) {
        e.preventDefault();

        var checkedCount = table.$('input.user-checkbox:checked').length;
        if (checkedCount === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Accounts Selected',
                text: 'Please select at least one agent or sub-admin from the list before updating.',
                confirmButtonText: 'Understood',
                customClass: {
                    confirmButton: 'swal-confirm-btn'
                },
                buttonsStyling: false
            });
            return false;
        }

        var pwd = $('#password').val();
        var pwdConfirm = $('#password_confirmation').val();

        if (!pwd || pwd.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Password Too Short',
                text: 'The new password must be at least 6 characters long.',
                confirmButtonText: 'Fix Password',
                customClass: {
                    confirmButton: 'swal-confirm-btn'
                },
                buttonsStyling: false
            });
            return false;
        }

        if (pwd !== pwdConfirm) {
            Swal.fire({
                icon: 'error',
                title: 'Passwords Do Not Match',
                text: 'Please make sure the new password and confirm password fields match exactly.',
                confirmButtonText: 'Try Again',
                customClass: {
                    confirmButton: 'swal-confirm-btn'
                },
                buttonsStyling: false
            });
            return false;
        }

        // Beautiful Theme Confirmation Popup
        Swal.fire({
            title: 'Change Passwords?',
            html: `You are about to update the password for <strong style="color: #1E9C5D; font-size: 1.15rem;">${checkedCount} selected user(s)</strong>.<br><br>
                   <span class="text-muted" style="font-size: 0.88rem;">
                       <i class="fas fa-exclamation-triangle text-warning mr-1"></i> 
                       All selected accounts will immediately require this new password to log in.
                   </span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check mr-1"></i> Yes, Update Passwords',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn mr-2'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var $form = $('#bulkPasswordForm');
                
                // Clear any previously appended hidden user_ids
                $form.find('input[type="hidden"].dynamic-user-id').remove();

                // Append hidden inputs for checked rows not in current DOM page
                table.$('input.user-checkbox:checked').each(function(){
                    if(!$.contains(document, this)){
                        $form.append(
                            $('<input>')
                                .attr('type', 'hidden')
                                .attr('class', 'dynamic-user-id')
                                .attr('name', 'user_ids[]')
                                .val(this.value)
                        );
                    }
                });

                // Show loading state
                Swal.fire({
                    title: 'Updating Passwords...',
                    text: 'Please wait while the passwords are being updated securely.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Unbind submit to prevent loop and submit native form
                $form.off('submit').submit();
            }
        });
    });
});
</script>
@stop
