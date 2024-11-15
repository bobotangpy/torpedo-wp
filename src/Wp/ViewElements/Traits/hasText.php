<?php

namespace Torpedo\Wp\ViewElements\Traits;

use Torpedo\Wp\ViewElements\ViewElement;

trait hasText
{
	protected $text = '';
	
	/**
	 * @return mixed
	 */
	public function getText ()
	{
		return $this->text;
	}
	
	/**
	 * @param mixed $text
	 * @return mixed|ViewElement|hasText
	 */
	public function setText ($text)
	{
		$this->text = $text;
		return $this;
	}
	
}
