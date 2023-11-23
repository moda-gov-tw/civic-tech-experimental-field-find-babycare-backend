<?php

namespace App\Models;

use App\Enums\ApplicationInfantDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDraftInfantDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_draft_id',
        'infant_id',
        'type',
        'path'
    ];

    protected $casts = [
        'type' => ApplicationInfantDocumentType::class
    ];

    public function applicationDraft(): BelongsTo
    {
        return $this->belongsTo(ApplicationDraft::class);
    }

    public function infant(): BelongsTo
    {
        return $this->belongsTo(Infant::class);
    }
}
