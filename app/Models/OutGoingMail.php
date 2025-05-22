<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutGoingMail extends Model
{
    protected $fillable = [
        'to',
        'subject',
        'html_body'
    ];
}
