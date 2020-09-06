<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BaseFeature extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected $user;
    protected $headers = [
        'X-Requested-With' => 'XMLHttpRequest',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('type', User::TYPE_CUSTOMER)->first();
    }
}