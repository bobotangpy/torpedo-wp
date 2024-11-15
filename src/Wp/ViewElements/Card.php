<?php

namespace Torpedo\Wp\ViewElements;

use Torpedo\Wp\AcfHelper;
use Torpedo\Wp\ImageHelper;
use Torpedo\Wp\Posts\CardListable;
use Torpedo\Wp\Posts\Post;
use Torpedo\Wp\ViewElements\Interfaces\isCard;
use Torpedo\Wp\ViewElements\Traits\hasBackgroundImage;
use Torpedo\Wp\ViewElements\Traits\hasData;
use Torpedo\Wp\ViewElements\Traits\hasImage;
use Torpedo\Wp\ViewElements\Traits\hasSubCta;
use Torpedo\Wp\ViewElements\Traits\hasText;
use Torpedo\Wp\ViewElements\Traits\hasAuthor;
use Torpedo\Wp\ViewElements\Traits\hasCta;
use Torpedo\Wp\ViewElements\Traits\hasDate;
use Torpedo\Wp\ViewElements\Traits\hasTheme;
use Torpedo\Wp\ViewElements\Traits\hasTitle;
use Torpedo\Utils;
use Torpedo\Wp\ViewElements\Traits\hasVideo;

class Card extends ViewElement implements isCard
{
    use hasTheme;
	use hasTitle;
    use hasText;
	use hasAuthor;
	use hasData;
    use hasDate;
    use hasImage;
    use hasBackgroundImage;
	use hasCta;
	use hasSubCta;
	use hasVideo;

    protected $template = 'elements/cards/default';
    
	protected $show_social_share = false;

    /**
     * return Card
     */
    public static function create()
    {
        return new Card();
    }

    /**
     * @param Post $post
     * @param string $prefix
     * @param AcfHelper $acf
     * @return ViewElement|Card
     */
	static public function doCreateFromFields(Post $post, $prefix = '', AcfHelper $acf)
	{
        $cardPost = $acf->getPostFromField('post');

        if ($cardPost) {
            $card = static::createFromPost($cardPost);
        } else {
            $card = static::create()
                ->setTitle($acf->get('title'))
                ->setText($acf->get('text'))
                ->setImageById($acf->get('image'))
                ->setBackgroundImageById( $acf->get('image') )
            ;
        }

        return $card;
	}
    
    /**
     * @param CardListable $post
     * @return Card
     */
    static function createFromPost (CardListable $post)
    {
        return $post->getCardView();
    }
	
	/**
	 * @return mixed
	 */
	public function getShowSocialShare ()
	{
		return $this->show_social_share;
	}
	
	/**
	 * @param mixed $show_social_share
	 * @return Card
	 */
	public function setShowSocialShare ($show_social_share)
	{
		$this->show_social_share = $show_social_share;
		return $this;
	}

	public function getBestImageSize()
    {
        return ImageHelper::IMAGE_SIZE_LARGE;
    }

    public function getBestBackgroundImageSize()
    {
        switch ($this->template) {
            case 'elements/cards/blog-post':
                return ImageHelper::IMAGE_SIZE_CARD_SMALL;
            default:
                break;
        }

        if (in_array($this->getThemeWidth(), ['is-full', 'full']) ) {
            return ImageHelper::IMAGE_SIZE_CARD_LARGE;
        } else {
            return ImageHelper::IMAGE_SIZE_CARD_MEDIUM;
        }
    }

}

