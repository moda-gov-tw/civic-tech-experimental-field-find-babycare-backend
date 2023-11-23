<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDayCareDocument;
use Storage;

class ApplicationDayCareDocumentController extends Controller
{
  public function show(Application $application, ApplicationDayCareDocument $dayCareDocument)
  {
    $this->authorize('view', [$dayCareDocument, $application]);

    return response()->file(Storage::path($dayCareDocument->path));
  }
}
