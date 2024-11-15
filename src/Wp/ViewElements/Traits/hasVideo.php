<?php


namespace Torpedo\Wp\ViewElements\Traits;


use Torpedo\Wp\ViewElements\ViewElement;

trait hasVideo
{
    /** @var string */
    protected $video_url;

    protected $video_youtube_id;

    /**
     * @return string
     */
    public function getVideoUrl()
    {
        return $this->video_url;
    }

    /**
     * @param mixed $video
     * @return hasVideo|ViewElement|mixed
     */
    public function setVideoUrl($video)
    {
        $this->video_url = $video;
        return $this;
    }

    /**
     * @param $videoId
     * @return hasVideo|ViewElement|mixed
     */
    public function setVideoById($videoId)
    {
        if (empty($videoId)) {
            return $this;
        }

        if (strpos($videoId, 'http') !== false) {
            // Ok, maybe it isn't a youtube id...
            $this->setVideoUrl($videoId);
        }

        $this->setVideoUrl("https://www.youtube.com/embed/$videoId?rel=0");
        if (method_exists($this, 'setImage')) {
            $this->setImage("https://i3.ytimg.com/vi/$videoId/maxresdefault.jpg");
        }
        
        return $this;
    }
}

