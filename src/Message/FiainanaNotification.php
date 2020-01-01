<?php
/**
 * Julien Rajerison <julienrajerison5@gmail.com>
 **/

namespace App\Message;

/**
 * Class FiainanaNotification.
 */
class FiainanaNotification
{
    private $content;

    /**
     * FiainanaNotification constructor.
     *
     * @param array|null $content
     */
    public function __construct(?array $content)
    {
        return $this->content = $content;
    }

    /**
     * @return array|null
     */
    public function getContent():?array
    {
        return $this->content;
    }
}
