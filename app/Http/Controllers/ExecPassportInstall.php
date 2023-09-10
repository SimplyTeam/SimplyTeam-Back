<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExecPassportInstall extends Controller
{
    public function activatePassportInstall() {

        try {
            // Run passport:install
            exec('php artisan passport:install');

            return response(['success' => true]);
        }catch (\Exception $exception) {
            return response(['error' => $exception->getMessage()]);
        }
    }
}
