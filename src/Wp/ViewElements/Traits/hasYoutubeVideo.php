<?php


namespace Torpedo\Wp\ViewElements\Traits;


use Torpedo\Wp\ViewElements\ViewElement;

trait hasYoutubeVideo
{
    /** @var string */
    protected $youtube_id;

    /**
     * @return string
     */
    public function getYoutubeId()
    {
        return $this->youtube_id;
    }

    /**
     * @param string $id
     * @return hasVideo|ViewElement|mixed
     */
    public function setYoutubeId($id)
    {
        $this->youtube_id = $id;
        return $this;
    }
}

