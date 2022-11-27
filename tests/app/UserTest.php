<?php

use Tests\TestCase;
 
class UserTest extends TestCase
{
    public function test_user_create_update()
    {
        $response = $this->post('/user', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
        $response->assertStatus(200);
    }
}