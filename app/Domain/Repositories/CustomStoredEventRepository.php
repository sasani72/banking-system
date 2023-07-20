<?php

namespace App\Domain\Repositories;

use Spatie\EventSourcing\StoredEvents\Exceptions\InvalidStoredEvent;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEventCollection;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEventQueryBuilder;
use Spatie\EventSourcing\StoredEvents\Repositories\EloquentStoredEventRepository;

class CustomStoredEventRepository extends EloquentStoredEventRepository
{
    /**
     * @param string $event
     * @param string $uuid
     * @return EloquentStoredEventCollection
     */
    public function retrieveAllWhereEvent(string $event, string $uuid):EloquentStoredEventCollection
    {

        return $this->getQuery()
            ->whereEvent($event)
            ->where('aggregate_uuid', $uuid)
            ->get();
    }

    /**
     * @return EloquentStoredEventQueryBuilder
     */
    private function getQuery(): EloquentStoredEventQueryBuilder
    {
        return $this->storedEventModel::query();
    }
}
