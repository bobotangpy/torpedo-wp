<?php

namespace Torpedo\Wp\ViewElements\Traits;

use Torpedo\Wp\ViewElements\ViewElement;

trait hasTitle
{
	protected $title = '';
	protected $subtitle = '';
	
	/**
	 * @return mixed
	 */
	public function getTitle ()
	{
		return $this->title;
	}
	
	/**
	 * @param mixed $title
	 * @return mixed|ViewElement|hasTitle
	 */
	public function setTitle ($title)
	{
		$this->title = $title;
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getSubtitle ()
	{
		return $this->subtitle;
	}
	
	/**
	 * @param mixed $subtitle
	 * @return mixed|ViewElement|hasTitle
	 */
	public function setSubtitle ($subtitle)
	{
		$this->subtitle = $subtitle;
		return $this;
	}
	
}
