<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $table = 'document_templates';

    protected $fillable = [
        'user_id', 'description', 'file_name', 'name',
    ];
}