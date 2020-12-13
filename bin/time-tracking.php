#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

$app = new Symfony\Component\Console\Application('Time-Tracking', '0.2.1');
$commandsLoader = new \Symfony\Component\Console\CommandLoader\FactoryCommandLoader([
    'timer:list' => function () { return new Mediashare\Command\TimerListCommand(); },
    'timer:start' => function () { return new Mediashare\Command\TimerStartCommand(); },
    'timer:stop' => function () { return new Mediashare\Command\TimerStopCommand(); },
    'timer:status' => function () { return new Mediashare\Command\TimerStatusCommand(); },
    'timer:archive' => function () { return new Mediashare\Command\TimerArchiveCommand(); },
    'timer:remove' => function () { return new Mediashare\Command\TimerRemoveCommand(); },
    'timer:commit' => function () { return new Mediashare\Command\CommitCommand(); },
    'timer:commit:edit' => function () { return new Mediashare\Command\CommitEditCommand(); },
    'timer:commit:remove' => function () { return new Mediashare\Command\CommitRemoveCommand(); },
    'upgrade' => function () { return new Mediashare\Command\UpgradeCommand(); },
]);
$app->setCommandLoader($commandsLoader);
$app->run();