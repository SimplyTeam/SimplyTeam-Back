<?php

// Navigate to the Laravel project directory
chdir(__DIR__ . '/FinalProject/');

// Run passport:install
exec('php artisan passport:install');

// Output success message
echo 'Passport installation completed.';
