<?php

namespace App\Notifications\Messages;

class DiscordEmbedField
{
    /**
     * The name of the field.
     *
     * @var string
     */
    public $name;

    /**
     * The value of the field.
     *
     * @var string
     */
    public $value;

    /**
     * Create a new Embed Field instance.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Set the name of the field.
     *
     * @param string $name
     *
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the value of the field.
     *
     * @param string $value
     *
     * @return $this
     */
    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get an array representation of the embedded field.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'inline' => false,
        ];
    }
}
