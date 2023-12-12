<?php

namespace Mediashare\Marathon\Collection;

use Mediashare\Marathon\Entity\Commit;
use Ramsey\Collection\AbstractCollection;

class CommitCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Commit::class;
    }

    public function last(): Commit|null {
        return $this->data[array_key_last($this->data)] ?? null;
    }

    public function first(): Commit|null {
        return $this->data[array_key_first($this->data)] ?? null;
    }

    public function getKey(Commit $commit): mixed {
        return array_search($commit, $this->data);
    }

    public function allPrevious(Commit $commit): CommitCollection {
        $currentKey = $this->getKey($commit);
        return new CommitCollection(
            array_filter(
                array_map(
                    fn (Commit $commit, int $key) => $key < $currentKey ? $commit : null,
                    $this->data,
                    array_keys($this->data),
                ),
                fn (Commit|null $commit) => $commit instanceof Commit,
            )
        );
    }

    public function findOneBy(callable $callback): Commit|null {
        return $this->filter($callback)?->first();
    }
}