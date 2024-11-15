<?php

namespace Torpedo\Wp\Posts;

use Torpedo\Utils;

use Torpedo\Wp\ImageHelper;
use Torpedo\Wp\ViewElements\Card;
use Torpedo\Wp\ViewElements\Cta;
use Torpedo\Wp\ViewElements\SubCta;
use WPSEO_Primary_Term; // Yoast Namespace
use WP_Post;

/**
 * [Class Description]
 *
 * @author     John McCann
 */
class Post implements CardListable
{
	const POST_TYPE_NAME = 'post';

	/**
     * The total number of posts that exist in wp for this post type
     * @var $totalPosts
     */
	static $totalPostPages;

	/** @var \WP_Post */
	protected $post;
	
	/**
     * Returns the most appropriate Post object based on the post type name.
	 * @param \WP_Post $post
	 * @return Post|CardListable
	 */
	public static function create(\WP_Post $post)
	{
        $class = self::determinePostClass($post);
        return new $class($post);
	}

	protected static function determinePostClass(\WP_Post $post)
    {
        if ($post->post_type == 'post') {
            return static::class;
        }

        if ($post->post_type == 'page') {
            return static::class;
        }

        $postClassName = Utils::toUpperCamelCase($post->post_type);

        // TODO: Namespaces must be registered
        $namespaces = ['Torpedo\\Wp\\Posts\\'];

        foreach ($namespaces as $namespace) {
            $class = $namespace . $postClassName;
            if (class_exists($class)) {
                //return call_user_func([$class, 'create'], $post);
                return $class;
            }
        }

        return static::class;
    }
	
    public static function createFromId($id)
    {
        $post = get_post($id);
        return static::create($post);
    }
	
	public static function createFromName($name)
	{
		$post = get_page_by_path($name, OBJECT, static::POST_TYPE_NAME);
		return ($post)
            ? static::create($post)
            : null;
	}

    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param null $offset
     * @param null $perPage
     * @param string $orderBy
     * @param string $order
     * @return Post[]
     */
    public static function getPosts($page = 1, $perPage = 6, $orderBy = 'date', $order = 'DESC', $terms = [], $excludePostIds = [])
    {
        $args = [
            'post_type'      => static::POST_TYPE_NAME,
            'paged'          => $page,
            'posts_per_page' => $perPage,
            'orderby'        => $orderBy,
            'order'          => $order,
        ];

        if (!empty($excludePostIds)) {
            $args['post__not_in'] = $excludePostIds;
        }

        // TODO: Tax query

        // $posts = get_posts($args);

        return self::getPostsArgs($args);
    }

    /**
     * @param $args
     * @return Post[]
     */
    public static function getPostsArgs($args)
    {
        $query = new \WP_Query($args);
        $posts = $query->get_posts();

        static::$totalPostPages = $query->max_num_pages;

        $out = [];
        foreach ($posts as &$post) {
            $out[] = static::create($post);
        }

        return $out;
    }

    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////

    protected function __construct(\WP_Post $post)
    {
        $this->post = $post;
    }

    ////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return WP_Post
     */
    public function getWpPostObject()
    {
        return $this->post;
    }

	// TODO: Refactor out all ACF/meta data to a class which is used for both post and section
	
    public function getField($field, $default = null)
    {
        $value = get_field($field, $this->post->ID);
        
        if ($value) {
            return $value;
        }
        
        return $default;
    }
    
    public function hasField($fieldName)
    {
        $value = $this->getField($fieldName);
        return (!empty($value));
    }
    
    public function getAllFields()
    {
    	$output = get_fields($this->post->ID);
	    return $output;
    }
    
    public function getFieldOverride($fieldName, $optionField)
    {
        $output = $this->getField($fieldName);
        if (empty($output)) {
            $output = get_field($optionField, 'option');
        }
        return $output;
    }
    
    public function getDateField($field, $sourceFormat = 'd/m/Y')
    {
	    $value = get_field($field, $this->post->ID);
	    $date = \DateTime::createFromFormat($sourceFormat, $value);
	    return $date;
    }
	
	////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////
	
	public function getId()            { return $this->post->ID; }
    public function getTitle()         { return $this->post->post_title; }
    public function getSlug()          { return $this->post->post_name; }
	public function getPostType()      { return $this->post->post_type; }
	public function getPostTypeLabel() { return ucfirst($this->post->post_type); }
	
	public function getPermalink()
	{
//		if (function_exists('icl_object_id')) {
//			$link = apply_filters('wpml_permalink', get_permalink($this->post->ID), wpml_get_current_language() );
//			return $link;
//		}
//		global $sitepress;
//		$sitepress->get_object_id($this->post->ID, $this->getPostType());
		return get_permalink($this->post->ID);
	}
	
	public function getPermalinkLabel()
    {
        return 'See the full story';
    }
		
    public function getCategoryName()
    {
    	$cats = get_the_category($this->post->ID);
	    
	    if (empty($cats) || !is_array($cats)) {
		    return '';
	    }
    	return $cats[0]->name;
    }

    public function getAvailableTerms()
    {
    	return Site::main()->getAvailableTerms(['post'], ['category']);
	}


    /**
     * Returns term (Primary term if Yoast is installed)
     * @param string $taxonomy
     * @param bool   $preferChildTerms   If multiple terms are returned, return a child term rather than a parent
     * @return \WP_Term
     */
    public function getTerm($taxonomy = 'post_tag', $preferChildTerms = false)
    {
        $terms = wp_get_post_terms($this->post->ID, $taxonomy);

        if (empty($terms) || !is_array($terms)) {
            return null;
        }

        // Show Yoast primary category, or default to normal first cat
        if ((is_plugin_active('wordpress-seo/wp-seo.php'))
            && (@include_once(WP_PLUGIN_DIR . '/wordpress-seo/inc/class-wpseo-primary-term.php')))
        {
            $wpseo_primary_term = new WPSEO_Primary_Term($taxonomy, $this->post->ID);
            $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
            $term = get_term($wpseo_primary_term);

            if (!is_wp_error($term)) {
                return $term;
            }
        }

        if (count($terms) > 1 && $preferChildTerms) {
            foreach ($terms as $term) { // Default
                if ($term->parent == 0) {
                    continue;
                }
                return $term;
            }
        } else {
            return $terms[0];
        }
    }
 
	public function getAuthorId()
	{
		return $this->post->post_author;
	}
	
    public function getAuthorName()
	{
		$user = get_user_by('ID', $this->post->post_author);
		return ucfirst($user->display_name);
	}
	
	public function getAuthorPageLink()
	{
		return get_the_author_posts_link();
	}
	

	public function getDate($format = 'F j, Y')
	{
		$date = \DateTime::createFromFormat('Y-m-d H:i:s', $this->post->post_date);
		return $date->format($format);
	}
	
	
	public function getThumbnailId($useDefaultIfMissing = true)
	{
        $thumbnail = get_post_thumbnail_id($this->post->ID);

        if (empty($thumbnail) && $useDefaultIfMissing) {
            return ImageHelper::getDefaultImageId($this->post->ID, 'post');
        }

		return $thumbnail;
	}
	
	
	/**
	 * Returns summary text about the post. Will be either an excerpt if
	 * available or post content if available
	 * @author John McCann
	 * @param null $maxChars
	 * @return bool|mixed|string
	 */
	public function getSummary($maxChars = null, $stripHtmlTags = false)
	{
		$summary = $this->getExcerpt($maxChars, $stripHtmlTags);
		
		if (empty($summary)) {
			$summary = $this->getContent($maxChars, $stripHtmlTags);
		}
		
		return $summary;
	}
	
	public function getExcerpt($maxChars = null, $stripHtmlTags = false)
	{
		$excerpt = $this->post->post_excerpt;
		
		$excerpt = apply_filters('the_excerpt', $excerpt);
		
		if (empty($excerpt)) {
			return '';
		}
		
		if ($stripHtmlTags) {
			$excerpt = wp_strip_all_tags($excerpt);
		}
		
		if ($maxChars) {
			$excerpt = Utils::limitString($excerpt, $maxChars, '&hellip;');
		}
		
		return $excerpt;
	}
	
	
    public function getContent($maxChars = null, $stripTags = false)
    {
        $content = apply_filters('the_content', $this->post->post_content);
	    
	    if (empty($content)) {
	    	return '';
	    }
	
	    if ($stripTags) {
		    $content = wp_strip_all_tags($content);
	    }
	    
	    $content = str_replace(']]>', ']]&gt;', $content);
	
	    if ($maxChars) {
		    $content = Utils::limitString($content, $maxChars, '&hellip;');
	    }

	    return $content;
    }

    /**
     * @param null $context
     * @return Card
     */
    public function getCardView($context = null)
    {
       
       $card = Card::create();
       
        return $card
            ->setTitle( $this->getTitle() )
            ->setSubtitle( $this->getTerm() )
            ->setText( $this->getSummary(200, true) )
            ->setBackgroundImageById( $this->getThumbnailId() )
            ->setCta( $this->getCtaView() )
            ->setSubCta( $this->getSubCtaView() )
        ;
    }

    public function getCtaView($context = null)
    {
        return Cta::create()
            ->setTheme('cta')
            ->setText( 'Read more')
            ->setUrl( $this->getPermalink() )
        ;
    }

    public function getSubCtaView($context = null)
    {
        $subCtaText = $this->getCategoryName();
        // TODO: Get category page url
        $subCtaUrl  = $this->getCategoryName();

        if (empty($subCtaText)) {
            $subCtaText = Utils::toHumanReadable($this->getPostType());
        }
        if (empty($subCtaUrl)) {
            $subCtaUrl = $this->getPermalink();
        }

        return SubCta::createFromVars($subCtaText, $subCtaUrl);
    }
}
