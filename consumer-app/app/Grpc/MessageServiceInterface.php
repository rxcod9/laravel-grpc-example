<?php

namespace App\Grpc;

use Messages\MessageRequest;
use Messages\MessageResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;
use Spiral\RoadRunner\GRPC\ServiceInterface;

interface MessageServiceInterface extends ServiceInterface
{
    // 👇 This MUST match your .proto service name
    public const NAME = 'messages.MessageService';

    public function SendMessage(
        ContextInterface $ctx,
        MessageRequest $in
    ): MessageResponse;
}
