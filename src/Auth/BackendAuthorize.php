<?php

namespace Backend\Auth;

use Cake\Auth\BaseAuthorize;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Network\Request;

class BackendAuthorize extends BaseAuthorize
{
    /**
     * Constructor
     *
     * @param ComponentRegistry $registry
     * @param array $config
     */
    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
    }

    /**
     * Authorize user for request
     *
     * @param array $user Current authenticated user
     * @param \Cake\Network\Request $request Request instance.
     * @return bool
     */
    public function authorize($user, Request $request)
    {
        return true;

        $userId = $user['id'];
        if (!$userId) {
            return null;
        }

        // allow root
        if ($user['username'] === 'root') {
            return true;
        }

        // allow superusers
        if (isset($user['is_superuser']) && $user['is_superuser'] === true) {
            return true;
        }

        // configured backend users
        //@TODO Refactor this dirty UserId-hack with actual http basic auth
        $backendUsersIds = (array) Configure::read('Backend.Users');
        if (in_array($userId, $backendUsersIds)) {
            return true;
        }

        return null;
    }
}