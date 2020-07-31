<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'document_templates';

    protected $fillable = [
        'user_id', 'description', 'path', 'name',
    ];
}
