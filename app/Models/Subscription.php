<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_TRAILING = 2;
    const STATUS_PAST_DUE = 3;  // Просрочено
    const STATUS_PAUSED = 4;
    const STATUS_DELETED = 5;
    const STATUS_CANCELLED = 6;

    protected $guarded = [ 'id' ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function website()
    {
        return $this->hasOne(Website::class);
    }
}
