<?php


namespace Torpedo\Wp\ViewElements;


use Torpedo\Wp\AcfHelper;
use Torpedo\Wp\Posts\Post;

class SubCta extends Cta
{
    protected $template       = 'elements/cta/sub-cta';

    protected $text  = '';
    protected $url   = '';
    protected $theme = 'sub-cta';
    protected $size  = 'default';

    /**
     * return SubCta
     */
    public static function create()
    {
        return new SubCta();
    }
}
