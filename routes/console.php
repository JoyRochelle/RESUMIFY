<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('resumify:setup', function () {
    $this->info('Setting up the Resumify project...');

    $this->info('1. Linking storage...');
    $this->call('storage:link');

    $this->info('2. Running migrations and seeding database (this might take a few seconds)...');
    $this->call('migrate:fresh', ['--seed' => true]);

    $this->info('Project setup complete! You can now log in with admin@resumify.com / password.');
})->purpose('Setup the project for a new developer (links storage, migrates, and seeds)');
