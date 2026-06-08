<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    //
    protected $table = 'medias';

    protected $fillable = [
        'filename', 'url', 'mime_type', 'file_size', 'user_id'
    ];
}
