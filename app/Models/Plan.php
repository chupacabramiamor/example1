<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    const TYPE_DAILY = 1;
    const TYPE_WEEKLY = 2;
    const TYPE_MONTHLY = 3;
    const TYPE_ANNUALLY = 4;

    public $timestamps = false;

    protected $guarded = [ 'id', 'created_at' ];
}
