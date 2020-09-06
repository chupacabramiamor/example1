<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'user_id', 'website_id', 'scan_id', 'cookie_id', 'expired_at', 'path', 'flags', 'url' ];

    protected $attributes = [
        'path' => '/'
    ];

    protected $casts = [
        'flags' => 'array'
    ];

    public function scan()
    {
        return $this->belongsTo(Scan::class);
    }

    public function cookie()
    {
        return $this->belongsTo(Cookie::class);
    }
}
