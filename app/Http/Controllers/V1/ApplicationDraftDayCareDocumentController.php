<?php

namespace App\Http\Controllers\V1;

use App\Enums\ApplicationDayCareDocumentType;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ApplicationDraftDayCareDocumentResource;
use App\Models\ApplicationDraft;
use App\Models\ApplicationDraftDayCareDocument;
use App\Rules\ValidDocument;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Storage;

class ApplicationDraftDayCareDocumentController extends Controller
{
  public function store(Request $request, ApplicationDraft $draft)
  {
    $this->authorize('create', [ApplicationDraftInfantDocument::class, $draft]);

    $validated = $request->validate([
      'day_care_id' => [
        'required',
        Rule::exists('application_draft_day_care')->where(
          fn (Builder $query) => $query->where('application_draft_id', $draft->id)
        ),
      ],
      'type' => ['required', new Enum(ApplicationDayCareDocumentType::class)],
      'file' => ['required', new ValidDocument()]
    ]);

    $path = $validated['file']->store('documents');

    return new ApplicationDraftDayCareDocumentResource(
      ApplicationDraftDayCareDocument::create([
        'application_draft_id' => $draft->id,
        'day_care_id' => $validated['day_care_id'],
        'type' => $validated['type'],
        'path' => $path
      ])
    );
  }

  public function show(ApplicationDraft $draft, ApplicationDraftDayCareDocument $dayCareDocument)
  {
    $this->authorize('view', [$dayCareDocument, $draft]);

    return response()->file(Storage::path($dayCareDocument->path));
  }

  public function destroy(ApplicationDraft $draft, ApplicationDraftDayCareDocument $dayCareDocument)
  {
    $this->authorize('delete', [$dayCareDocument, $draft]);

    Storage::delete($dayCareDocument->path);

    $dayCareDocument->delete();
  }
}
