<?php

namespace App\Models;

use App\Tools\Classes\DefaultModelUuid;

class SongEdit extends DefaultModelUuid
{
    protected function casts(): array
    {
        return [
            'release_date' => 'immutable_date',
            'dismissed_errors' => 'array',
        ];
    }
}
