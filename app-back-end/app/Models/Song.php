<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Song extends Model
{
    protected $guarded = ['id'];

    public function album(): BelongsTo
    {
        return $this->BelongsTo(Album::class);
    }

    public function artist(): BelongsTo
    {
        return $this->BelongsTo(Artist::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }
}
