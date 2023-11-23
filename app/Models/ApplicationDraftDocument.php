<?php

namespace App\Models;

use App\Enums\ApplicationDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDraftDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_draft_id',
        'type',
        'path'
    ];

    protected $casts = [
        'type' => ApplicationDocumentType::class
    ];

    public function applicationDraft(): BelongsTo
    {
        return $this->belongsTo(ApplicationDraft::class);
    }
}
