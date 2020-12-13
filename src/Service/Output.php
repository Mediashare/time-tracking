<?php
namespace Mediashare\Service;

use Mediashare\Entity\Tracking;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;

Class Output {
    public $output;
    public function __construct($output) {
        $this->output = $output;
    }

    /**
     * Render output for Symfony console
     *
     * @param Tracking $tracking
     * @return self
     */
    public function render(Tracking $tracking) {
        $table = new Table($this->output);
        // Commits
        $table->setHeaders([
                [new TableCell('Commits', ['colspan' => 5])],
                ['N°', 'ID', 'Message', 'Duration', 'Total', 'Create date']
            ])
            ->setRows($tracking->getCommits())
            ->render();
        
        // Informations
        $table->setHeaders([
                [new TableCell('Tracking', ['colspan' => 7])],
                ['ID', 'Name', 'Status', 'Commits', 'Duration', 'Current step', 'Create date']
            ])
            ->setRows([$tracking->getInformations()])
            ->render();
            
        return $this;
    }
}