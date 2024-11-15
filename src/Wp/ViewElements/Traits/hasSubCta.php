<?php

namespace Torpedo\Wp\ViewElements\Traits;


use Torpedo\Wp\ViewElements\SubCta;
use Torpedo\Wp\ViewElements\ViewElement;

trait hasSubCta
{
    /** @var SubCta */
    protected $sub_cta;
    
    /**
     * @return SubCta
     */
    public function getSubCta ()
    {
        return $this->sub_cta;
    }
    
    /**
     * @param SubCta|null $cta
     * @return mixed|ViewElement|hasCta
     */
    public function setSubCta ($cta)
    {
        $this->sub_cta = $cta;
        return $this;
    }
}
