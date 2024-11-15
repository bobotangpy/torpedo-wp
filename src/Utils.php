<?php
/**
 * [Class Description]
 *
 * @author     John McCann
 */


namespace Torpedo;

class Utils
{
	public static function isSetOr(&$var, $default = false)
	{
		return isset($var) ? $var : $default;
	}


	/**
	 * Replacement for array_column function not present in ancient versions of php
	 *
	 * @author John McCann
	 * @param array $input
	 * @param       $columnKey
	 * @param null  $indexKey
	 * @return array
	 * @throws \Exception
	 */
	public static function arrayColumn(array $input, $columnKey, $indexKey = null)
	{
		
		if (function_exists('array_column')) {
			return array_column($input, $columnKey, $indexKey);
		}
		
		$array = array ();
		foreach ($input as $value) {
			if (!isset($value[$columnKey])) {
				throw new \Exception("Key \"$columnKey\" does not exist in array");
			}

			if (is_null($indexKey)) {
				$array[] = $value[$columnKey];

			} else {
				if (!isset($value[$indexKey])) {
					throw new \Exception("Key \"$indexKey\" does not exist in array");
				}

				if (!is_scalar($value[$indexKey])) {
					throw new \Exception("Key \"$indexKey\" does not contain scalar value");
				}

				$array[$value[$indexKey]] = $value[$columnKey];
			}
		}

		return $array;
	}

	public static function reindexArrayByColumn(array $input, $indexKey)
	{
		$output = [];
		
		foreach($input as $inputRow) {
			$output[$inputRow[$indexKey]] = $inputRow;
		}
		return $output;
	}
	
	public static function getConfig($config, $field, $default = null)
	{
		if (isset($config[$field])) {
			return $config[$field];
		}

		if ($default === null) {
			throw new \Exception("Couldn't get required config param $field");
		}

		return $default;
	}

	public static function objectToArray($data)
	{
		if (is_array($data) || is_object($data)) {
			$result = array();
			foreach ($data as $key => $value) {
				$result[$key] = self::objectToArray($value);
			}
			return $result;
		}
		return $data;
	}

	public static function hasSubArrays(array $data)
	{
		foreach($data as $key => $value) {
			if (is_array($value)) {
				return true;
			}
		}
		return false;
	}

	public static function multiExplode(array $delimiters, $string)
	{
		$string = str_replace($delimiters, $delimiters[0], $string);
		$output = explode($delimiters[0], $string);
		return array_filter($output);
	}
	
	/**
	 * Explodes a comma separated or space separated string
	 *
	 * @param $string
	 * @return array
	 */
	public static function stringToArray($string)
	{
		return self::multiExplode([' ',','], $string);
	}

	/**
	 * Converts a string-like_this to a StringLikeThis
	 * @author John McCann
	 * @param $string
	 * @return array
	 */
	public static function toUpperCamelCase($string)
	{
		$chunks = self::multiExplode(array('-', '_', '+'), $string);
		$output = array_map(function($n){ return(ucfirst($n));}, $chunks);
		return implode('', $output);
	}
	
	/**
	 * Converts a string-like_this to a StringLikeThis
	 * @author John McCann
	 * @param $string
	 * @return array
	 */
	public static function toLowerCamelCase($string)
	{
		$chunks = self::multiExplode(array('-', '_', '+'), $string);
		$output = array_map(function($n){ return(ucfirst($n));}, $chunks);
		return lcfirst(implode('', $output));
	}

	public static function toHumanReadable($string)
	{
		$chunks = self::multiExplode(array('-', '_', '+'), $string);
		$output = array_map(function($n){ return(ucfirst($n));}, $chunks);
		return implode(' ', $output);
	}

	public static function toKebabCase($string)
    {
        return self::toUrlSlug($string);
    }

    public static function toUrlSlug($string, $maxLength=0)
    {
        $string = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($string)), '-');
        if ($maxLength && strlen($string) > $maxLength) {
            $string = substr($string, 0, $maxLength);
            $pos = strrpos($string, '-');
            if ($pos > 0) {
                $string = substr($string, 0, $pos);
            }
        }
        return $string;
    }

    public static function implodeKeyValuePairs($pairs, $separator = ':', $delimiter = '; ')
	{
		if (!is_array($pairs)) {
			return '';
		}
		$output = '';
		foreach($pairs as $key => $value) {
			$output .= $key.$separator.$value.$delimiter;
		}
		return $output;
	}
	
	/**
	 * @author John McCann
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * @return string
	 */
	public static function humanDateRange($start, $end)
	{
		if ($start && $end) {
			if ($start == $end) {
			
			}
			
			if ($start->format('m') != $end->format('m')) {
				return $start->format('F jS') . ' - ' . $end->format('F jS, Y');
			} else {
				return $start->format('F jS') . ' - ' . $end->format('jS, Y');
			}
		}
		elseif ($start) {
			return $start->format('F jS, Y');
		}
		else if ($end) {
			return $end->format('F jS, Y');
		}
		else {
			return '';
		}
	}
	
	
	public static function arrayToHtmlAttribs($attributes)
	{
		$output = '';
		foreach ($attributes as $name => $value) {
			if (is_bool($value)) {
				if ($value) {
					$output .= $name . ' ';
				}
			} else {
				$output .= sprintf('%s="%s"', $name, $value);
			}
		}
		return $output;
	}
	
	/**
	 * @param $obj
	 * @param $className
	 * @return string/bool class name or false
	 */
	public static function isClass($obj, $className)
	{
		return strstr(get_class($obj), $className);
	}
	
	/**
	 * Convert bytes to a nicer file size readout
	 * @url https://stackoverflow.com/questions/5501427/php-filesize-mb-kb-conversion
	 * @param $bytes
	 * @return string
	 */
	public static function formatFileSizeUnits($bytes)
	{
		if ($bytes >= 1073741824) {
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		}
		elseif ($bytes >= 1048576) {
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		}
		elseif ($bytes >= 1024) {
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		}
		elseif ($bytes > 1) {
			$bytes = $bytes . ' bytes';
		}
		elseif ($bytes == 1) {
			$bytes = $bytes . ' byte';
		}
		else {
			$bytes = '0 bytes';
		}
		
		return $bytes;
	}
	
	/**
	 * Recursively get taxonomy and its children
	 *
	 * @param string $taxonomy
	 * @param int $parent - parent term id
	 * @return array
	 */
	public static function get_taxonomy_hierarchy( $taxonomy, $parent = 0 ) {
		// only 1 taxonomy
		$taxonomy = is_array( $taxonomy ) ? array_shift( $taxonomy ) : $taxonomy;
		// get all direct decendants of the $parent
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'parent' => $parent, 'hide_empty' => false ) );
		// prepare a new array.  these are the children of $parent
		// we'll ultimately copy all the $terms into this new array, but only after they
		// find their own children
		$children = array();
		// go through all the direct decendants of $parent, and gather their children
		foreach ( $terms as $term ){
			// recurse to get the direct decendants of "this" term
			$term->children = Utils::get_taxonomy_hierarchy( $taxonomy, $term->term_id );
			// add the term to our new array
			$children[ $term->term_id ] = $term;
		}
		// send the results back to the caller
		return $children;
	}
	
	/**
	 * Limits string at end of nearest word and adds optional limiter.
	 * @param $string
	 * @param $charLimit
	 * @param $limiter
	 * @return string
	 */
	public static function limitString($string, $charLimit, $limiter = '')
	{
		if (strlen($string) > $charLimit && !empty($string)) {
			$subString = substr($string, 0, $charLimit);
			$lastSpaceChar = strrpos($subString, ' ');
			if ($lastSpaceChar) {
				$subString = substr($subString, 0, $lastSpaceChar);
			}
			$formattedExcerpt = $subString . $limiter;
			return $formattedExcerpt;
		}
		return $string;
	}
}
