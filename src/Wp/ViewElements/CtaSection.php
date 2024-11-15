<?php

namespace Torpedo\Wp\ViewElements;

use Torpedo\Wp\AcfHelper;
use Torpedo\Wp\Posts\Post;
use Torpedo\Wp\ViewElements\Traits\hasCta;
use Torpedo\Wp\ViewElements\Traits\hasTheme;

class CtaSection extends ViewElement
{
    use hasTheme;
    use hasCta;

    protected $template = 'elements/sections/cta';

    /**
     * @return CtaSection
     */
    static public function create()
    {
        return new CtaSection();
    }

    protected static function doCreateFromFields(Post $post, $prefix = '', AcfHelper $acf)
    {
        $ctaSection = new CtaSection();

        $ctaSection
            ->setCta( Cta::createFromFields($post, $prefix.'cta_') )
            ->setThemeBackground( $acf->get('theme_background')
        );

        return $ctaSection;
    }

    protected static function doCreateFromVars($background, $ctaText, $ctaUrl, $ctaTheme = 'cta', $ctaSize = 'default')
    {
        $ctaSection = new CtaSection();

        $ctaSection
            ->setCta(
                Cta::createFromVars($ctaText, $ctaUrl, $ctaTheme, $ctaSize)
            )
            ->setThemeBackground( $background );

        return $ctaSection;
    }
}

