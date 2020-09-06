<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Scans
{
    public function creating(Model $model)
    {
        $model->key = Uuid::uuid4()->toString();
    }
}
