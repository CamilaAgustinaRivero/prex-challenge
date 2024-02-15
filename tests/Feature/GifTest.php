<?php

namespace Tests\Feature;

use App\Models\favs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class GifTest extends TestCase
{
    public function testSearchWithValidParameters()
    {
        Passport::actingAs(
            User::factory()->create(),
            ['create-servers']
        );
        $response = $this->get('/api/gifs?query=test&limit=5&offset=0');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
            ]);
    }

    public function testSearchWithInvalidParameters()
    {
        Passport::actingAs(
            User::factory()->create(),
            ['create-servers']
        );
        $response = $this->get('/api/gifs?query=123&limit=abc&offset=xyz');
        
        $response->assertStatus(400)
            ->assertJsonStructure([
                'error',
            ]);
    }

    public function testSearchWithoutBeignLoggedIn()
    {
        $response = $this->get('/api/gifs?query=123&limit=5&offset=0');

        $response->assertStatus(302);
    }

}
