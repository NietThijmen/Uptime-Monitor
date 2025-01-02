<?php

namespace App\Helpers;

class MagicArray
{
    public array $a;

    public function __construct(array $a)
    {
        $this->a = $a;
    }

    public function __get($name)
    {
        $value = $this->a[$name] ?? null;
        if (is_array($value)) {
            return new MagicArray($value);
        }

        return $value;
    }

    public function __set($name, $value)
    {
        $this->a[$name] = $value;
    }

    public function to_array()
    {
        return $this->a; // return the array
    }
}
