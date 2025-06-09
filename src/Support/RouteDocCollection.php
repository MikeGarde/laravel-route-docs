<?php

namespace RouteDocs\Support;

use Illuminate\Support\Collection;

class RouteDocCollection extends Collection
{
    public function sortByKey(string $key): self
    {
        return new static($this->sortBy(fn(RouteDocEntry $entry) => $entry->{$key} ?? null)->values());
    }

    public function hasErrors(): bool
    {
        return $this->contains(fn(RouteDocEntry $entry) => $entry->error);
    }

    public function onlyErrors(): self
    {
        return new static($this->filter(fn(RouteDocEntry $entry) => $entry->error)->values());
    }

    public function toDisplayArray(bool $includeError = false, bool $withColor = true): array
    {
        return $this->map(function (RouteDocEntry $entry) use ($withColor, $includeError) {
            $entry->setOutputColor($withColor);
            $row = $entry->toArray();

            return $includeError ? $row : array_slice($row, 1); // Drop 'error' column if not needed
        })->values()->all();
    }
}
