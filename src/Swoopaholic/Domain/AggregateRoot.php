<?php
namespace Swoopaholic\Domain;

interface AggregateRoot
{
    public function popRecordedEvents(): array;
    public static function reconstituteFromHistory(\Iterator $historyEvents);
}
