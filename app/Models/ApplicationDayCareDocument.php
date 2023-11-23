<?php

namespace App\Models;

use App\Enums\ApplicationDayCareDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDayCareDocument extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => ApplicationDayCareDocumentType::class
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
