<?php

namespace App\Models;

use App\Enums\ApplicationDayCareDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDraftDayCareDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_draft_id',
        'day_care_id',
        'type',
        'path'
    ];

    protected $casts = [
        'type' => ApplicationDayCareDocumentType::class
    ];

    public function applicationDraft(): BelongsTo
    {
        return $this->belongsTo(ApplicationDraft::class);
    }

    public function dayCare(): BelongsTo
    {
        return $this->belongsTo(DayCare::class);
    }
}
