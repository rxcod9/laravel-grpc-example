<?php

namespace App\Services;

use Grpc\ChannelCredentials;
use Messages\MessageRequest;
use Messages\MessageServiceClient;

class GrpcPublisher
{
    public function publish(string $topic, string $payload): string
    {
        $client = new MessageServiceClient(
            'consumer-app.test:50051',
            [
                'credentials' => ChannelCredentials::createInsecure()
            ]
        );

        $req = new MessageRequest();
        $req->setTopic($topic);
        $req->setPayload($payload);

        list($res, $status) = $client->SendMessage($req)->wait();

        if ($status->code !== 0) {
            return "❌ gRPC Error: " . $status->details;
        }

        return "✅ gRPC OK: " . $res->getMessage();
    }
}
