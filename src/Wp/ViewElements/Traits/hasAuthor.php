<?php

namespace Torpedo\Wp\ViewElements\Traits;

use Torpedo\Wp\ImageHelper;
use Torpedo\Wp\ViewElements\ViewElement;

trait hasAuthor
{
	protected $author_image = '';
	protected $author_name = '';
	
	/**
	 * @return mixed
	 */
	public function getAuthorImage ()
	{
		return $this->author_image;
	}
	
	/**
	 * @param mixed $author_image
	 * @return mixed|ViewElement|hasAuthor
	 */
	public function setAuthorImage ($author_image)
	{
		$this->author_image = $author_image;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getAuthorName ()
	{
		return $this->author_name;
	}
	
	/**
	 * @param mixed $author_name
	 * @return mixed|ViewElement|hasAuthor
	 */
	public function setAuthorName ($author_name)
	{
		$this->author_name = $author_name;
		return $this;
	}
	
	/**
	 * Populates all other author fields from author id
	 *
	 * @param $authorId
	 * @return $this
	 * @return mixed|ViewElement|hasAuthor
	 */
	public function populateAuthorById($authorId)
	{
		$user = get_user_by('ID', $authorId);
		$this->setAuthorName($user->display_name);
		
		// TODO: Set author image or a default

        $authorImage = get_field('author_image', "user_$authorId");

        $this->setAuthorImage(
            ImageHelper::getUrlOrDefault($authorImage, $authorId, ImageHelper::IMAGE_SIZE_THUMBNAIL, 'author')
        );
		
		return $this;
	}
}
