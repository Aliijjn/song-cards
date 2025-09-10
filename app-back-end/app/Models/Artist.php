<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    protected $guarded = ['id'];
    protected $keyType = 'string';
    public $incrementing = false;

    public function genres(): BelongsToMany
    {
        return $this->BelongsToMany(Genre::class);
    }

    public function albums(): BelongsToMany
    {
        return $this->BelongsToMany(Album::class);
    }

    public function songs(): BelongsToMany
    {
        return $this->BelongsToMany(Song::class);
    }
}
