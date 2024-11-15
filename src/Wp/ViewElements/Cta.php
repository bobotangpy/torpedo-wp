<?php


namespace Torpedo\Wp\ViewElements;


use Torpedo\Utils;
use Torpedo\Wp\AcfHelper;
use Torpedo\Wp\Posts\CardListable;
use Torpedo\Wp\Posts\Post;

class Cta extends ViewElement
{
    protected $template = 'elements/cta/default';
    
	protected $text  = '';
	protected $url   = '';
	protected $theme = 'cta';
	protected $size  = 'default';
	protected $target = '';
	protected $is_fake_link = false;

	public static function createFromVars($text, $url, $theme = '', $size = '')
    {
        $cta = static::create();
        $cta->text = $text;
        $cta->url = $url;

        if ($theme) {
            $cta->theme = $theme;
        }
        if ($size) {
            $cta->size = $size;
        }
        return $cta;
    }

	/**
	 * return Cta
	 */
	public static function create()
	{
        return new Cta();
	}

    /**
     * @param Post   $post
     * @param string $prefix
     * @return ViewElement|Cta
     */
    public static function doCreateFromFields (Post $post, $prefix = '', AcfHelper $acf)
    {
        return static::create()
            ->setUrl($acf->get('url'))
            ->setText($acf->get('text'))
            ->setTheme($acf->get('theme'))
            ->setSize($acf->get('size'))
        ;
    }

    /**
     * @return bool
     */
    public function getIsFakeLink()
    {
        return $this->is_fake_link;
    }
	
    /**
     * @param bool $isFakeLink
     * @return Cta
     */
    public function setIsFakeLink($isFakeLink)
    {
        $this->is_fake_link = $isFakeLink;
        return $this;
    }
 
    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
	
    /**
     * @param string $target
     * @return Cta
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
	 * @return string
	 */
	public function getTheme ()
	{
		return $this->theme;
	}
	
	/**
	 * @param string $theme
	 * @return mixed|ViewElement
	 */
	public function setTheme ($theme)
	{
		$this->theme = $theme;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getSize ()
	{
		return $this->size;
	}
	
	/**
	 * @param string $size
	 * @return mixed|ViewElement
	 */
	public function setSize ($size)
	{
		$this->size = $size;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getText ()
	{
		return $this->text;
	}
	
	/**
	 * @param mixed $text
	 * @return mixed|ViewElement
	 */
	public function setText ($text)
	{
		$this->text = $text;
		return $this;
	}

	public function applyContactUsParameters()
    {

    }

	/**
	 * @return mixed
	 */
	public function getUrl ()
	{
	    // If we're linking to the contact page, cheekily insert a
        // parameter telling the page where we came from
	    if (strpos($this->url, '/contact-us') !== false) {
	        global $post;
	        return $this->url.'?contact-src='.$post->ID.'&contact-cta='.Utils::toUrlSlug($this->text);
        }

		return $this->url;
	}
	
	/**
	 * @param mixed $url
	 * @return mixed|ViewElement
	 */
	public function setUrl ($url)
	{
		$this->url = $url;
		return $this;
	}
}

