<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationInfantDocument;
use Storage;

class ApplicationInfantDocumentController extends Controller
{
  public function show(Application $application, ApplicationInfantDocument $infantDocument)
  {
    $this->authorize('view', [$infantDocument, $application]);

    return response()->file(Storage::path($infantDocument->path));
  }
}
