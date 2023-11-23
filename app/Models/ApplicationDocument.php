<?php

namespace App\Models;

use App\Enums\ApplicationDocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => ApplicationDocumentType::class
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
