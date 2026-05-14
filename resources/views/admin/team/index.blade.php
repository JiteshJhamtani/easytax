@extends('layouts.admin')

@section('title', 'Team & Operators')

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

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="bg-light text-uppercase text-muted" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                        <tr>
                            <th class="border-0 pl-4">ID</th>
                            <th class="border-0">Name & Contact</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Joined</th>
                            
                            <th class="border-0 text-center">Assigned Apps</th>
                            
                            <th class="border-0 text-right pr-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamMembers as $member)
                        <tr>
                            <td class="pl-4 font-weight-bold text-muted">#{{ $member->id }}</td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $member->name }}</div>
                                <div class="small text-muted">{{ $member->email }} <br> {{ $member->phone ?? 'No Phone' }}</div>
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
                            <td class="text-right pr-4">
                                
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

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
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