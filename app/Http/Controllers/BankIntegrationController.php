<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankLeadRequest;
use App\Models\Application;
use App\Services\BankIntegrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class BankIntegrationController extends Controller
{
    /**
     * Store the bank integration lead generation request.
     */
    public function store(StoreBankLeadRequest $request, BankIntegrationService $bankIntegrationService): RedirectResponse
    {
        $validated = $request->validated();

        // STEP 1: SECURITY GATE
        // Fetch the Application ensuring the ID matches AND the agent_id exactly matches the auth user.
        // firstOrFail() throws a 404 (ModelNotFoundException) if a rogue agent tries to spoof an ID.
        $application = Application::where('id', $validated['application_id'])
            ->where('agent_id', auth()->id())
            ->firstOrFail();

        try {
            // STEP 2 & 3: Delegate to the Service class for secure payload transformation and API dispatch
            $bankTrackingId = $bankIntegrationService->generateLead($application, $validated['bank_name']);

            // STEP 4: Database Update & UI Response
            if ($bankTrackingId) {
                // Update the application record by saving the bank's tracking ID
                $application->bank_lead_reference = $bankTrackingId;
                $application->save();

                return redirect()->back()->with('success', "Lead successfully securely transmitted to {$validated['bank_name']}. Tracking ID: {$bankTrackingId}");
            }

            return redirect()->back()->with('error', 'Failed to generate lead with the bank. Please try again.');

        } catch (\Exception $e) {
            // Log the error for internal monitoring, without exposing PII to the frontend
            Log::error('Bank Integration Error: '.$e->getMessage(), [
                'application_id' => $application->id,
                'bank_name' => $validated['bank_name'],
                'agent_id' => auth()->id(),
            ]);

            return redirect()->back()->with('error', 'A secure communication error occurred while contacting the bank.');
        }
    }
}
