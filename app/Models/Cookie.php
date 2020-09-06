<?php

namespace App\Models;

use App\Models\Result;
use Illuminate\Database\Eloquent\Model;

class Cookie extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name', 'provider' ];

    protected $attributes = [
        'group_id' => Group::IDENT_UNCLASSIFIED
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_cookie')->withPivot('group_id', 'description');
    }

    public function setDescriptionAttribute($value)
    {
        if (is_string($value)) {
            return $this->attributes['description'] = json_encode([
                config('app.locale') => $value
            ]);
        }

        return $this->attributes['description'] = $value;
    }

    public function getDescriptionAttribute($value)
    {
        return json_decode($value, true);
    }
}
