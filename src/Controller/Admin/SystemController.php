<?php
namespace Backend\Controller\Admin;

use Backend\Controller\Admin\AppController;
use Cake\Core\Plugin;
use Cake\Core\Configure;

/**
 * Class SystemController
 * @package Backend\Controller
 *
 */
class SystemController extends AppController
{
    public function index()
    {
    }

    /**
     * Display PHPINFO
     *
     * @see http://www.mainelydesign.com/blog/view/displaying-phpinfo-without-css-styles
     * @param int $what PHP Info option
     */
    public function php($what = INFO_ALL)
    {
        ob_start();
        phpinfo($what);
        $phpinfo = ob_get_contents();
        ob_end_clean();

        $phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);

        $this->set(compact('phpinfo'));
    }

    /**
     * Displays CAKE PHP Global constants
     */
    public function globals()
    {
        $globals = [
            'APP', 'APP_DIR', 'CONFIG', 'CACHE', 'CAKE', 'CAKE_VERSION', 'CAKE_CORE_INCLUDE_PATH', 'CORE_PATH',
            'DS', 'LOGS', 'ROOT', 'TESTS', 'TMP','WWW_ROOT'
        ];
        $this->set(compact('globals'));
    }

    /**
     * Displays information about loaded Cake plugins
     */
    public function plugins()
    {
        $plugins = [];
        foreach (Plugin::loaded() as $pluginName) {
            /*
            $plugins[$pluginName] = [
                'path' => Plugin::path($pluginName),
                'config' => Plugin::configPath($pluginName),
                'class' => Plugin::classPath($pluginName)
            ];
            */
            $plugins[$pluginName] = Plugin::path($pluginName);
        }

        $this->set('plugins', $plugins);
        $this->set('_serialize', $plugins);
    }

    public function datetime()
    {
        $data = array();

        $data['dateDefaultTimezoneGet'] = date_default_timezone_get();
        $data['dateTimeZoneIniGet'] = ini_get('date.timezone');

        $this->set(compact('data'));
    }

    public function session()
    {
        $this->set('session', $this->request->session()->read());
    }

    public function config()
    {
        $this->set('config', Configure::read());
    }

}