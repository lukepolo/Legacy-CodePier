<?php

namespace App\Classes;

/**
 * Class DiskSpace
 *
 * @package App\Classes
 */
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->format();
    }

    /**
     * Gets the percentage used
     *
     * @return string
     */
    public function getPercentageUsed()
    {
        return round(($this->used / $this->size) * 100) .'%';
    }

    /**
     * Converts from kb to GB
     *
     * @param $kilobytes
     * @return string
     */
    private function kilobytesToGigabytes($kilobytes)
    {
        return round($kilobytes / 1000000,2) .' GB';
    }

    /**
     * Formats to string : 20 / 40 GB (50%)
     *
     * @return string
     */
    public function format()
    {
        return $this->kilobytesToGigabytes($this->used). ' / '.$this->kilobytesToGigabytes($this->available) .' ('.$this->getPercentageUsed().')';
    }
}