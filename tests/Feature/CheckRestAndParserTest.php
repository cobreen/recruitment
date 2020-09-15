<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckRestAndParser extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testBreakGet()
    {
        $res = $this->get('/api/queue/1/get?key=');
        
        $this->assertTrue((json_decode($res->getContent())->status ?? null) == 'error');
        $res->assertStatus(200);
    }

    public function testBreakGetProduct()
    {
        $res = $this->get('/api/queue/1/1/get?key=');
        
        $this->assertTrue((json_decode($res->getContent())->status ?? null) == 'error');

        $res->assertStatus(200);
    }

    public function testBreakGetProductAttribute()
    {
        $res = $this->get('/api/queue/1/1/get?key=');
        
        $this->assertTrue((json_decode($res->getContent())->status ?? null) == 'error');

        $res->assertStatus(200);
    }

    public function testBreakAddProduct()
    {
        $res = $this->post('/api/queue/123/add?key=');
        
        $this->assertTrue((json_decode($res->getContent())->status ?? null) == 'error');

        $res->assertStatus(200);
    }

    public function testBreakUpdateProduct()
    {
        $res = $this->post('/api/queue/123/123/name/test?key=');
        
        $this->assertTrue((json_decode($res->getContent())->status ?? null) == 'error');

        $res->assertStatus(200);
    }

    public function testBreakDeleteProduct()
    {
        $res = $this->post('/api/queue/123/123/drop?key=');
        
        $this->assertTrue((json_decode($res->getContent())->status ?? null) == 'error');

        $res->assertStatus(200);
    }

    public function testBreakParsedFileReceive()
    {
        $res = $this->get('/api/yml/give?key=&token=');
        
        $this->assertTrue((json_decode($res->getContent())->status ?? null) == 'error');

        $res->assertStatus(200);
    }

    public function testBreakFileOrdering()
    {
        $res = $this->post('/api/yml/make?key=');
        
        $this->assertTrue((json_decode($res->getContent())->status ?? null) == 'error');

        $res->assertStatus(200);
    }
}
