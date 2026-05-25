@extends('layouts.front.app')

@section('title', $page->title)

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="font-weight-bold mb-4 text-dark">{{ $page->title }}</h1>
                        <hr class="mb-5">
                        <div class="page-content text-secondary" style="line-height: 1.8; font-size: 1.05rem;">
                            {{-- We output unescaped content because it's managed via TinyMCE WYSIWYG by the Admin --}}
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('styles')
<style>
    .page-content h1, .page-content h2, .page-content h3, .page-content h4, .page-content h5, .page-content h6 {
        color: #1a202c;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .page-content a {
        color: #0044b2;
        text-decoration: underline;
    }
    .page-content a:hover {
        color: #00368c;
    }
    .page-content ul, .page-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    .page-content p {
        margin-bottom: 1.5rem;
    }
</style>
@endpush

 {{-- ── RAZORPAY GATEWAY INVOCATION ── --}}
    @if (session('razorpay_order'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ORDER = @json(session('razorpay_order'));

                const options = {
                    key: ORDER.key_id,
                    amount: ORDER.amount,
                    currency: ORDER.currency,
                    order_id: ORDER.order_id,
                    name: 'EasyTax',
                    description: 'Application Processing Fee',
                    handler: function(response) {
                        // User paid successfully! Send them to the payment.success route
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('payment.success') }}';

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);

                        for (const [key, value] of Object.entries(response)) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = value;
                            form.appendChild(input);
                        }

                        document.body.appendChild(form);
                        form.submit();
                    },


                    }

                    modal: {
                        ondismiss: function() {
                            // User closed the Razorpay window without paying
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route('payment.failure') }}';

                            const csrf = document.createElement('input');
                            csrf.type = 'hidden';
                            csrf.name = '_token';
                            csrf.value = '{{ csrf_token() }}';
                            form.appendChild(csrf);

                            const orderInput = document.createElement('input');
                            orderInput.type = 'hidden';
                            orderInput.name = 'razorpay_order_id';
                            orderInput.value = ORDER.order_id;
                            form.appendChild(orderInput);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    },
                    theme: { color: '#1E9C5D' }
                };

                const rzp = new Razorpay(options);
                
                // Add a tiny delay so the page can finish rendering before the popup takes over
                setTimeout(() => { rzp.open(); }, 300);
            });
        </script>
    @endif
