#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';
$app = new Symfony\Component\Console\Application('Time-Tracking', '0.1.6');
$app->add((new Mediashare\Command\TrackingAllCommand('all')));
$app->add((new Mediashare\Command\TrackingStartCommand('start')));
$app->add((new Mediashare\Command\TrackingCommitCommand('commit')));
$app->add((new Mediashare\Command\TrackingStopCommand('stop')));
$app->add((new Mediashare\Command\TrackingStatusCommand('status')));
$app->add((new Mediashare\Command\TrackingEndCommand('end')));
$app->add((new Mediashare\Command\TrackingRemoveCommand('remove')));
$app->add((new Mediashare\Command\TrackingUpgradeCommand('upgrade')));
$app->run();