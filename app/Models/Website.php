<?php

namespace App\Models;

use App\Models\Traits\WithUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Website extends Model
{
    use WithUuid, SoftDeletes;

    const STATE_DISABLED = 1;
    const STATE_NEW = 2;
    const STATE_READY = 3;
    const STATE_COMPLETED = 4;

    public $timestamps = false;
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'domain', 'protocol', 'description' ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    public function lastResults()
    {
        $query = Result::query()
            ->leftJoin('scans', 'scans.id', '=', 'results.scan_id');

        return new HasMany($query, $this, 'website_id', 'id');
    }

    public function getBannerAttribute($value)
    {
        return $value ? json_decode($value) : null;
    }
}
