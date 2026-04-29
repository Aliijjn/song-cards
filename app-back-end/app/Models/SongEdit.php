<?php

namespace App\Models;

use App\Tools\Classes\DefaultModel;

class SongEdit extends DefaultModel
{
    protected function casts(): array
    {
        return [
            'release_date' => 'immutable_date',
        ];
    }
}
