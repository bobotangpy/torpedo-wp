<?php

namespace Torpedo\Wp\ViewElements\Traits;

use Torpedo\Wp\ViewElements\ViewElement;

trait hasTheme
{
    /**
     * @var string
     * @ORM/
     */
	protected $theme_class = '';
	protected $theme_size = '';
	protected $theme_theme = '';
	protected $theme_align = '';
	protected $theme_align_bulma_class = 'has-text-centered';
	protected $theme_width = '';
	protected $theme_background = '';
 
    /**
     * @return string
     */
    public function getThemeWidth ()
    {
        return $this->theme_width;
    }

    /**
     * @param string $theme_width
     * @return ViewElement|mixed
     */
    public function setThemeWidth ($theme_width)
    {
        $this->theme_width = $theme_width;
        return $this;
    }

	/**
	 * @return string
	 */
	public function getThemeSize ()
	{
		return $this->theme_size;
	}

	/**
	 * @param string $theme_size
	 * @return ViewElement|mixed
	 */
	public function setThemeSize ($theme_size)
	{
		$this->theme_size = $theme_size;
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function getThemeBackground ()
	{
	    // Another hack to ensure specificly transparent cards return no-bg
        // and override any images
	    if ($this->theme_background == 'transparent') {
	        return 'no-bg';
        }

	    // Hack to stop setting no-bg if we have a background image set
        // As this will prevent overlay from being drawn
        // TODO: Should be dealt with in css instead of code hack
	    if ($this->theme_background == 'no-bg') {
	        if (!empty($this->background_image_id)
                || !empty($this->background_image)
            ) {
	            return '';
            }
        }

		return $this->theme_background;
	}
	
	/**
	 * @param string $theme_background
	 * @return mixed|ViewElement|hasTheme
	 */
	public function setThemeBackground ($theme_background)
	{
	    if (empty($theme_background) || in_array($theme_background, ['none', 'default'])) {
	        $this->theme_background = 'no-bg';
        }
        else {
            $this->theme_background = $theme_background;
        }
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getThemeClass ()
	{
		return $this->theme_class;
	}
	
	/**
	 * @param mixed $class
	 * @return mixed|ViewElement|hasTheme
	 */
	public function setThemeClass ($class)
	{
		$this->theme_class = $class;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getThemeTheme ()
	{
		return $this->theme_theme;
	}
	
	/**
	 * @param mixed $theme
	 * @return mixed|ViewElement|hasTheme
	 */
	public function setThemeTheme ($theme)
	{
		$this->theme_theme = $theme;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getThemeAlign ()
	{
		return $this->theme_align;
	}
	
	/**
	 * @param mixed $align
	 * @return mixed|ViewElement|hasTheme
	 */
	public function setThemeAlign ($align)
	{
		$this->theme_align = $align;

		switch ($align) {
            case 'left':
                $this->theme_align_bulma_class = 'has-text-left';
                break;
            case 'right':
                $this->theme_align_bulma_class = 'has-text-right';
                break;
            case 'center':
                $this->theme_align_bulma_class = 'has-text-centered';
                break;
        }

		return $this;
	}
}
