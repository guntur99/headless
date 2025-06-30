<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MediaManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'filename',
        'path',
    ];

    protected $keyType = 'string';

}
