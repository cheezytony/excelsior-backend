<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostTag extends Pivot
{
    use SoftDeletes;

    public $timestamps = false;
}
