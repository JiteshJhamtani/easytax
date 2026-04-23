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

        if (!$schema) {
            return;
        }

        foreach ($schema as $document) {

            $fieldName = $document['name'];

            if ($request->hasFile($fieldName)) {

                $file = $request->file($fieldName);

                $application
                    ->addMedia($file)
                    ->withCustomProperties([
                        'field_name' => $fieldName,
                        'label'      => $document['label'],
                    ])
                    ->toMediaCollection('documents');
                activity('application')
                    ->performedOn($application)
                    ->log('Documents uploaded');
            }
        }
    }
}
