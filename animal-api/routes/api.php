<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::any('/', [ApiController::class, 'handle']);
