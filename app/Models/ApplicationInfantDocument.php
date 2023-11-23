<?php

namespace App\Models;

use App\Enums\ApplicationInfantDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationInfantDocument extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => ApplicationInfantDocumentType::class
    ];

    protected $fillable = [
        'type',
        'path'
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
