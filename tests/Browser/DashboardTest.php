<?php

use App\Models\User;

it('allows authenticated user to view dashboard', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $page = visit('/login');

    $page->assertSee('Log in to your account')
        ->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->click('Log in')
        ->assertUrlIs('/dashboard')
        ->assertSee('DYSFUNCTIONAL')
        ->assertSee('Functions')
        ->assertSee('Total Runs')
        ->assertSee('Runs Today')
        ->assertSee('Unresolved Errors')
        ->assertNoJavascriptErrors();
});

it('displays function execution chart on dashboard', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $page = visit('/login');

    $page->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->click('Log in')
        ->assertSee('Function Executions')
        ->assertSee('Success vs errors over time')
        ->assertSee('Last 24 Hours')
        ->assertNoJavascriptErrors();
});

it('shows recent function runs with logs', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $page = visit('/login');

    $page->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->click('Log in')
        ->waitForText('Run ID')
        ->assertSee('Runtime')
        ->assertSee('Response')
        ->assertSee('Memory')
        ->assertSee('Cost')
        ->assertNoJavascriptErrors();
});
