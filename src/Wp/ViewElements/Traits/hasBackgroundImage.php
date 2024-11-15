<?php

namespace Torpedo\Wp\ViewElements\Traits;

use Torpedo\Wp\ImageHelper;
use Torpedo\Wp\ViewElements\ViewElement;

trait hasBackgroundImage
{
	protected $background_image;

	private $backgroundImageId = null;
	private $backgroundImageSizeOverride = null;

	/**
	 * @return mixed
	 */
	public function getBackgroundImage ()
	{
	    // Do not return a background image if we have a theme background set to transparent
        if (property_exists($this, 'theme_background')) {
            if ($this->theme_background == 'transparent') {
                return '';
            }
        }

	    if (!$this->backgroundImageId) {
            return $this->background_image;
        }

        $seed = method_exists($this, 'getTitle')
            ? $this->getTitle()
            : rand(1, 1000);

        $imageSize = $this->backgroundImageSizeOverride ?? $this->getBestBackgroundImageSize();

        return ImageHelper::getUrlOrDefault($this->backgroundImageId, $seed, $imageSize);
    }
	
	/**
     * It is recommended that you set the image using setImageById passing
     * the attachment id instead, this is so an appropriate image size for
     * the context can be selected
	 * @param mixed $imageUrl
	 * @return hasBackgroundImage
     * @deprecated
	 */
	public function setBackgroundImage ($imageUrl)
	{
		$this->background_image = $imageUrl;
		return $this;
	}
    
    /**
     * Sets the url of the image by getting the most appropriate sized
     * image for the attachment id
     *
     * @param int $attachmentId|WP_Post
     * @return mixed|ViewElement|hasImage
     */
    public function setBackgroundImageById ($attachment, $imageSizeOverride = null)
    {
        $attachmentId = (is_array($attachment))
            ? $attachment['id']
            : $attachment;

        $this->backgroundImageId = $attachmentId;
        $this->backgroundImageSizeOverride = $imageSizeOverride;
        return $this;
    }

    protected function getBestBackgroundImageSize()
    {
        return ImageHelper::IMAGE_SIZE_LARGE;
    }

    public function getBackgroundImageId()
    {
        return $this->backgroundImageId;
    }
}

