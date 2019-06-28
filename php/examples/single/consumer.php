<?php

define('CONSUMER_GROUP', 'test_consumer_group');

$conf = new RdKafka\Conf();
// Set the group id. This is required when storing offsets on the broker
$conf->set('group.id', CONSUMER_GROUP);

$consumer = new RdKafka\Consumer($conf);
$consumer->addBrokers('kafka');

/* auto commit
$topicConf = new RdKafka\TopicConf();
$topicConf->set('auto.commit.interval.ms', 100);
// Set the offset store method to 'file'
$topicConf->set('offset.store.method', 'file');
$topicConf->set('offset.store.path', sys_get_temp_dir());
// Alternatively, set the offset store method to 'broker'
//$topicConf->set('offset.store.method', 'broker');

$topic = $consumer->newTopic('test_topic', $topicConf);
$topic->consumeStart(0, rd_kafka_offset_tail(5));
while (true) {
    $message = $topic->consume(0, 120 * 10000);
    processMessage($message);
}
// */


//*  manual commit
$topicConf = new RdKafka\TopicConf();
$topicConf->set('auto.commit.enable', 'false');
$topic = $consumer->newTopic('test_topic', $topicConf);
$topic->consumeStart(0, rd_kafka_offset_tail(5));
while (true) {
    $message = $topic->consume(0, 120 * 1000);
    processMessage($message);
    $topic->offsetStore($message->partition, $message->offset);
    sleep(1);
}
// */

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