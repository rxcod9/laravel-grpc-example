<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protos/messages.proto

namespace GPBMetadata\Protos;

class Messages
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        $pool->internalAddGeneratedFile(
            '
�
protos/messages.protomessages"0
MessageRequest
topic (	
payload (	"3
MessageResponse
success (
message (	2T
MessageServiceB
SendMessage.messages.MessageRequest.messages.MessageResponsebproto3'
        , true);

        static::$is_initialized = true;
    }
}

