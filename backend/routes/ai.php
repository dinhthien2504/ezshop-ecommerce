<?php

use \App\Http\Controllers\AIController;

Route::post('/ai/generate-description', [AIController::class, 'generate_description']);
