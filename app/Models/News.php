<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as Auditing;
use OwenIt\Auditing\Auditable;

class News extends Model implements Auditing
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'title',
        'text'
    ];
}
