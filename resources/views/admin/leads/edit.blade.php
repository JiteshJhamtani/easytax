@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.marketer')
@section('title', 'Update Lead | EasyTax')

@section('content')
<div class="chq-wrapper p-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('crm.leads.index') }}" class="text-muted font-weight-bold mr-3"><i class="fas fa-arrow-left"></i> Back</a>
        <h3 class="mb-0 font-weight-bold text-dark"><i class="fas fa-user-edit text-primary mr-2"></i> Update Lead</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success font-weight-bold rounded-lg shadow-sm border-0 mb-4">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="font-weight-bold text-dark mb-0">Status & Notes</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('crm.leads.update', $lead->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group mb-4">
                            <label class="text-xs font-weight-bold text-muted text-uppercase">Current Status</label>
                            <select name="status" class="form-control rounded-lg font-weight-bold" style="height: 48px;">
                                <option value="NEW" {{ $lead->status === 'NEW' ? 'selected' : '' }}>New</option>
                                <option value="CONTACTED" {{ $lead->status === 'CONTACTED' ? 'selected' : '' }}>Contacted</option>
                                <option value="IN_DISCUSSION" {{ $lead->status === 'IN_DISCUSSION' ? 'selected' : '' }}>In Discussion</option>
                                <option value="CONVERTED" {{ $lead->status === 'CONVERTED' ? 'selected' : '' }}>Converted to Client</option>
                                <option value="LOST" {{ $lead->status === 'LOST' ? 'selected' : '' }}>Lost / Not Interested</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-xs font-weight-bold text-muted text-uppercase">Add New Note</label>
                            <textarea name="notes" class="form-control rounded-lg" rows="4" placeholder="What happened in your latest call or email?"></textarea>
                            <small class="text-muted mt-1 d-block">This will be added to the lead's history.</small>
                        </div>

                        <button type="submit" class="btn btn-primary font-weight-bold shadow-sm px-4 py-2" style="border-radius: 8px;">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm rounded-lg mb-4" style="background: #f8fafc;">
                <div class="card-body p-4">
                    <h6 class="text-xs font-weight-bold text-muted text-uppercase mb-3">Lead Details</h6>
                    <h4 class="font-weight-bold text-dark mb-1">{{ $lead->name }}</h4>
                    <p class="text-muted mb-3"><i class="fas fa-phone-alt mr-2 text-primary"></i> {{ $lead->phone }}</p>
                    
                    @if($lead->email)
                        <p class="text-muted mb-3"><i class="fas fa-envelope mr-2 text-primary"></i> {{ $lead->email }}</p>
                    @endif

                    <div class="d-flex justify-content-between border-top pt-3 mt-3">
                        <span class="text-muted font-weight-bold text-xs text-uppercase">Interested In</span>
                        <span class="font-weight-bold text-dark">{{ $lead->service_interested ?? 'Not Specified' }}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-lg">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="text-xs font-weight-bold text-muted text-uppercase mb-0">Notes History</h6>
                </div>
                <div class="card-body p-4">
                    @if($lead->notes)
                        <div class="p-3 rounded-lg" style="background: #fdf8f6; border-left: 4px solid #f97316; white-space: pre-wrap; font-size: 0.9rem; color: #4b5563;">{{ $lead->notes }}</div>
                    @else
                        <p class="text-muted font-italic mb-0 text-sm">No notes have been added to this lead yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection