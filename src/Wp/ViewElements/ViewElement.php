<?php

namespace Torpedo\Wp\ViewElements;


use Torpedo\Utils;
use Torpedo\Wp\AcfHelper;
use Torpedo\Wp\Posts\Post;

abstract class ViewElement
{
	/**
	 * A prefix string that will be added to the beginning of each variable
	 * which is passed into the template
	 * @var string
	 */
	protected $templatePrefix = 'view_';
	
	/**
	 * Slug of the template which will be used for this view element
	 * @var string
	 */
	protected $template = '';
	
	abstract static public function create();
    
	static public function createFromArray(array $array)
    {
        $element = static::create();
        foreach($array as $key => $value) {
            $element->$key = $value;
        }
    }

    /**
     * @todo I don't like the way that acf prefix has to be determined in this way
     * @todo it should be an object property like template & template prefix
     *
     * @return string
     */
    static protected function getDefaultAcfPrefix()
    {
        return '';
    }
    
    /**
     * @param Post|int $post        Either a child of Post object or a wp post id
     * @param string   $acfPrefix   Prefix string that determines which acf field names to use to populate
     * @return ViewElement|mixed    The new ViewElement (or child class) that has been created
     */
	static public function createFromFields($post, $acfPrefix = null)
	{
        if (is_numeric($post)) {
            $post = Post::createFromId($post);
        }

        if (get_class($post) == \WP_Post::class) {
            $post = Post::create($post);
        }
        
        $acfPrefix = $acfPrefix ?? static::getDefaultAcfPrefix();
        
        $acf = new AcfHelper($acfPrefix, $post->getId());
        
        $element = static::doCreateFromFields($post, $acfPrefix, $acf);
        
        return $element;
	}

    /**
     * Creates an instance of this ViewElement and populates from ACF fields
     * @param Post      $post
     * @param string    $prefix
     * @param AcfHelper $acf
     * @return ViewElement
     * @TODO: Implement generic solution
     */
	abstract static protected function doCreateFromFields(Post $post, $prefix = '', AcfHelper $acf);

    
	public function getData()
	{
		$out = [];
		$out['_template'] = $this->getTemplate();

		$vars = get_object_vars($this);
		
		unset($vars['prefix']);

		$this->walkDataArray($vars, $out);

		return $out;
	}

    /**
     * @param $vars
     * @param $out
     * @param bool $ignoreGetters  If set to true, will not check for existence of getter methods when getting value. Can avoid situations where a sub field of a repeater clashes with a top level
     */
	public function walkDataArray(&$vars, &$out, $ignoreGetters = false)
    {
        array_walk($vars, function(&$value, $key) use (&$out, $ignoreGetters) {

            if (!$ignoreGetters) {
                $getter = 'get' . Utils::toUpperCamelCase($key);

                if (method_exists($this, $getter)) {
                    $value = $this->$getter();
                }
            }

            if (is_array($value)) {
                $var = [];

                $firstElement = reset($value);

                if (is_subclass_of($firstElement, ViewElement::class)) {
                    $this->walkDataArray($value, $var);
                } else {
                    $this->walkDataArray($value, $var, true);
                }


                $value = $var;
            }

            if (is_subclass_of($value, ViewElement::class)) {
                /** @var ViewElement $value */
                $value = $value->getData();
            }

            $out[$this->templatePrefix.$key] = $value;
        });
    }

	/**
	 * @return string
	 */
	public function getTemplate (): string
	{
		return $this->template;
	}
	
	/**
	 * @param string $template
	 * @return ViewElement|mixed
	 */
	public function setTemplate (string $template): ViewElement
	{
		$this->template = $template;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getTemplatePrefix (): string
	{
		return $this->templatePrefix;
	}
	
	/**
	 * @param string $templatePrefix
	 * @return mixed|ViewElement
	 */
	public function setTemplatePrefix (string $templatePrefix)
	{
		$this->templatePrefix = $templatePrefix;
		return $this;
	}
}

