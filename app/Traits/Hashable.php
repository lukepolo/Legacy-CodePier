<?php

namespace App\Traits;

trait Hashable
{
    public function encode()
    {
        return \Hashids::connection($this->hashConnection ?: 'default')->encode($this->id);
    }

    public function decode($hash)
    {
        return $this->findOrFail(\Hashids::connection($this->hashConnection ?: 'default')->decode($hash));
    }
}
