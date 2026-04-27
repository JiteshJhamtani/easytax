@extends(auth()->user()->role === 'ADMIN' ? 'layouts.admin' : 'layouts.marketer')
@section('title', 'Add Lead | EasyTax')

@section('content')
<div class="chq-wrapper p-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('crm.leads.index') }}" class="text-muted font-weight-bold mr-3"><i class="fas fa-arrow-left"></i> Back</a>
        <h3 class="mb-0 font-weight-bold" style="color: #8b5cf6;"><i class="fas fa-magnet mr-2"></i> Capture New Lead</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-lg" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('crm.leads.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Full Name *</label>
                        <input type="text" name="name" class="form-control rounded-lg" required>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Phone Number *</label>
                        <input type="text" name="phone" class="form-control rounded-lg" required>
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Email Address</label>
                        <input type="email" name="email" class="form-control rounded-lg">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Service Interest</label>
                        <select name="service_interested" class="form-control rounded-lg">
                            <option value="">Unknown / General</option>
                            <option value="ITR Filing">ITR Filing</option>
                            <option value="GST Registration">GST Registration</option>
                            <option value="GST Return">GST Return</option>
                            <option value="Company Formation">Company Formation</option>
                        </select>
                    </div>
                    
                    @if(strtoupper(auth()->user()->role) === 'ADMIN')
                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Assign to Marketer</label>
                        <select name="marketer_id" class="form-control rounded-lg">
                            <option value="">None (Direct Lead)</option>
                            @foreach($marketers as $marketer)
                                <option value="{{ $marketer->id }}">{{ $marketer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-6 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Lead Source</label>
                        <input type="text" name="source" class="form-control rounded-lg" placeholder="e.g. Facebook Ads, WhatsApp">
                    </div>
                    
                    <div class="col-md-12 form-group mb-4">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Initial Notes</label>
                        <textarea name="notes" class="form-control rounded-lg" rows="3" placeholder="Enter any initial details here..."></textarea>
                    </div>
                </div>
                
                <hr class="border-light mt-0 mb-4">
                <button type="submit" class="btn text-white font-weight-bold shadow-sm px-4" style="background: #8b5cf6;">Save Lead</button>
            </form>
        </div>
    </div>
</div>
@endsection