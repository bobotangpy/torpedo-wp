<?php

namespace Torpedo\Wp;

use Torpedo\Wp\ViewElements\ViewElement;
use Torpedo\Wp\Posts\Post;
use WP_Post;

class Template
{
    static protected $templatePrefix = '';

    /**
     * Renders a template with the passed view data
     *
     * viewData can be either an array, a wordpress post, or a viewElement object.
     *
     * @param array|WP_Post|Post|ViewElement $viewData
     * @param string|null                    $template
     * @param string                         $extraClasses
     * @param string                         $acfPrefix
     * @return string
     */
    static public function render($viewData, $template = null, $extraClasses = '', $acfPrefix = '')
    {
        if (is_null($viewData)) {
            return '';
        }

        switch (gettype($viewData)) {
            case 'object':

                if (is_subclass_of($viewData, ViewElement::class) ) {
                    return self::renderView($viewData, $template, $extraClasses);
                }
                else if (is_subclass_of($viewData, Post::class)) {
                    /** @var Post $viewData */
                    return self::renderPostSection($viewData->getWpPostObject(), $template, $extraClasses, $acfPrefix);
                }
                else if (is_subclass_of($viewData, WP_Post::class)) {
                    return self::renderPostSection($viewData, $template, $extraClasses, $acfPrefix);
                }
                else {
                    return '<p>Template object is not a subclass of ViewElement, Post or WP_Post</p>';
                }
                break;

            case 'array':
                if (isset($viewData['_template'])) {
                    if (!empty($template)) {
                        $viewData['_template'] = $template;
                    }
                    return self::getTemplatePartVars($viewData['_template'], $viewData, $extraClasses);
                }
                else {
                    return self::getTemplatePartVars($template, $viewData, $extraClasses);
                }
                break;

            default:
                return self::getTemplatePartVars($template, ['data'=>$viewData], $extraClasses);
        }
    }

	static public function renderTemplate($template, array $viewData = [])
	{
		try {
			if (empty($viewData)) {
				return self::getTemplatePart($template);
			} else {
				return self::getTemplatePartVars($template, $viewData);
			}
		}
		catch (\Exception $e) {
			return '<div>Error: '.$e->getMessage().'</div>';
		}
	}

	static private function renderPostSection(\WP_Post $post, $template, $extraClasses, $fieldPrefix = '')
    {
        $prefix = self::$templatePrefix;

        $data = [
            $prefix.'post_title'     => &$post->post_title,
            $prefix.'post_content'   => &$post->post_content,
            $prefix.'post_excerpt'   => &$post->post_excerpt,
            $prefix.'post_permalink' => get_permalink($post->ID),
        ];

        $fields = get_fields($post->ID);

        array_walk($fields, function(&$value, $key) use (&$data, $prefix) {
            $data[$prefix.$key] = $value;
        });

        return self::getTemplatePartVars($template, $data, $extraClasses);
    }
	
	/**
	 * @param ViewElement|array $viewElement
	 * @param string|null       $templateOverride
	 * @return string
	 */
	static private function renderView(ViewElement $viewElement, $templateOverride = null, $extraClasses = '')
	{
        $viewData = $viewElement->getData();

		$template = $templateOverride ?? $viewData['_template'];
	
		if (empty($template)) {
			return '<div>Template not found for '.get_class($viewElement).' view element</div>';
		}
		
		return self::getTemplatePartVars($template, $viewData, $extraClasses);
	}


	
	static private function getTemplatePart($slug)
	{
		ob_start();
		get_template_part($slug);
		return ob_get_clean();
	}
	
	/**
	 * @param       $slug
	 * @param ViewElement|array $viewData
	 * @return string
	 */
	static private function getTemplatePartVars($slug, $viewData, $extraClasses = '')
	{
	    // Remove this line
//		$data = (is_subclass_of($viewData, ViewElement::class))
//			? $viewData->getData()
//			: $viewData;

        $viewData['template_class'] = $extraClasses;

		$name = '';
		
		// Taken from standard get_template_part function
		\do_action("get_template_part_{$slug}", $slug, $name);
		
		$templates = array();
		$name = (string)$name;
		if ('' !== $name) {
			$templates[] = "{$slug}-{$name}.php";
		}
		$templates[] = "{$slug}.php";
		$template = \locate_template($templates, false, false);
		if (empty($template)) {
			return "<p>Template \"$templates[0]\" not found</p>";
		}
		
		// @see load_template (wp-includes/template.php) - these are needed for WordPress to work.
		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
		if (is_array($wp_query->query_vars)) {
			\extract($wp_query->query_vars, EXTR_SKIP);
		}
		if (isset($s)) {
			$s = \esc_attr($s);
		}
		// End standard WordPress behavior
		
		foreach ($viewData as $var => $value) {
			if (!self::isValidVariableName($var)) {
				trigger_error("Variable names must be valid. Skipping \"$var\" because it is not a valid variable name.");
				continue;
			}
			if (isset($$var)) {
				trigger_error("$var already existed, probably set by WordPress, so it wasn't set to $value like you wanted. Instead it is set to: " . print_r($$var, true));
				continue;
			}
			$$var = $value;
		}

        unset($viewData);
		
		ob_start();
		try {
            if (WP_ENV != 'production') {
                echo "<!-- $slug -->\n";
            }
            require $template;
        }
        catch (\Exception $e) {
		    echo "<p>Template error: {$e->getMessage()}</p>";
        }

		return ob_get_clean();
	}
	
	static private function isValidVariableName($var)
	{
		return true;
	}

    /**
     * @return string
     */
    public static function getTemplatePrefix(): string
    {
        return self::$templatePrefix;
    }

    /**
     * @param string $templatePrefix
     */
    public static function setTemplatePrefix(string $templatePrefix): void
    {
        self::$templatePrefix = $templatePrefix;
    }

}

