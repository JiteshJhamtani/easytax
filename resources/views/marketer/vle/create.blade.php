@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.marketer')
@section('title', 'Add VLE Customer | EasyTax')

@section('content')
<div class="chq-wrapper p-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('marketer.dashboard') }}" class="text-muted font-weight-bold mr-3"><i class="fas fa-arrow-left"></i> Back</a>
        <h3 class="mb-0 font-weight-bold" style="color: #10b981;"><i class="fas fa-handshake mr-2"></i> Add VLE Customer</h3>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm rounded-lg mb-4">
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-lg" style="max-width: 800px;">
        <div class="card-body p-4">
            <form action="{{ route('crm.leads.vle.store') }}" method="POST">
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
                    
                    <div class="col-md-12 form-group mb-3">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">Total Leads Provided (Min 10) *</label>
                        <select name="amount" class="form-control rounded-lg" required>
                            <option value="" disabled selected>Select number of leads...</option>
                            @for ($i = 10; $i <= 20; $i++)
                                <option value="{{ $i }}">{{ $i }} Leads</option>
                            @endfor
                            <option value="100+">More than 100 Leads</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 form-group mb-4">
                        <label class="text-xs font-weight-bold text-muted text-uppercase">VLE Notes</label>
                        <textarea name="notes" class="form-control rounded-lg" rows="3" placeholder="Enter specific details regarding this VLE customer..."></textarea>
                    </div>
                </div>
                
                <hr class="border-light mt-0 mb-4">
                <button type="submit" class="btn text-white font-weight-bold shadow-sm px-4" style="background: #10b981;">Save VLE Customer</button>
            </form>
        </div>
    </div>
</div>
@endsection