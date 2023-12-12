<?php
namespace Mediashare\Marathon\Service;

use Mediashare\Marathon\Collection\TimerCollection;
use Mediashare\Marathon\Entity\Commit;
use Mediashare\Marathon\Entity\Config;
use Mediashare\Marathon\Entity\Timer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Output\OutputInterface;

class OutputService {
    private Table $table;

    public function __construct(
        private OutputInterface $output,
        private Config $config,
    ) {
        $this->table = new Table($this->output);
    }

    public function renderTimers(TimerCollection|Timer $timers): self {
        $this->table->setHeaders([
                [new TableCell(($timers instanceof Timer) ? 'Timer' : 'Timers', ['colspan' => 7])],
                ['ID', 'Name', 'Status', 'Commits', 'Duration', 'Current step', 'Start date', 'End date']
            ])
            ->setRows(
                ($timers instanceof Timer)
                    ? [$timers->toRender($this->config->getDateTimeFormat())]
                    : $timers->map(fn (Timer $timer) => $timer->toRender($this->config->getDateTimeFormat()))->toArray()
            )
            ->render()
        ;

        return $this;
    }

    public function renderCommits(Timer $timer): self {
        $this->table->setHeaders([
                [new TableCell('Commits', ['colspan' => 5])],
                ['N°', 'ID', 'Message', 'Duration', 'Total', 'Start date', 'End date']
            ])
            ->setRows(
                $timer
                    ->getCommits()
                    ->map(
                        fn (Commit $commit)
                            => $commit
                                ->toRender(
                                    $timer->getCommits()->getKey($commit) + 1,
                                    array_sum(
                                        $timer
                                            ->getCommits()
                                            ->allPrevious($commit)
                                            ->map(fn (Commit $previousCommit) => $previousCommit->getSeconds())
                                            ->toArray(),
                                    ),
                                    $this->config->getDateTimeFormat()
                                )
                    )
                    ->toArray(),
            )
            ->render();

        return $this;
    }
}