<?php
namespace Backend\Controller\Admin;

use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Component\PaginatorComponent;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Backend\Controller\Component\FlashComponent;


/**
 * Class BackendAppController
 *
 * Use this class as a base controller for app controllers
 * which should run in backend context
 *
 * @package Backend\Controller
 *
 * @property AuthComponent $Auth
 * @property FlashComponent $Flash
 * @property PaginatorComponent $Paginator
 *
 */
abstract class AbstractBackendController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @throws \Cake\Core\Exception\Exception
     * @return void
     */
    public function initialize()
    {
        // Configure Backend component
        if (!$this->components()->has('Backend')) {
            $this->loadComponent('Backend.Backend');
        }
    }

    /**
     * Fallback controller authorization
     *
     * @return bool|null
     */
    public function isAuthorized()
    {
        return null;
    }
}
