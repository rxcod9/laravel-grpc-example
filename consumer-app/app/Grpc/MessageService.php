<?php

namespace App\Grpc;

use Messages\MessageRequest;
use Messages\MessageResponse;
use Spiral\RoadRunner\GRPC\ContextInterface;

class MessageService implements MessageServiceInterface
{
    public function SendMessage(
        ContextInterface $ctx,
        MessageRequest $in
    ): MessageResponse {
        $topic = $in->getTopic();
        $payload = $in->getPayload();

        error_log("Received on [$topic]: $payload");

        $response = new MessageResponse();
        $response->setSuccess(true);
        $response->setMessage(
            "Message received successfully on topic [$topic] " .
            "with payload: " . $payload
        );

        return $response;
    }
}
