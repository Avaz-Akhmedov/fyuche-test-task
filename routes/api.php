<?php

use App\Http\Controllers\NotebookController;
use Illuminate\Support\Facades\Route;



Route::apiResource('notebook', NotebookController::class);
