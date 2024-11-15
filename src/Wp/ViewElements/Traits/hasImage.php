<?php


namespace Torpedo\Wp\ViewElements\Traits;


use Torpedo\Wp\ImageHelper;
use Torpedo\Wp\ViewElements\ViewElement;

trait hasImage
{
    /**
     * URL of the image to display in the template
     * @var string
     */
	protected $image = null;

	private $imageId = null;
	private $imageSizeOverride = null;
	
	/**
	 * @return null
	 */
	public function getImage ()
	{
	    if (!$this->imageId) {
            return $this->image;
        }

        $seed = method_exists($this, 'getTitle')
            ? $this->getTitle()
            : rand(1, 1000);

        $imageSize = $this->imageSizeOverride ?? $this->getBestImageSize();

        try {
            $context = (new \ReflectionClass($this))->getShortName();
        } catch (\Exception $e) {
            $context = '';
        }

        return ImageHelper::getUrlOrDefault($this->imageId, $seed, $imageSize, $context);
	}
	
	/**
     * It is recommended that you set the image using setImageById passing
     * the attachment id instead, this is so an appropriate image size for
     * the context can be selected
	 * @param string $imageUrl
     * @return mixed|ViewElement|hasImage
	 */
	public function setImage ($imageUrl)
	{
		$this->image = $imageUrl;
		return $this;
	}
    
    /**
     * Sets the url of the image by getting the most appropriate sized
     * image for the attachment id
     *
     * @param int|array $attachment
     * @return mixed|ViewElement|hasImage
     */
	public function setImageById ($attachment, $imageSizeOverride = null)
    {
        $attachmentId = (is_array($attachment))
            ? $attachment['id']
            : $attachment;

        $this->imageId = $attachmentId;
        $this->imageSizeOverride = $imageSizeOverride;
        return $this;
    }
    
    protected function getBestImageSize()
    {
        return ImageHelper::IMAGE_SIZE_CARD_MEDIUM;
    }
}

