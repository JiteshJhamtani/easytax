@extends('layouts.admin')

@section('title', 'Team & Operators')

@section('css')
    <style>
        /* ── DESKTOP TABLE STYLES ── */
        .table-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        
        .team-table {
            border-collapse: collapse !important;
            width: 100% !important; /* Forces table to fit screen */
            margin-bottom: 0 !important;
        }
        
        .team-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #e2e8f0 !important;
            border-top: none !important;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            font-weight: 700;
            white-space: nowrap;
        }
        
        .team-table tbody td {
            white-space: normal !important; /* Allows text to wrap */
            word-break: break-word !important; 
            padding: 1rem 0.75rem !important;
            vertical-align: middle;
            font-size: 0.9rem !important;
            border-bottom: 1px solid #f1f5f9;
        }
        
        /* Protect the Action Column on Desktop */
        .team-table th:last-child,
        .team-table td:last-child {
            min-width: 160px;
            white-space: nowrap !important;
        }

        /* ==========================================================================
           🔥 MORPH TABLE INTO CARDS ON MOBILE & TABLET (Max 1024px) 🔥
           ========================================================================== */
        @media screen and (max-width: 1024px) {
            
            .table-responsive { overflow-x: visible !important; -webkit-overflow-scrolling: auto; }
            .table-card { overflow: visible !important; border: none !important; background: transparent !important; box-shadow: none !important; }
            
            .team-table, 
            .team-table tbody, 
            .team-table tr, 
            .team-table td {
                display: block !important;
                width: 100% !important;
                min-width: 0 !important; 
                white-space: normal !important; 
            }

            .team-table thead {
                display: none !important;
            }

            .team-table tbody tr {
                margin-bottom: 1.25rem !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 12px !important;
                padding: 1rem !important;
                background: #ffffff !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
            }

            .team-table tbody td {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 0.6rem 0 !important;
                border-bottom: 1px dashed #e2e8f0 !important;
                text-align: right !important; 
                border-top: none !important;
            }
            
            /* Action Buttons Row */
            .team-table tbody td:last-child {
                border-bottom: none !important;
                padding-bottom: 0 !important;
                margin-top: 0.5rem;
                justify-content: flex-end !important;
            }

            .team-table tbody td::before {
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                text-align: left;
                margin-right: 1rem;
            }

            /* --- COLUMN MAP FOR TEAM TABLE --- */
            .team-table tbody td:nth-child(1)::before { content: "ID"; }
            .team-table tbody td:nth-child(2)::before { content: "Name & Contact"; }
            .team-table tbody td:nth-child(3)::before { content: "Status"; }
            .team-table tbody td:nth-child(4)::before { content: "Joined"; }
            .team-table tbody td:nth-child(5)::before { content: "Assigned Apps"; }
            .team-table tbody td:nth-child(6)::before { content: "Actions"; display: none; }
        }
    </style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="font-weight-bold text-dark mb-0">🛡️ Internal Team & Operators</h3> 
            <p class="text-muted mb-0">Manage your internal staff who process the applications.</p>
        </div>
        <a href="{{ route('admin.team.create') }}" class="btn btn-primary font-weight-bold shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-plus mr-1"></i> Add New Operator
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm" style="border-radius: 8px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger shadow-sm" style="border-radius: 8px;">
            <ul class="mb-0">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- Added the 'team-table' class here --}}
                <table class="table table-hover mb-0 team-table">
                    <thead>
                        <tr>
                            <th class="pl-4">ID</th>
                            <th>Name & Contact</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-center">Assigned Apps</th>
                            <th class="text-right pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamMembers as $member)
                        <tr>
                            <td class="pl-4 font-weight-bold text-muted">#{{ $member->id }}</td>
                            <td>
                                {{-- Wrapped content in a div so flexbox keeps them stacked correctly --}}
                                <div>
                                    <div class="font-weight-bold text-dark">{{ $member->name }}</div>
                                    <div class="small text-muted">{{ $member->email }} <br> {{ $member->phone ?? 'No Phone' }}</div>
                                </div>
                            </td>
                            <td>
                                @if($member->is_active)
                                    <span class="badge badge-success px-2 py-1">Active</span>
                                @else
                                    <span class="badge badge-danger px-2 py-1">Suspended</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $member->created_at->format('d M Y') }}</td>

                            <td class="text-center">
                                @php $appCount = $assignedCounts[$member->id] ?? 0; @endphp
                                @if($appCount > 0)
                                    <span class="badge badge-info px-2 py-1" style="font-size: 0.85rem;">{{ $appCount }} Apps</span>
                                @else
                                    <span class="badge badge-light px-2 py-1 text-muted">0 Apps</span>
                                @endif
                            </td>
                            <td class="pr-4">
                                {{-- Wrapped all action buttons in a flex container so they stay side-by-side --}}
                                <div class="d-flex justify-content-end align-items-center">
                                    <a href="{{ route('admin.team.show', $member->id) }}" class="btn btn-sm btn-info text-white mr-2 shadow-sm font-weight-bold" title="View Profile & Financials">
                                        <i class="fas fa-user-circle mr-1"></i> Profile
                                    </a>
                                    
                                    <form action="{{ route('admin.team.toggle-status', $member->id) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $member->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} mr-1" title="{{ $member->is_active ? 'Suspend Operator' : 'Activate Operator' }}">
                                            <i class="fas {{ $member->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.team.edit', $member->id) }}" class="btn btn-sm btn-outline-primary mr-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.team.destroy', $member->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this operator?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-user-shield fa-3x mb-3 text-light"></i>
                                <h5>No Operators Found</h5>
                                <p>Click the button above to add your first team member.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection