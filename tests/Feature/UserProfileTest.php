<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\BaseFeature;

class UserProfileTest extends BaseFeature
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testObtainUserInformation()
    {
        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('GET', route('me.info'));

        $response->assertOk();

        $response->assertJsonFragment([
            'id' => $this->user->id,
            'fullname' => $this->user->fullname,
            'email' => $this->user->email
        ]);
    }

    public function testUpdateUserInformation()
    {
        $data = [
            'fullname' => $this->faker->name,
            'email' => $this->faker->email
        ];

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('PATCH', route('me.update'), $data);

        $response->assertOk();

        $response->assertJsonFragment([
            'fullname' => $data['fullname'],
            'email' => $data['email']
        ]);
    }

    public function testUpdateUserInformationWithPassword()
    {
        $data = [
            'fullname' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => '123456',
            'password_confirmation' => '123456'
        ];

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('PATCH', route('me.update'), $data);

        $response->assertOk();

        $this->assertTrue(Hash::check($data['password'], $this->user->fresh()->password));
    }
}
