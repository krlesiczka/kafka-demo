<?php

$producer = new RdKafka\Producer();
$producer->setLogLevel(LOG_DEBUG);
$producer->addBrokers('kafka');

$topic = $producer->newTopic('test_topic');
$x = time();
for ($i = $x; $i < $x + 10; $i++) {
    $message = "Message $i";
    $key = "key-$i";
    echo "$message\n";
    $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message, $key);
    $producer->poll(0);
}

while ($producer->getOutQLen() > 0) {
    $producer->poll(50);
}
