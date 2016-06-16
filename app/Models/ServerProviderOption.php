<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerProviderOption extends Model
{
    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function formatToSring()
    {
        return $this->formatRAM($this->memory) . ' RAM - ' . $this->cpus . 'CPU Cores - ' . $this->space . 'GB SSD - $' . $this->priceHourly . ' / Hour - $' . $this->priceMonthly . ' / Month';
    }

    private function formatRAM($megaBytes)
    {
        if ($megaBytes >= 1024) {
            return number_format($megaBytes / 1024, 0) . ' GB';
        }

        return $megaBytes . ' MB';
    }
}
