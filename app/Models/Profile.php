<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected function avatar(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => !preg_match("/^http/", $value) && !empty($value)
                ? Storage::url($value)
                : $value,
        );
    }
}
