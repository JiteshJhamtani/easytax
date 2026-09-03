<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Application | EasyTax</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #f4f7fa; } </style>
</head>
<body class="antialiased text-slate-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-lg w-full bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-[#1e9c5d] p-6 text-center text-white">
            <h1 class="text-2xl font-bold">EasyTax</h1>
            <p class="text-sm mt-1 opacity-90">Application Tracking</p>
        </div>

        <div class="p-6 md:p-8">
            <div class="mb-6 border-b pb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-2">Status Details</h2>
                <p class="text-slate-600">Your application for <strong>{{ $application->service->name ?? 'Service' }}</strong> is currently being processed.</p>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center bg-slate-50 p-4 rounded-lg border">
                    <span class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Application ID</span>
                    <span class="font-bold text-slate-800">#{{ $application->id }}</span>
                </div>

                <div class="flex justify-between items-center bg-slate-50 p-4 rounded-lg border">
                    <span class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Current Status</span>
                    
                    @php 
                        // Safely extract the string value from the Enum to prevent the str_replace 500 Error
                        $statusStr = $application->status instanceof \App\Enums\ApplicationStatus 
                                        ? $application->status->value 
                                        : (is_string($application->status) ? $application->status : 'PENDING');
                    @endphp

                    @if($statusStr === 'COMPLETED')
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-bold rounded-full">Completed</span>
                    @elseif($statusStr === 'IN_PROGRESS' || $statusStr === 'E_FILING')
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-bold rounded-full">In Progress</span>
                    @else
                        <span class="px-3 py-1 bg-slate-200 text-slate-800 text-sm font-bold rounded-full">{{ str_replace('_', ' ', $statusStr) }}</span>
                    @endif
                </div>

                <div class="flex justify-between items-center bg-slate-50 p-4 rounded-lg border">
                    <span class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Assigned Agent</span>
                    <span class="font-bold text-slate-800">{{ $application->agent->name ?? 'EasyTax Team' }}</span>
                </div>
            </div>

            <div class="mt-8 text-center text-sm text-slate-500">
                <p>If you have any questions, please contact your agent directly.</p>
            </div>
        </div>
    </div>

</body>
</html>