<?php
/**
 * [Class Description]
 *
 * @author     John McCann
 */


namespace Torpedo\Wp\Posts;


class PostTypeHelper
{
	const SUPPORTS_TITLE           = 'title';
	const SUPPORTS_EDITOR          = 'editor';
	const SUPPORTS_COMMENTS        = 'comments';
	const SUPPORTS_REVISIONS       = 'revisions';
	const SUPPORTS_TRACKBACKS      = 'trackbacks';
	const SUPPORTS_AUTHOR          = 'author';
	const SUPPORTS_EXCERPT         = 'excerpt';
	const SUPPORTS_PAGE_ATTRIBUTES = 'page-attributes';
	const SUPPORTS_THUMBNAIL       = 'thumbnail';
	const SUPPORTS_CUSTOM_FIELDS   = 'custom-fields';
	const SUPPORTS_POST_FORMATS    = 'post-formats';


    protected $name;

    protected $parameters = array ();

    /**
     * @author John McCann
     * @param $name
     * @return PostTypeHelper
     */
    public static function createPostType ($name)
    {
        $postType = new PostTypeHelper();
        $postType->setName($name);
        return $postType;
    }

    public static function getLabelsArray ($singular, $plural = '')
    {
        if (empty($plural)) {
            $plural = $singular . 's';
        }

        return array (
	        'name'               => $plural,
	        'singular_name'      => $singular,
	        'menu_name'          => $plural,
	        'name_admin_bar'     => $singular,
	        'add_new'            => 'Add New',
	        'add_new_item'       => 'Add New ' . $singular,
	        'new_item'           => 'New ' . $singular,
	        'edit_item'          => 'Edit ' . $plural,
	        'view_item'          => 'View ' . $singular,
	        'all_items'          => 'All ' . $plural,
	        'search_items'       => 'Search ' . $plural,
	        'parent_item_colon'  => 'Parent ' . $plural,
	        'not_found'          => 'No ' . ucfirst($plural) . ' found.',
	        'not_found_in_trash' => 'No ' . ucfirst($plural) . ' found in Trash.',
        );
    }

	public static function getTaxonomyLabelsArray ($singular, $plural = '')
	{
		return [
			'name'              => $singular,
			'singular_name'     => $singular,
			'search_items'      => 'Search '.$plural,
			'popular_items'     => 'Popular '.$plural,
			'all_items'         => 'All '.$plural,
			'parent_item'       => 'Parent '.$singular,
			'parent_item_colon' => 'Parent '.$singular,
			'edit_item'         => 'Edit '.$singular,
			'update_item'       => 'Update '.$singular,
			'add_new_item'      => 'Add New '.$singular,
			'new_item_name'     => 'New '.$singular.' Name',
		];
	}


    public function __construct ()
    {

    }

    public function register ()
    {
        register_post_type($this->name, $this->parameters);
    }


    public function setName ($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Name of the post type shown in the menu. Usually plural.
     * Default is value of $labels['name'].
     */
    public function setLabel ($string = '')
    {
        $this->parameters['label'] = $string;
        return $this;
    }


    /**
     * An array of labels for this post type. If not set, post
     * labels are inherited for non-hierarchical types and page
     * labels for hierarchical ones. See get_post_type_labels() for a full
     * list of supported labels.
     */
    public function setLabels ($labels = array ())
    {
        $this->parameters['labels'] = $labels;
        return $this;
    }

    /**
     * A short descriptive summary of what the post type is.
     *
     * @param string $text
     * @return $this
     */
    public function setDescription ($text = '')
    {
        $this->parameters['description'] = $text;
        return $this;
    }

    public function setHierarchical ($bool = false)
    {
        $this->parameters['hierarchical'] = $bool;
        return $this;
    }

    /**
     * Whether a post type is intended for use publicly either via
     * the admin interface or by front-end users. While the default
     * settings of $exclude_from_search, $publicly_queryable, $show_ui,
     * and $show_in_nav_menus are inherited from public, each does not
     * rely on this relationship and controls a very specific intention.
     *
     * @author John McCann
     * @param bool $isPublic
     * @return $this
     */
    public function setPublic ($isPublic = false)
    {
        $this->parameters['public'] = $isPublic;
        return $this;
    }

    /**
     * Whether to exclude posts with this post type from front end search
     * results. Default is the opposite value of $public.
     */
    public function setExcludeFromSearch ($bool = true)
    {
        $this->parameters['exclude_from_search'] = $bool;
        return $this;
    }

    /**
     * Whether queries can be performed on the front end for the post type
     * as part of parse_request(). Endpoints would include:
     * * ?post_type={post_type_key}
     * * ?{post_type_key}={single_post_slug}
     * * ?{post_type_query_var}={single_post_slug}
     * If not set, the default is inherited from $public.
     */
    public function setPubliclyQueryable ($bool = false)
    {
        $this->parameters['publicly_queryable'] = $bool;
        return $this;
    }

    /**
     * Whether to generate and allow a UI for managing this post type in the
     * admin. Default is value of $public.
     */
    public function setShowUI ($bool = false)
    {
        $this->parameters['show_ui'] = $bool;
        return $this;
    }

    /**
     * Where to show the post type in the admin menu. To work, $show_ui
     * must be true. If true, the post type is shown in its own top level
     * menu. If false, no menu is shown. If a string of an existing top
     * level menu (eg. 'tools.php' or 'edit.php?post_type=page'), the post
     * type will be placed as a sub-menu of that.
     * Default is value of $show_ui.
     */
    public function setShowInMenu ($bool = false)
    {
        $this->parameters['show_in_menu'] = $bool;
        return $this;
    }

    /**
     * Makes this post type available for selection in navigation menus.
     * Default is value $public.
     */
    public function setShowInNavMenus ($bool = false)
    {
        $this->parameters['show_in_nav_menus'] = $bool;
        return $this;
    }


    /**
     *  Makes this post type available via the admin bar. Default is value
     * of $show_in_menu.
     */
    public function setShowInAdminBar ($bool = false)
    {
        $this->parameters['show_in_admin_bar'] = $bool;
        return $this;
    }

    /**
     * Whether to add the post type route in the REST API 'wp/v2' namespace.
     */
    public function setShowInRest ($bool = false)
    {
        $this->parameters['show_in_rest'] = $bool;
        return $this;
    }

    /**
     * To change the base url of REST API route. Default is $post_type.
     */
    public function setRestBase ($name)
    {
        $this->parameters['rest_base'] = $name;
        return $this;
    }

    /**
     * $rest_controller_class REST API Controller class name. Default is 'WP_REST_Posts_Controller'.
     */
    public function setRestControllerClass ($controllerClass)
    {
        $this->parameters['rest_controller_class'] = $controllerClass;
        return $this;
    }

    /**
     * The position in the menu order the post type should appear. To work,
     * $show_in_menu must be true. Default null (at the bottom).
     */
    public function setMenuPosition ($position = null)
    {
        $this->parameters['menu_position'] = $position;
        return $this;
    }

    /**
     * The url to the icon to be used for this menu. Pass a base64-encoded
     * SVG using a data URI, which will be colored to match the color scheme
     * -- this should begin with 'data:image/svg+xml;base64,'. Pass the name
     * of a Dashicons helper class to use a font icon, e.g.
     * 'dashicons-chart-pie'. Pass 'none' to leave div.wp-menu-image empty
     * so an icon can be added via CSS. Defaults to use the posts icon.
     */
    public function setMenuIcon ($icon)
    {
        $this->parameters['menu_icon'] = $icon;
        return $this;
    }

    /**
     *  The string to use to build the read, edit, and delete capabilities.
     *  May be passed as an array to allow for alternative plurals when using
     * this argument as a base to construct the capabilities, e.g.
     *  array('story', 'stories'). Default 'post'.
     */
    public function setCapabilityType ($string)
    {
        $this->parameters['capability_type'] = $string;
        return $this;
    }

    /**
     * Array of capabilities for this post type. $capability_type is used
     * as a base to construct capabilities by default.
     * See get_post_type_capabilities().
     */
    public function setCapabilities (array $capabilities)
    {
        $this->parameters['capabilities'] = $capabilities;
        return $this;
    }

    /**
     * Whether to use the internal default meta capability handling.
     * Default false.
     */
    public function setMapMetaCap ($bool = false)
    {
        $this->parameters['map_meta_cap'] = $bool;
        return $this;
    }

    /**
     * Core feature(s) the post type supports. Serves as an alias for calling
     * add_post_type_support() directly. Core features include 'title',
     * 'editor', 'comments', 'revisions', 'trackbacks', 'author', 'excerpt',
     * 'page-attributes', 'thumbnail', 'custom-fields', and 'post-formats'.
     * Additionally, the 'revisions' feature dictates whether the post type
     * will store revisions, and the 'comments' feature dictates whether the
     * comments count will show on the edit screen. Defaults is an array
     * containing 'title' and 'editor'.
     */
    public function setSupports (
        $array = array(self::SUPPORTS_TITLE, self::SUPPORTS_EDITOR))
    {
        $this->parameters['supports'] = $array;
        return $this;
    }

    /**
     * Provide a callback function that sets up the meta boxes for the
     * edit form. Do remove_meta_box() and add_meta_box() calls in the
     * callback. Default null.
     */
    public function setRegisterMetaBoxCallback (callable $callback = null)
    {
        $this->parameters['register_meta_box_cb'] = $callback;
        return $this;
    }

    /**
     * An array of taxonomy identifiers that will be registered for the
     * post type. Taxonomies can be registered later with register_taxonomy()
     * or register_taxonomy_for_object_type().
     * Default empty array.
     */
    public function setTaxonomies ($array = array ())
    {
        $this->parameters['taxonomies'] = $array;
        return $this;
    }

    /**
     * Whether there should be post type archives, or if a string, the
     * archive slug to use. Will generate the proper rewrite rules if
     *  $rewrite is enabled. Default false.
     */
    public function setHasArchive ($bool = false)
    {
        $this->parameters['has_archive'] = $bool;
        return $this;
    }


    /**
     * Triggers the handling of rewrites for this post type. To prevent rewrite, set to false.
     * Defaults to true, using $post_type as slug. To specify rewrite rules, an array can be
     * passed with any of these keys:
     *
     * @type string $slug       Customize the permastruct slug. Defaults to $post_type key.
     * @type bool   $with_front Whether the permastruct should be prepended with WP_Rewrite::$front.
     *                          Default true.
     * @type bool   $feeds      Whether the feed permastruct should be built for this post type.
     *                          Default is value of $has_archive.
     * @type bool   $pages      Whether the permastruct should provide for pagination. Default true.
     * @type const  $ep_mask    Endpoint mask to assign. If not specified and permalink_epmask is set,
     *                          inherits from $permalink_epmask. If not specified and permalink_epmask
     *                          is not set, defaults to EP_PERMALINK.
     */
    public function setRewrite($rewrite) {
        $this->parameters['rewrite'] = $rewrite;
        return $this;
    }


    /**
     * Sets the query_var key for this post type. Defaults to $post_type
     * key. If false, a post type cannot be loaded at
     * ?{query_var}={post_slug}. If specified as a string, the query
     * ?{query_var_string}={post_slug} will be valid.
     */
    public function setQueryVar ($queryVar)
    {
        $this->parameters['query_var'] = $queryVar;
        return $this;
    }


    /**
     * Whether to allow this post type to be exported. Default true.
     */
    public function setCanExport ($bool = true)
    {
        $this->parameters['can_export'] = $bool;
        return $this;
    }

    /**
     * Whether to delete posts of this type when deleting a user. If true,
     * posts of this type belonging to the user will be moved to trash
     * when then user is deleted. If false, posts of this type belonging
     * to the user will *not* be trashed or deleted. If not set (the default),
     * posts are trashed if post_type_supports('author'). Otherwise posts
     * are not trashed or deleted. Default null.
     */
    public function setDeleteWithUser ($bool = false)
    {
        $this->parameters['delete_with_user'] = $bool;
        return $this;
    }

}
