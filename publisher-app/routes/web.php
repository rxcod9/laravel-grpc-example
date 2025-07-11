<?php

use App\Services\GrpcPublisher;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send', function () {
    return app(GrpcPublisher::class)->publish('orders', 'New order created');
});
