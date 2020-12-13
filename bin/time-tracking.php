#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';
$app = new Symfony\Component\Console\Application('Time-Tracking', '0.1.7');
$app->add((new Mediashare\Command\TimerListCommand('timer:list')));
$app->add((new Mediashare\Command\TimerStartCommand('timer:start')));
$app->add((new Mediashare\Command\TimerStopCommand('timer:stop')));
$app->add((new Mediashare\Command\TimerStatusCommand('timer:status')));
$app->add((new Mediashare\Command\TimerEndCommand('timer:end')));
$app->add((new Mediashare\Command\TimerRemoveCommand('timer:remove')));
$app->add((new Mediashare\Command\CommitCommand('timer:commit')));
$app->add((new Mediashare\Command\CommitEditCommand('timer:commit:edit')));
$app->add((new Mediashare\Command\CommitRemoveCommand('timer:commit:remove')));
$app->add((new Mediashare\Command\UpgradeCommand('upgrade')));
$app->run();