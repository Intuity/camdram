<?php
namespace Acts\DiaryBundle\Diary;

use Acts\DiaryBundle\Event\EventInterface;
use Acts\DiaryBundle\Event\SingleDayEventInterface;
use Acts\DiaryBundle\Event\MultiDayEventInterface;

class DiaryRow
{
    const MAX_ROW_RANGE_MINUTES = 30;

    private $items = array();

    private $start;

    private $end;

    private $start_date;

    public function __construct(\DateTime $start_time, \DateTime $start_date)
    {
        $this->start = clone $start_time;
        $this->end = clone $start_time;
        $this->start_date = $start_date;
    }

    public function calculateIndex(\DateTime $date)
    {
        $diff = $this->start_date->diff($date, false);
        $days = $diff->days;
        if ($diff->invert) $days *= -1;
        return $days;
    }

    /**
     * @param int $start_index
     * @param int $end_index
     * @return bool
     */
    public function rangeIsFree($start_index, $end_index)
    {
        foreach ($this->items as $item) {
            $item_start = $item->getStartIndex();
            $item_end = $item->getEndIndex();
            if ($start_index <= $item_end && $end_index >= $item_start) return false;
        }
        return true;
    }

    /**
     * @param EventInterface $event
     * @return bool
     */
    public function canAccept(EventInterface $event)
    {
        //First check if the time is the same (within a certain threshold)
        $earliest = clone $this->start;
        $earliest->modify('-'.self::MAX_ROW_RANGE_MINUTES.' minutes');
        $latest = clone $this->start;
        $latest->modify('+'.self::MAX_ROW_RANGE_MINUTES.' minutes');
        if ($event->getStartTime() < $earliest || $event->getStartTime() >= $latest) return false;

        //Now see if there's space in the row
        if ($event instanceof SingleDayEventInterface) {
            $index = $this->calculateIndex($event->getDate());
            return $this->rangeIsFree($index, $index);
        }
        elseif ($event instanceof MultiDayEventInterface) {
            $start_index = $this->calculateIndex($event->getStartDate());
            $end_index = $this->calculateIndex($event->getEndDate());
            if ($event->getStartTime() == new \DateTime('2013-02-17 13:45:00')) {
            }
            return $this->rangeIsFree($start_index, $end_index);
        }
    }

    private function addItem(DiaryItem $item)
    {
        if ($item->getStartIndex() < 0) $item->setIndex(0);
        if ($item->getEndIndex() > 6) $item->setNumberOfDays(7 - $item->getStartIndex());
        $this->items[$item->getStartIndex()] = $item;
    }

    public function addEvent(EventInterface $event)
    {
        $item = new DiaryItem();
        $item->setEvent($event);
        $item->setStartAt($event->getStartTime());
        $item->getEndAt($event->getEndTime());

        if ($event instanceof SingleDayEventInterface) {
            $item->setNumberOfDays(1);
            $item->setIndex($this->calculateIndex($event->getDate()));
            $this->items[] = $item;
        }
        elseif ($event instanceof MultiDayEventInterface) {
            if ($event->getStartDate() < $event->getExcludeDate()
                    && $event->getExcludeDate() < $event->getEndDate()) {
                $start_index = $this->calculateIndex($event->getStartDate());
                $end_index = $this->calculateIndex($event->getExcludeDate());
                $item->setIndex($start_index);
                $item->setNumberOfDays($end_index - $start_index);
                $this->addItem($item);

                $item2 = clone $item;
                $start_index = $this->calculateIndex($event->getExcludeDate());
                $end_index = $this->calculateIndex($event->getEndDate());
                $item2->setIndex($start_index+1);
                $item2->setNumberOfDays($end_index - $start_index);
                $this->addItem($item2);
            }
            else {

                $start_index = $this->calculateIndex($event->getStartDate());
                if ($event->getName() == 'AIDA' && $start_index == 4) { var_dump($start_index, $event->getStartDate()); }
                $end_index = $this->calculateIndex($event->getEndDate());
                $item->setIndex($start_index);
                $item->setNumberOfDays($end_index - $start_index + 1);
                $this->addItem($item);
            }
        }


        if (!$this->start || $item->getStartAt() < $this->start) {
            $this->start = $item->getStartAt();
        }
        if (!$this->end || $item->getStartAt() > $this->end) {
            $this->end = $item->getStartAt();
        }
    }

    public function sort()
    {
        ksort($this->items);
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getStartTime()
    {
        return $this->start;
    }

    public function setEndTime()
    {
        return $this->end;
    }
}
