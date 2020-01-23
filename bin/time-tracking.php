<?php
require __DIR__ . '/../vendor/autoload.php';
$app = new Symfony\Component\Console\Application('Time-Tracking', '1.0.0');
$app->add((new Mediashare\Command\TrackingStartCommand('start')));
$app->add((new Mediashare\Command\TrackingCommitCommand('commit')));
$app->add((new Mediashare\Command\TrackingStopCommand('stop')));
$app->add((new Mediashare\Command\TrackingStatusCommand('status')));
$app->add((new Mediashare\Command\TrackingEndCommand('end')));
$app->run();