<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    const IDENT_UNCLASSIFIED = 1;
    const IDENT_NECESSARY = 2;
    const IDENT_PREFERENCES = 3;
    const IDENT_STATISTICS = 4;
    const IDENT_ADVERTISING = 5;

    const IDENTS = [ self::IDENT_UNCLASSIFIED, self::IDENT_NECESSARY, self::IDENT_PREFERENCES, self::IDENT_STATISTICS, self::IDENT_ADVERTISING ];

    public $timestamps = false;

    protected $fillable = [ 'name', 'description' ];
}
