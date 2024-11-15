<?php
/**
 * [Class Description]
 *
 * @author     John McCann
 */


namespace Torpedo\Wp;

use Torpedo\Wp\Posts\Post;

class ImageHelper
{
    const IMAGE_SIZE_FULL = 'full';
    const IMAGE_SIZE_THUMBNAIL = 'thumbnail';
    const IMAGE_SIZE_MEDIUM = 'medium';
    const IMAGE_SIZE_MEDIUM_LARGE = 'medium_large';
    const IMAGE_SIZE_LARGE = 'large';
    const IMAGE_SIZE_FEATURED = 'featured-image';

    const IMAGE_SIZE_HERO_MEDIUM = 'hero-medium';

    const IMAGE_SIZE_CARD_LARGE = 'card-image-large';
    const IMAGE_SIZE_CARD_MEDIUM = 'card-image-medium';
    const IMAGE_SIZE_CARD_SMALL = 'card-image-small';


    static protected $imageSizes = [];

    static public function addImageSize($name, $width, $height, $crop)
    {
        add_image_size($name, $width, $height, $crop);
    }

    static private function getImageSizes()
    {
        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach (get_intermediate_image_sizes() as $size) {
            if (in_array($size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                $sizes[$size]['width'] = get_option("{$size}_size_w");
                $sizes[$size]['height'] = get_option("{$size}_size_h");
                $sizes[$size]['crop'] = (bool)get_option("{$size}_crop");
            } elseif (isset($_wp_additional_image_sizes[$size])) {
                $sizes[$size] = array(
                    'width'  => $_wp_additional_image_sizes[$size]['width'],
                    'height' => $_wp_additional_image_sizes[$size]['height'],
                    'crop'   => $_wp_additional_image_sizes[$size]['crop'],
                );
            }
        }

        self::$imageSizes = $sizes;
    }


    static public function getDefaultImage($seed, $imageSize = self::IMAGE_SIZE_FEATURED, $context = null)
    {
        $image = self::getDefaultImageObject($seed, $context);
        return $image['sizes'][$imageSize]
            ?? $image['url'];
    }

    static public function getDefaultImageId($seed, $context = null)
    {
        $image = self::getDefaultImageObject($seed, $context);
        return $image['id'];
    }

    static private function getDefaultImageObject($seed, $context = null)
    {
        $defaultImages = [];

        if (!empty($context)) {
            $defaultImages = get_field("default_{$context}_images", 'option');
        }

        if (empty($defaultImages)) {
            $defaultImages = get_field('default_images', 'option');
        }

        if (empty($defaultImages)) {
            return null;
        }

        $dec = hexdec(substr(md5($seed), 0, 4));
        //$index = $dec % count($defaultImages);

        srand($dec);
        $index = mt_rand(0, count($defaultImages)-1);
        return $defaultImages[$index]['image'];
    }

    static public function getUrlOrDefault($attachment, $seed, $imageSize = self::IMAGE_SIZE_FULL, $context = null)
    {
        if (empty($attachment)) {
            $seed = $seed ?? mt_rand(1, 100);
            return self::getDefaultImage($seed, $imageSize, $context);
        }

        return self::getUrl($attachment, $imageSize);
    }

    /**
     * Returns an image url for a given attachment at the given size
     * Deals with attachment being an array or an id as the acf image field
     * can be set to either!
     *
     * @author John McCann
     * @param array|int $attachment
     * @param string $imageSize
     * @return mixed
     */
    static public function getUrl($attachment, $imageSize = self::IMAGE_SIZE_FULL)
    {
        if (empty($attachment)) {
            return '';
        }

        // Attachment is a post id
        if (is_numeric($attachment)) {
            $img = wp_get_attachment_image_src($attachment, $imageSize);
            return $img[0];
        }

        // Attachment is a full image definition
        if (is_array($attachment)) {
            if ($imageSize == self::IMAGE_SIZE_FULL || empty($imageSize)) {
                return $attachment['url'];
            }

            if (isset($attachment['sizes'][$imageSize])) {
                return $attachment['sizes'][$imageSize];
            }

            // Last resort - full image
            return $attachment['url'];
        }

        // Todo: do we return a default image here?
        return '';
    }

    static public function getFilePath($attachment, $imageSize = self::IMAGE_SIZE_FULL)
    {
        return get_attached_file($attachment);
    }

    static public function getSrcSet($imageSize = self::IMAGE_SIZE_FULL)
    {

    }

    static public function getThumbnailUrl(Post $post, $imageSize = self::IMAGE_SIZE_FULL)
    {
        $img = wp_get_attachment_image_src($post->getThumbnailId(), $imageSize);

        if (empty($img)) {
            $id = self::getDefaultImage($post->getId());
            $img = wp_get_attachment_image_src($id, $imageSize);
        }

        return $img[0];
    }

    static public function getThumbnailSrcSet(Post $post, array $imageSizes = [])
    {
        $url = wp_get_attachment_image_srcset($post->getThumbnailId(), $imageSizes);

        if (empty($url)) {
            $id = self::getDefaultImage($post->getId());
            $url = wp_get_attachment_image_srcset($id);
        }

        return $url;
    }

    static public function getThumbnailSizes(Post $post, array $imageSizes = [])
    {
        $id = $post->getThumbnailId();

        if (empty($id)) {
            $id = self::getDefaultImage($post->getId());
        }
        return wp_get_attachment_image_sizes($id);
    }
}

?>
