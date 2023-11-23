<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDocument;
use Storage;

class ApplicationDocumentController extends Controller
{
  public function show(Application $application, ApplicationDocument $document)
  {
    $this->authorize('view', [$document, $application]);

    return response()->file(Storage::path($document->path));
  }
}
