<?php

namespace App\Notifications\Messages;

class DiscordEmbed
{
    /**
     * The title of embed.
     *
     * @var string
     */
    public $title;

    /**
     * The description of embed.
     *
     * @var string
     */
    public $description;

    /**
     * The URL of embed.
     *
     * @var string
     */
    public $url;

    /**
     * The color code of the embed.
     *
     * @var int
     */
    public $color;

    /**
     * The fields information.
     *
     * @var array
     */
    public $fields;

    /**
     * Set the title (url) of embed.
     *
     * @param string $title
     * @param string|null $url
     *
     * @return $this
     */
    public function title($title, $url = '')
    {
        $this->title = $title;
        $this->url = $url;

        return $this;
    }

    /**
     * Set the description (text) of embed.
     *
     * @param string $description
     *
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the fields information.
     *
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function field($name, $value)
    {
        $this->fields[] = new DiscordEmbedField($name, $value);

        return $this;
    }

    /**
     * Set the color code of the embed.
     *
     * @param int|string $code
     *
     * @return $this
     */
    public function color($code)
    {
        $this->color = $code;
        return $this;
    }

    /**
     * Get an array representation of the embedded content.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'color' => $this->color,
            'footer' => $this->footer,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'fields' => $this->fields,
        ];
    }
}
