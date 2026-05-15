<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * Extend Sanctum's PersonalAccessToken to enable soft deletes.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
