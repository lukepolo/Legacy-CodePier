<?php

namespace App\Traits;

trait Hashable
{
    public function encode()
    {
        return \Hashids::encode($this->id);
    }

    public function decode($hash)
    {
        return $this->findOrFail(\Hashids::decode($hash));
    }
}
