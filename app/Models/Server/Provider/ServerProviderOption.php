<?php

namespace App\Models\Server\Provider;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServerProviderOption extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
    ];

    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function formatToString()
    {
        return $this->formatRAM($this->memory).' RAM - '.$this->cpus.'CPU Cores - '.$this->space.'GB SSD - $'.$this->priceHourly.' / Hour - $'.$this->priceMonthly.' / Month';
    }

    public function getRamString()
    {
        return  str_replace(' ', '', strtolower($this->formatRAM($this->memory)));
    }

    private function formatRAM($megaBytes)
    {
        if ($megaBytes >= 1024) {
            return number_format($megaBytes / 1024, 0).' GB';
        }

        return $megaBytes.' MB';
    }
}
