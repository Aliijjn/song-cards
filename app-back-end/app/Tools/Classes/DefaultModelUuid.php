<?php

namespace App\Tools\Classes;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DefaultModelUuid extends Model
{
    use HasUuids, HasTimestamps;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];
}
