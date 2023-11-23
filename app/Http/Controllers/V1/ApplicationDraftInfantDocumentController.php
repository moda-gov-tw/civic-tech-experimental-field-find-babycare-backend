<?php

namespace App\Http\Controllers\V1;

use App\Enums\ApplicationInfantDocumentType;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationDraftInfantDocumentResource;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftInfantDocument;
use App\Rules\ValidDocument;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Storage;

class ApplicationDraftInfantDocumentController extends Controller
{
  public function store(Request $request, ApplicationDraft $draft)
  {
    $this->authorize('create', [ApplicationDraftInfantDocument::class, $draft]);

    $validated = $request->validate([
      'infant_id' => [
        'required',
        Rule::exists('application_draft_infant')->where(
          fn (Builder $query) => $query->where('application_draft_id', $draft->id)
        ),
      ],
      'type' => ['required', new Enum(ApplicationInfantDocumentType::class)],
      'file' => ['required', new ValidDocument()]
    ]);

    $path = $validated['file']->store('documents');

    return new ApplicationDraftInfantDocumentResource(
      ApplicationDraftInfantDocument::create([
        'application_draft_id' => $draft->id,
        'infant_id' => $validated['infant_id'],
        'type' => $validated['type'],
        'path' => $path
      ])
    );
  }

  public function show(ApplicationDraft $draft, ApplicationDraftInfantDocument $infantDocument)
  {
    $this->authorize('view', [$infantDocument, $draft]);

    return response()->file(Storage::path($infantDocument->path));
  }

  public function destroy(ApplicationDraft $draft, ApplicationDraftInfantDocument $infantDocument)
  {
    $this->authorize('delete', [$infantDocument, $draft]);

    Storage::delete($infantDocument->path);

    $infantDocument->delete();
  }
}
