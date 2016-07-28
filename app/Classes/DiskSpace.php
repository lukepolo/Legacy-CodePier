<?php

namespace App\Classes;

class DiskSpace
{
    public $size;
    public $used;
    public $available;

    /**
     * DiskSpace constructor.
     * @param $size
     * @param $used
     * @param $available
     */
    public function __construct($size, $used, $available)
    {
        $this->size = $size;
        $this->used = $used;
        $this->available = $available;
    }

    public function __toString()
    {
        return $this->format();
    }

    public function getPercentageUsed()
    {
        return round(($this->used / $this->size) * 100) .'%';
    }

    private function kilobytesToGigabytes($kilobytes)
    {
        return round($kilobytes / 1000000,2) .' GB';
    }

    public function format()
    {
        return $this->kilobytesToGigabytes($this->used). ' / '.$this->kilobytesToGigabytes($this->available) .' ('.$this->getPercentageUsed().')';
    }
}