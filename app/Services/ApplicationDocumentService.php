<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationDocumentService
{
    public function handleUploads(
        Application $application,
        Request $request,
        string $serviceSlug
    ): void {

        $schema = config("service_forms.$serviceSlug.documents");

        if (! $schema) {
            return;
        }

        foreach ($schema as $document) {
            $fieldName = $document['name'];
            $file = null;

            // SMART DETECTOR: Check both common HTML form submission formats
            if ($request->hasFile("documents.$fieldName")) {
                $file = $request->file("documents.$fieldName");
            } elseif ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
            }

            if ($file) {
                // Handle case where a single field might allow multiple files
                $filesToUpload = is_array($file) ? $file : [$file];

                foreach ($filesToUpload as $f) {
                    $application
                        ->addMedia($f)
                        ->withCustomProperties([
                            'field_name' => $fieldName,
                            'label' => $document['label'],
                        ])
                        // Save it to a bucket named after the field (e.g., 'partner_pan_1')
                        // And explicitly force it to the 'private' security disk!
                        ->toMediaCollection($fieldName, 'private');
                }

                activity('application')
                    ->performedOn($application)
                    ->log("Uploaded document: {$document['label']}");
            }
        }
    }
}
