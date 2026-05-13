<?php

/**
 * @package     SP Simple Portfolio
 *
 * @copyright   Copyright (C) 2010 - 2025 JoomShaper. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;
use Joomla\CMS\Version;

class SpsimpleportfolioHelper
{

    public static $extension = 'com_spsimpleportfolio';
    
    /**
     * Load class aliases for Joomla 6 compatibility
     * 
     * @return void
     */
    public static function loadAliases()
    {
        $joomlaVersion = defined('JVERSION') ? JVERSION : (new Version())->getShortVersion();

        if (version_compare($joomlaVersion, '6.0', '>=')) {
            $classAliases = [
                '\Joomla\Filesystem\Path' => 'Joomla\CMS\Filesystem\Path',
                '\Joomla\Filesystem\Folder' => 'Joomla\CMS\Filesystem\Folder',
                '\Joomla\Filesystem\File' => 'Joomla\CMS\Filesystem\File',
            ];

            foreach ($classAliases as $alias => $original) {
                if (!class_exists($original)) {
                    class_alias($alias, $original);
                }
            }
        }
    }

    /**
     * Get the extension of a file name
     * 
     * @param string $file The file name
     * 
     * @return string The extension
     */
    public static function getExt($file)
    {
        // String manipulation should be faster than pathinfo() on newer PHP versions.
        $dot = strrpos($file, '.');

        if ($dot === false) {
            return '';
        }

        $ext = substr($file, $dot + 1);

        // Extension cannot contain slashes.
        if (strpos($ext, '/') !== false || (DIRECTORY_SEPARATOR === '\\' && strpos($ext, '\\') !== false)) {
            return '';
        }

        return $ext;
    }


    // Create thumbs
    public static function createThumbs($src, $sizes = array(), $folder = '', $base_name = '', $ext = '')
    {
        /**
         * Creates resized thumbnail images for a given source image in various sizes.
         *
         * Supports multiple image formats using the GD library. If the image format is AVIF,
         * it delegates processing to the handleAvifImage() method which supports both GD and Imagick.
         *
         * @param string $src        Full path to the source image file.
         * @param array  $sizes      Array of target sizes. Each element should be an array [width, height].
         * @param string $folder     Destination folder for storing generated thumbnails.
         * @param string $base_name  Base name for the generated thumbnail files (without extension).
         * @param string $ext        Extension/format of the image (e.g., 'jpg', 'png', 'avif', etc.)
         *
         * @return array|false       Returns an associative array of thumbnail paths on success.
         *                           Keys are the same as in the $sizes array. On failure, returns false.
         *
         * @throws Exception         If an image cannot be created or resized due to an unsupported type or a GD/Imagick error.
         */


        // Get params
        $params = ComponentHelper::getParams('com_spsimpleportfolio');
        $img_crop_position = $params->get('crop_position', 'center');

        list($originalWidth, $originalHeight) = getimagesize($src);

        if ($ext === 'avif') {
            return self::handleAvifImage($src, $folder, $base_name, $ext, $sizes, $img_crop_position);
        }

        // GD fallback for other image types
        switch ($ext) {
            case 'bmp':
                $img = imagecreatefromwbmp($src);
                break;
            case 'gif':
                $img = imagecreatefromgif($src);
                break;
            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($src);
                break;
            case 'png':
                $img = imagecreatefrompng($src);
                break;
            case 'webp':
                $img = imagecreatefromwebp($src);
                break;
            default:
                Factory::getApplication()->enqueueMessage('Unsupported image type or no AVIF support in GD', 'error');
                return false;
        }

        if (count($sizes)) {
            $output = array();

            if ($base_name) {
                $output['original'] = $folder . '/' . $base_name . '.' . $ext;
            }

            foreach ($sizes as $key => $size) {
                $targetWidth = $size[0];
                $targetHeight = $size[1];
                $ratio_thumb = $targetWidth / $targetHeight;
                $ratio_original = $originalWidth / $originalHeight;

                if ($ratio_original >= $ratio_thumb) {
                    $height = $originalHeight;
                    $width = ceil(($height * $targetWidth) / $targetHeight);
                    if ($img_crop_position == 'topleft') {
                        $x = 0;
                    } elseif ($img_crop_position == 'topright') {
                        $x = ceil($originalWidth - $width);
                    } else {
                        $x = ceil(($originalWidth - $width) / 2);
                    }
                    $y = 0;
                } else {
                    $width = $originalWidth;
                    $height = ceil(($width * $targetHeight) / $targetWidth);
                    if ($img_crop_position == 'topleft') {
                        $y = 0;
                    } else {
                        $y = ceil(($originalHeight - $height) / 2);
                    }
                    $x = 0;
                }
                try {
                    $new = imagecreatetruecolor($targetWidth, $targetHeight);
                } catch (\Exception $e) {
                    Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                }

                if ($ext == "gif" or $ext == "png") {
                    imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
                    imagealphablending($new, false);
                    imagesavealpha($new, true);
                }

                imagecopyresampled($new, $img, 0, 0, $x, $y, $targetWidth, $targetHeight, $width, $height);

                if ($base_name) {
                    $dest = dirname($src) . '/' . $base_name . '_' . $key . '.' . $ext;
                    $output[$key] = $folder . '/' . $base_name . '_' . $key . '.' . $ext;
                } else {
                    $dest = $folder . '/' . $key . '.' . $ext;
                }

                switch ($ext) {
                    case 'bmp':
                        imagewbmp($new, $dest);
                        break;
                    case 'gif':
                        imagegif($new, $dest);
                        break;
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($new, $dest);
                        break;
                    case 'png':
                        imagepng($new, $dest);
                        break;
                    case 'webp':
                        imagewebp($new, $dest);
                        break;
                }
            }

            return $output;
        }

        return false;
    }

    public static function handleAvifImage($src, $folder, $base_name, $ext, $sizes, $img_crop_position = 'center')
    {
        $output = [];

        // Joomla's Version class
        $version = new Version();
        $joomlaMajorVersion = $version->getShortVersion();

        if (version_compare($joomlaMajorVersion, '5.0.0', '>=') && class_exists('Imagick')) {
            try {
                $imagick = new Imagick($src);
                $dimensions = $imagick->getImageGeometry();
                $originalWidth = $dimensions['width'] ?? 0;
                $originalHeight = $dimensions['height'] ?? 0;

                if ($base_name) {
                    $output['original'] = $folder . '/' . $base_name . '.' . $ext;
                }

                foreach ($sizes as $key => $size) {
                    $targetWidth = $size[0];
                    $targetHeight = $size[1];

                    $ratio_thumb = $targetWidth / $targetHeight;
                    $ratio_original = $originalWidth / $originalHeight;

                    if ($ratio_original >= $ratio_thumb) {
                        $height = $originalHeight;
                        $width = ceil($height * $ratio_thumb);
                        switch ($img_crop_position) {
                            case 'topleft':
                                $x = 0;
                                break;
                            case 'topright':
                                $x = $originalWidth - $width;
                                break;
                            default:
                                $x = intval(($originalWidth - $width) / 2);
                                break;
                        }
                        $y = 0;
                    } else {
                        $width = $originalWidth;
                        $height = ceil($width / $ratio_thumb);
                        $x = 0;
                        $y = ($img_crop_position === 'topleft') ? 0 : intval(($originalHeight - $height) / 2);
                    }

                    $thumb = clone $imagick;
                    $thumb->cropImage($width, $height, $x, $y);
                    $thumb->resizeImage($targetWidth, $targetHeight, Imagick::FILTER_LANCZOS, 1, true);
                    $thumb->setImageFormat('avif');

                    $dest = $folder . '/' . ($base_name ? $base_name . '_' . $key : $key) . '.' . $ext;
                    $output[$key] = $dest;
                    $thumb->writeImage($dest);

                    $thumb->clear();
                    $thumb->destroy();
                }

                $imagick->clear();
                $imagick->destroy();
                return $output;
            } catch (Exception $e) {
                Factory::getApplication()->enqueueMessage('Imagick error: ' . $e->getMessage(), 'error');
                return false;
            }
        } else {
            // Fallback logic
            $dimensions = @getimagesize($src);
            $originalWidth = $dimensions ? $dimensions[0] : 0;
            $originalHeight = $dimensions ? $dimensions[1] : 0;

            if (!is_dir($folder)) {
                Folder::create($folder, 0755);
            }

            if ($base_name) {
                $originalDest = $folder . '/' . $base_name . '.' . $ext;
                if (File::copy($src, $originalDest)) {
                    $output['original'] = $originalDest . '?width=' . $originalWidth . '&height=' . $originalHeight;
                } else {
                    Factory::getApplication()->enqueueMessage('Failed to copy original AVIF file.', 'error');
                    return false;
                }
            }

            foreach ($sizes as $key => $size) {
                $thumbDest = $folder . '/' . $base_name . $key . '.' . $ext;

                if (!is_dir(dirname($thumbDest))) {
                    Folder::create(dirname($thumbDest), 0755);
                }

                if (File::copy($src, $thumbDest)) {
                    $output[$key] = $thumbDest . '?width=' . $size[0] . '&height=' . $size[1];
                } else {
                    Factory::getApplication()->enqueueMessage("Failed to copy AVIF thumbnail for size {$key}.", 'error');
                }
            }

            return $output;
        }
    }


    public static function isPageBuilderIntegrated($item)
    {

        $output = new stdClass();
        $output->url = '';

        if (PluginHelper::isEnabled('spsimpleportfolio', 'sppagebuilder')) {
            $hasPage = self::hasPBPage($item->id);
            $output->hasPage = $hasPage;

            if ($hasPage) {

                $app = Factory::getApplication();
                $router = $app->getRouter();

                $lang_code = (isset($item->language) && $item->language && explode('-', $item->language)[0]) ? explode('-', $item->language)[0] : '';
                $enable_lang_filter = PluginHelper::getPlugin('system', 'languagefilter');
                $conf = Factory::getConfig();

                $front_link = 'index.php?option=com_sppagebuilder&view=form&tmpl=componenet&layout=edit&extension=com_spsimpleportfolio&extension_view=item&id=' . $hasPage;
                $sefURI = str_replace('/administrator', '', $router->build($front_link));
                if ($lang_code && $lang_code !== '*' && $enable_lang_filter && $conf->get('sef')) {
                    $sefURI = str_replace('/index.php/', '/index.php/' . $lang_code . '/', $sefURI);
                } elseif ($lang_code && $lang_code !== '*') {
                    $sefURI = $sefURI . '&lang=' . $lang_code;
                }

                $output->url = $sefURI;
            }
        }

        return $output;
    }

    public static function hasPBPage($view_id = 0)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id')));
        $query->from($db->quoteName('#__sppagebuilder'));
        $query->where($db->quoteName('extension') . ' = ' . $db->quote('com_spsimpleportfolio'));
        $query->where($db->quoteName('extension_view') . ' = ' . $db->quote('item'));
        $query->where($db->quoteName('view_id') . ' = ' . $db->quote($view_id));
        $db->setQuery($query);
        return $db->loadResult();
    }
}
