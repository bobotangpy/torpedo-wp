<?php

namespace Torpedo\Wp\ViewElements;

use Torpedo\Wp\AcfHelper;
use Torpedo\Wp\Posts\Post;
use Torpedo\Wp\ViewElements\Traits\hasCards;
use Torpedo\Wp\ViewElements\Traits\hasCta;
use Torpedo\Wp\ViewElements\Traits\hasTheme;
use Torpedo\Wp\ViewElements\Traits\hasTitle;

class CardList extends ViewElement
{
    use hasTheme;
	use hasTitle;
	use hasCta;
	use hasCards;

    protected $template = 'elements/sections/card-list';
    
    /**
     * return CardList
     */
    public static function create()
    {
        return new CardList();
    }

    /**
     * @param Post $post
     * @param string $prefix
     * @param AcfHelper $acf
     * @return ViewElement
     */
    static protected function doCreateFromFields(Post $post, $prefix = '', AcfHelper $acf)
    {
        $cardList = static::create()
            ->setTitle( $acf->get('title') )
            ->setThemeTheme( $acf->get('theme_theme') )
            ->setThemeClass( $acf->get('theme_css' ))
            ->setThemeBackground( $acf->get('theme_background' ))
            ->setThemeAlign( $acf->get('theme_align') )
            ->setThemeClass( $acf->get('theme_css') )
            ->setCta( Cta::createFromFields($post, $prefix.'cta_'))
        ;

        $repeater = $acf->get('cards', []);

        for ($i = 0; $i < count($repeater); $i++) {
            $row = $repeater[$i];

            $card = static::createCard($post, $prefix.'cards_'.$i.'_card_');

            /** Section level overrides */
            if ($templateOverride = self::determineTemplateOverride($acf, $row)) {
                $card->setTemplate( $templateOverride );
            }

            if ($themeOverride = self::determineThemeOverride($acf, $row)) {
                $card->setThemeTheme( $themeOverride );
            }

            if ($widthOverride = self::determineWidthOverride($acf, $row)) {
                $card->setThemeWidth( $widthOverride );
            }

            $cardList->addCard($card);
        }

        return $cardList;
    }



    static public function createCard(Post $post, $prefix)
    {
        $card = Card::CreateFromFields($post, $prefix);

        if (empty($card)) {
            $card = Card::create()
                ->setTitle($post->getTitle())
                ->setText($post->getSummary(150))
            ;
        }

        return $card;
    }

    /**
     * @param AcfHelper $acf
     * @param           $row
     * @return null|string
     */
    private static function determineTemplateOverride(AcfHelper $acf, $row)
    {
        if (!empty($row['card_template_template']) && !in_array($row['card_template_template'], ['none', 'default']) ) {
            return 'elements/cards/'.$row['card_template_template'];
        }
        else {
            $defaultTemplate = $acf->get('default_template_template', null);
            if (!in_array($defaultTemplate, ['none', 'default']) ) {
                return 'elements/cards/'.$defaultTemplate;
            }
        }
        return null;
    }

    private static function determineThemeOverride(AcfHelper $acf, $row)
    {
        if (!empty($row['card_theme']) && $row['card_theme'] != 'default') {
            return $row['card_theme'];
        }
        else {
            $defaultTheme = $acf->get('default_theme_theme', null);
            if ($defaultTheme != 'default') {
                return $defaultTheme;
            }
        }
        return null;
    }

    private static function determineWidthOverride(AcfHelper $acf, $row)
    {
        if (!empty($row['card_width']) && $row['card_width'] != 'default') {
            return $row['card_width'];
        }
        else {
            $defaultWidth = $acf->get('default_width_width', null);
            if ($defaultWidth != 'default') {
                return $defaultWidth;
            }
        }
        return null;
    }
}

