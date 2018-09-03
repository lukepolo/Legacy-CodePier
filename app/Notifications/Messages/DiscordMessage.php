<?php

namespace App\Notifications\Messages;

class DiscordMessage
{
    /**
     * The message contents (up to 2000 characters).
     *
     * @var string
     */
    public $content;

    /**
     * Embedded rich content.
     *
     * @var array
     */
    public $embeds;

    /**
     * The "level" of the notification (info, success, warning, error).
     *
     * @var string
     */
    public $level = 'info';

    /**
     * Allows to set the content by creation.
     *
     * @param string $content
     */
    public function __construct($content = null)
    {
        if (! is_null($content)) {
            $this->content($content);
        }
    }

    /**
     * Set the content of the message.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Define an embedded rich content for the message.
     *
     * @param \Closure $callback
     *
     * @return $this
     */
    public function embed(\Closure $callback)
    {
        $this->embeds[] = $embed = new DiscordEmbed;

        $callback($embed);

        return $this;
    }

    /**
     * Indicate that the notification gives information about an operation.
     *
     * @return $this
     */
    public function info()
    {
        $this->level = 'info';

        return $this;
    }

    /**
     * Indicate that the notification gives information about a successful operation.
     *
     * @return $this
     */
    public function success()
    {
        $this->level = 'success';

        return $this;
    }

    /**
     * Indicate that the notification gives information about a warning.
     *
     * @return $this
     */
    public function warning()
    {
        $this->level = 'warning';

        return $this;
    }

    /**
     * Indicate that the notification gives information about an error.
     *
     * @return $this
     */
    public function error()
    {
        $this->level = 'error';

        return $this;
    }

    /**
     * Get the color for the message.
     *
     * @return string
     */
    public function color()
    {
        switch ($this->level) {
            case 'success':
                return 0x4BB543; // Green
            case 'error':
                return 0xCC0000; // Red
            case 'warning':
                return 0xFFAE42; // Yellow
        }
    }

    /**
     * Get an array representation of the message.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'content' => $this->content,
            'embeds' => $this->embeds,
        ];
    }
}
