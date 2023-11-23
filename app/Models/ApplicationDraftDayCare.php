<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ApplicationDraftDayCare extends Pivot
{
  use HasFactory;

  protected $table = 'application_draft_day_care';

  public function applicationDraft(): BelongsTo
  {
    return $this->belongsTo(ApplicationDraft::class);
  }

  public function dayCare(): BelongsTo
  {
    return $this->belongsTo(DayCare::class);
  }
}
