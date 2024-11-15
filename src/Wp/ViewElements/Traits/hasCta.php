<?php

namespace Torpedo\Wp\ViewElements\Traits;

use Torpedo\Wp\ViewElements\Cta;
use Torpedo\Wp\ViewElements\ViewElement;

trait hasCta
{
	/** @var Cta */
	protected $cta;
	
	/**
	 * @return Cta
	 */
	public function getCta ()
	{
		return $this->cta;
	}
	
	/**
	 * @param Cta $cta
	 * @return mixed|ViewElement|hasCta
	 */
	public function setCta ($cta)
	{
		$this->cta = $cta;
		return $this;
	}
}
