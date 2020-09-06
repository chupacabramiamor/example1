<?php

namespace App\Models;

use App\Models\Traits\WithUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scan extends Model
{
    public $timestamps = false;

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function cookies()
    {
        $query = Cookie::query()
            ->leftJoin('results', 'results.cookie_id', '=', 'cookies.id');

        return new HasMany($query, $this, 'scan_id', 'id');
    }
}
