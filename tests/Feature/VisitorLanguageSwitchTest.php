<?php

namespace Tests\Feature;

use Tests\TestCase;

class VisitorLanguageSwitchTest extends TestCase
{
    public function test_visitor_pages_switch_between_dutch_and_english(): void
    {
        $this->get('/visitor/login')
            ->assertOk()
            ->assertSee('Bezoeker inloggen')
            ->assertSee('Log anoniem in met alleen je naam.');

        $this->get('/lang/en');

        $this->get('/visitor/login')
            ->assertOk()
            ->assertSee('Visitor Sign In')
            ->assertSee('Sign in anonymously with only your name.');

        $this->get('/')
            ->assertOk()
            ->assertSee('Smarter reception, safer check-in.');

        $this->get('/lang/nl');

        $this->get('/visitor/login')
            ->assertOk()
            ->assertSee('Bezoeker inloggen');
    }
}
