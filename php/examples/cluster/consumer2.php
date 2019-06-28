<?php

define('CONSUMER_GROUP', 'cluster_consumer_group');

$conf = new RdKafka\Conf();
// Set the group id. This is required when storing offsets on the broker
$conf->set('group.id', CONSUMER_GROUP);

$consumer = new RdKafka\Consumer($conf);
$consumer->addBrokers('kafka-1,kafka-2,kafka-3');

$queue = $consumer->newQueue();

$topicConf = new RdKafka\TopicConf();
$topicConf->set('auto.commit.interval.ms', 100);

$topic1 = $consumer->newTopic("cluster_topic", $topicConf);
$topic1->consumeQueueStart(0, RD_KAFKA_OFFSET_STORED, $queue);

$topic2 = $consumer->newTopic("cluster_topic2", $topicConf);
$topic2->consumeQueueStart(0, RD_KAFKA_OFFSET_STORED, $queue);

while (true) {
    $message = $queue->consume(120 * 1000);
    processMessage($message);
}

function processMessage(RdKafka\Message $message)
{
    switch ($message->err) {
        case RD_KAFKA_RESP_ERR_NO_ERROR:
            //print_r($message);
            echo "{$message->payload}\n";
            break;
        case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            echo "No more messages; will wait for more\n";
            break;
        case RD_KAFKA_RESP_ERR__TIMED_OUT:
            echo "Timed out\n";
            break;
        default:
            echo "Error: {$message->errstr()}, {$message->err}\n";
            break;
    }
}