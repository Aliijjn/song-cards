<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    protected $guarded = ['id'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'release_date' => 'immutable_date',
        ];
    }

    public function artist(): HasMany
    {
        return $this->HasMany(Artist::class);
    }
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }
}
