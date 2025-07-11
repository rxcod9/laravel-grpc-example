<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Grpc\ChannelCredentials;
use Messages\MessageRequest;
use Messages\MessageServiceClient;

class GrpcPublishMessage extends Command
{
    protected $signature = 'grpc:publish-message 
                            {topic : The topic name} 
                            {payload : The message payload (stringified JSON or raw)} 
                            {--host=consumer-app.test:50051 : gRPC server address}';

    protected $description = 'Publish a message via gRPC to the consumer service';

    public function handle()
    {
        $topic = $this->argument('topic');
        $payload = $this->argument('payload');
        $host = $this->option('host');

        $this->info("📡 Connecting to gRPC server at {$host}");

        $client = new MessageServiceClient(
            $host,
            [
                'credentials' => ChannelCredentials::createInsecure(),
            ]
        );

        $request = new MessageRequest();
        $request->setTopic($topic);
        $request->setPayload($payload);

        list($response, $status) = $client->SendMessage($request)->wait();

        if ($status->code === 0) {
            $this->info('✅ Message sent successfully!');
            $this->info('Response: ' . $response->getMessage());
        } else {
            $this->error("❌ Failed to send message: {$status->details}");
        }

        return $status->code === 0 ? 0 : 1;
    }
}
