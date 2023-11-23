<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ApplicationDraftInfant extends Pivot
{
  use HasFactory;

  protected $table = 'application_draft_infant';

  public function applicationDraft(): BelongsTo
  {
    return $this->belongsTo(ApplicationDraft::class);
  }

  public function infant(): BelongsTo
  {
    return $this->belongsTo(Infant::class);
  }
}
