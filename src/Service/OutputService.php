<?php
namespace Mediashare\TimeTracking\Service;

use Mediashare\TimeTracking\Collection\TrackingCollection;
use Mediashare\TimeTracking\Entity\Commit;
use Mediashare\TimeTracking\Entity\Config;
use Mediashare\TimeTracking\Entity\Tracking;
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

    public function renderTrackings(TrackingCollection|Tracking $trackings): self {
        $this->table->setHeaders([
                [new TableCell(($trackings instanceof Tracking) ? 'Tracking' : 'Trackings', ['colspan' => 7])],
                ['ID', 'Name', 'Status', 'Commits', 'Duration', 'Current step', 'Start date', 'End date']
            ])
            ->setRows(
                ($trackings instanceof Tracking)
                    ? [$trackings->toRender($this->config->getDateTimeFormat())]
                    : $trackings->map(fn (Tracking $tracking) => $tracking->toRender($this->config->getDateTimeFormat()))->toArray()
            )
            ->render()
        ;

        return $this;
    }

    public function renderCommits(Tracking $tracking): self {
        $this->table->setHeaders([
                [new TableCell('Commits', ['colspan' => 5])],
                ['N°', 'ID', 'Message', 'Duration', 'Total', 'Start date', 'End date']
            ])
            ->setRows(
                $tracking
                    ->getCommits()
                    ->map(
                        fn (Commit $commit)
                            => $commit
                                ->toRender(
                                    $tracking->getCommits()->getKey($commit) + 1,
                                    array_sum(
                                        $tracking
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