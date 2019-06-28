<?php

$producer = new RdKafka\Producer();
$producer->addBrokers('kafka-1,kafka-2,kafka-3');


$topic = $producer->newTopic('cluster_topic');
$x = time();
for ($i = $x; $i < $x + 100; $i++) {
    $message = "cluster_topic - Message  $i";
    $key = "key";
    echo "$message\n";
    $topic->produce(0, 0, $message, $key);
    $producer->poll(1000);
    sleep(1);
}

while ($producer->getOutQLen() > 0) {
    $producer->poll(1000);
}
