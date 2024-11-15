<?php

namespace Torpedo\Wp\ViewElements\Traits;

use Torpedo\Wp\ViewElements\ViewElement;

trait hasDate
{
	protected $post_date = null;
	
	/**
	 * @return mixed
	 */
	public function getPostDate ()
	{
		return $this->post_date;
	}
	
	/**
	 * @param mixed $post_date
	 * @return mixed|ViewElement|hasDate
	 */
	public function setPostDate ($post_date)
	{
		$this->post_date = $post_date;
		return $this;
	}
}
