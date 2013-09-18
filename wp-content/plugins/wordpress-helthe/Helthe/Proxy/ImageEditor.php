<?php

/*
 * This file is part of the WordPress Helthe plugin.
 *
 * (c) Helthe
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once ABSPATH . WPINC . '/class-wp-image-editor.php';

/**
 * This is a proxy class around the image editor implementations of WordPress.
 * It checks for errors on important operations.
 *
 * @author Carl Alexander
 */
class Helthe_Proxy_ImageEditor extends WP_Image_Editor
{

    /**
     * All the registered implementations.
     *
     * @var array
     */
    private static $implementations = array();

    /**
     * The implemantation used by the wrapper.
     *
     * @var string
     */
    private static $chosenImplementation;

    /**
     * @var WP_Image_Editor
     */
    private $editor;

    /**
     * Register the image editor with the appropriate WordPress filters.
     */
    public static function register()
    {
        // Hook in as late as possible to allow other plugins to leverage the filter.
        add_filter('wp_image_editors', array('Helthe_Proxy_ImageEditor', 'registerImageEditor'), 9999);
    }

    /**
     * Registers the image editor as the only implementation. Saves all other implementations for internal usage.
     *
     * @param array $implementations
     *
     * @return array
     */
    public static function registerImageEditor($implementations = array())
    {
        self::$implementations = $implementations;

        return array('Helthe_Proxy_ImageEditor');
    }

    /**
     * Runs tests on every registered implementation. Saves the chosen implementation.
     * This function mirrors the testing done by _wp_image_editor_choose.
     *
     * @param array $args
     *
     * @return boolean
     */
    public static function test($args = array())
    {
        foreach (self::$implementations as $implementation) {
            if (!call_user_func(array($implementation, 'test'), $args)) {
                continue;
            }

            if (isset($args['mime_type']) && !call_user_func(array($implementation, 'supports_mime_type'), $args['mime_type'])) {
                continue;
            }

            if (isset($args['methods']) && array_diff($args['methods'], get_class_methods($implementation))) {
                continue;
            }

            self::$chosenImplementation = $implementation;

            return true;
        }

        do_action('helthe_image_editor_not_found', __('No image editor could be selected.', 'helthe'));

        return false;
    }

    /**
     * Checks to see if our chosen implementation supports the mime-type specified.
     *
     * @param string $mime
     *
     * @return boolean
     */
    public static function supports_mime_type($mime)
    {
        if (!self::$chosenImplementation) {
            return false;
        }

        return call_user_func(array(self::$chosenImplementation, 'supports_mime_type'), $mime);
    }

    /**
     * Constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->editor = new self::$chosenImplementation($file);
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        return $this->editor->load();
    }

    /**
     * {@inheritdoc}
     */
    public function save($filename = null, $mime = null)
    {
        return $this->editor->save($filename, $mime);
    }

    /**
     * {@inheritdoc}
     */
    public function stream($mime = null)
    {
        return $this->editor->stream($mime);
    }

    /**
     * {@inheritdoc}
     */
    public function resize($maxWidth, $maxHeight, $crop = false)
    {
        return $this->editor->resize($maxWidth, $maxHeight, $crop);
    }

    /**
     * {@inheritdoc}
     */
    public function multi_resize($sizes)
    {
        return $this->editor->multi_resize($sizes);
    }

    /**
     * {@inheritdoc}
     */
    public function crop($srcX, $srcY, $srcWidth, $srcHeight, $dstWidth = null, $dstHeigth = null, $srcAbs = false)
    {
        return $this->editor->crop($srcX, $srcY, $srcWidth, $srcHeight, $dstWidth, $dstHeigth, $srcAbs);
    }

    /**
     * {@inheritdoc}
     */
    public function rotate($angle)
    {
        return $this->editor->rotate($angle);
    }

    /**
     * {@inheritdoc}
     */
    public function flip($horizontal, $vertical)
    {
        return $this->editor->flip($horizontal, $vertical);
    }

    /**
     * {@inheritdoc}
     */
    public function get_size()
    {
        return $this->editor->get_size();
    }

    /**
     * {@inheritdoc}
     */
    public function set_quality($quality)
    {
        return $this->editor->set_quality($quality);
    }

    /**
     * {@inheritdoc}
     */
    public function generate_filename($suffix = null, $path = null, $extension = null)
    {
        return $this->editor->generate_filename($suffix, $path, $extension);
    }

    /**
     * {@inheritdoc}
     */
    public function get_suffix()
    {
        return $this->editor->get_suffix();
    }
}
