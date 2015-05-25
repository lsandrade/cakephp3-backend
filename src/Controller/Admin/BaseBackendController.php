<?php
namespace Backend\Controller\Admin;

use Backend\Controller\BackendControllerInterface;
use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Component\PaginatorComponent;
use Cake\Core\Configure;
use Cake\Controller\Controller;
use Backend\Controller\Component\FlashComponent;
use App\Controller\Admin\AppController as AdminAppController;

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
 */
abstract class BaseBackendController extends AdminAppController implements BackendControllerInterface
{
    public $layout = "Backend.admin";

    public $helpers = [
        'Html',
        'Form' => [
            'templates' => [
                'Backend.semantic-form-templates'
             ],
            'widgets' => [
                'button' => ['SemanticUi\View\Widget\ButtonWidget'],
                'datetime' => ['Backend\View\Widget\DateTimeWidget']
            ]
        ],
        'Paginator' => [
            'templates' => 'Backend.semantic-paginator-templates'
        ],
        'SemanticUi.Ui'
    ];

    /**
     * @var FlashComponent
     */
    public $Flash;

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
        parent::initialize();

        // Configure FlashComponent
        if ($this->components()->has('Flash')) {
            $this->components()->unload('Flash');
        }
        $this->Flash = $this->components()->load('Flash', [
            'className' => '\Backend\Controller\Component\FlashComponent',
            'key' => 'backend',
            'plugin' => 'Backend'
        ]);

        // Configure Authentication
        //@TODO autoconfigure backend authentication
        if (!$this->components()->has('Auth')) {
            throw new Exception('Backend: Authentication not configured');
        }

        // Configure Authorization
        //@TODO autoconfigure backend authorization
        $authorize = $this->Auth->config('authorize');
        if (empty($authorize)) {
            throw new Exception('Backend: Authorization not configured');
        }

        if (!$this->components()->has('Backend')) {
            $this->loadComponent('Backend.Backend');
        }
    }

    /**
     * Backend fallback controller authorization
     *
     * If controller authorization has been enabled and no other subclasses handles `isAuthorized`
     * ALL of the following fallback validations will be performed:
     *
     * 1) root: TRUE, if user with Id '1' or username 'root'
     * 2) userfield: TRUE, if user with field 'is_backend_user' set to TRUE
     * 3) usergroup: TRUE, if user is member of group 'backend' listed in field 'groups'
     *
     * @return bool
     */
    public function isAuthorized()
    {
        //@TODO Make controller authorization configurable

        $userId = $this->Auth->user('id');

        // root is always authorized
        if ($userId === 1 || $this->Auth->user('username') === 'root') {
            return true;
        }

        // configured backend user ids
        if (Configure::check('Backend.Users') && in_array($userId, (array) Configure::read('Backend.Users'))) {
            return true;
        }

        // user group authorization
        if ($this->Auth->user('groups') &&
            is_array($this->Auth->user('groups')) &&
            //isset($this->Auth->user('groups')[0]) &&
            in_array('backend', $this->Auth->user('groups'))
        ) {
            return true;
        }
    }
}