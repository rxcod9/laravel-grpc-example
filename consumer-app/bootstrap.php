<?php

require __DIR__ . '/vendor/autoload.php';

// // Bootstrap Laravel
// $app = require_once __DIR__ . '/bootstrap/app.php';

// // Run the Laravel application to set Facade root
// $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
// ✅ OR Bind facades and service container (Log will now work)
// Illuminate\Support\Facades\Facade::setFacadeApplication($app);

use Spiral\RoadRunner\GRPC\Server;
use Spiral\RoadRunner\GRPC\Invoker;
use App\Grpc\MessageService;
use App\Grpc\MessageServiceInterface;

$server = new Server(new Invoker());
$server->registerService(MessageServiceInterface::class, new MessageService());
$server->serve();
