<?php

namespace App\Models;

use App\Tools\Classes\DefaultModelUuid;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends DefaultModelUuid
{
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class);
    }
}
