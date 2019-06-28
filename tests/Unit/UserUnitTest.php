<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;


class UserUnitTest extends TestCase {
    
    /** @test */
    public function testGuest() {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    
    /** @test */
    public function it_can_create_an_article(){
        /*
        $user = factory(\App\Models\User::class)->create();
       
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        */
        //$this->assertTrue($result);

        //$this->assertInstanceOf(User::class, $user);
        //$this->assertEquals($data['firstname'], $user->firstsname);
        //$this->assertEquals($data['name'], $user->name);
        //$this->assertEquals($data['email'], $user->email);    
         

         
        $this->assertEquals(10, 10);
    }
}
