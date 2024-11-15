<?php


namespace Torpedo\Wp;


use Torpedo\Wp\Posts\CardListable;
use Torpedo\Wp\Posts\Post;

class AcfHelper
{
	protected $fieldPrefix = '';
	protected $postId = null;
 
	public function __construct($fieldPrefix, $postId)
    {
        $this->fieldPrefix = $fieldPrefix;
        $this->postId = $postId;
    }
   	/**
	 * @return string
	 */
	public function getFieldPrefix (): string
	{
		return $this->fieldPrefix;
	}
	
	/**
	 * @param string $fieldPrefix
	 */
	public function setFieldPrefix (string $fieldPrefix): void
	{
        $this->fieldPrefix = $fieldPrefix;
	}
	
	/**
	 * @return null
	 */
	public function getPostId ()
	{
        return $this->postId;
	}

	/**
	 * @param null $postId
	 */
	public function setPostId ($postId): void
	{
        $this->postId = $postId;
	}
	
	public function get($field, $default = null)
	{
		$val = get_field($this->fieldPrefix.$field, $this->postId);
		
		if ($default === null) {
			return $val;
		}
		
		if ($val === null) {
			return $default;
		}
		
		return $val;
	}

    /**
     * @param string $field
     * @return CardListable|Post
     */
	public function getPostFromField($field)
    {
        $post = $this->get($field);

        if (empty($post)) {
            return null;
        }

        if (is_numeric($post)) {
            //return Post::createFromId($post);
            return Post::createFromId($post);
        } else {
            //return Post::create($post);
            return Post::create($post);
        }
    }
}

