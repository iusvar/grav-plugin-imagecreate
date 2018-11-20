<?php
namespace Grav\Plugin;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Utils;
use Grav\Common\Cache;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class ImagecreatePlugin
 * @package Grav\Plugin
 */
class ImagecreatePlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onShortcodeHandlers' => ['onShortcodeHandlers', 0]
        ];
    }

    public function onShortcodeHandlers(Event $e)
    {
        $this->grav['shortcode']->registerShortcode('ImagecreateShortcode.php', __DIR__);
    }

    public static function listFonts()
    {
      $fonts = [];
      $path = Grav::instance()['locator']->findResource('plugins://imagecreate/fonts');

      foreach (new \DirectoryIterator($path) as $file) {
        if ($file->isDir() || $file->isDot() || Utils::startsWith($file->getFilename(), '.')) {
          continue;
        }
        $font = $file->getBasename('.tff');
        $fonts[$font] = $font;
      }
      asort($fonts);
Cache::clearCache('standard');
      return $fonts;
    }
}
