<?php

namespace App\Http\Controllers\V1;

use App\Enums\ApplicationDocumentType;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationDraftDocumentResource;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDocument;
use App\Rules\ValidDocument;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Storage;

class ApplicationDraftDocumentController extends Controller
{
  public function store(Request $request, ApplicationDraft $draft)
  {
    $this->authorize('create', [ApplicationDraftDocument::class, $draft]);

    $validated = $request->validate([
      'type' => ['required', new Enum(ApplicationDocumentType::class)],
      'file' => ['required', new ValidDocument()]
    ]);

    $path = $validated['file']->store('documents');

    return new ApplicationDraftDocumentResource(
      ApplicationDraftDocument::create([
        'application_draft_id' => $draft->id,
        'type' => $validated['type'],
        'path' => $path
      ])
    );
  }

  public function show(ApplicationDraft $draft, ApplicationDraftDocument $document)
  {
    $this->authorize('view', [$document, $draft]);

    return response()->file(Storage::path($document->path));
  }

  public function destroy(ApplicationDraft $draft, ApplicationDraftDocument $document)
  {
    $this->authorize('delete', [$document, $draft]);

    Storage::delete($document->path);

    $document->delete();
  }
}
