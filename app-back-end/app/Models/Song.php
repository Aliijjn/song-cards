<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Song extends Model
{
    /** @use HasFactory<\Database\Factories\SongFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    public function album(): BelongsTo
    {
        return $this->BelongsTo(Album::class);
    }

    public function artist(): BelongsTo
    {
        return $this->BelongsTo(Artist::class);
    }
}
