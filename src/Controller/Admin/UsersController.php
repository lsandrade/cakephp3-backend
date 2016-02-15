<?php
namespace Backend\Controller\Admin;

use Backend\Controller\Admin\AppController;

/**
 * Users Controller
 *
 * @property \User\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    public $modelClass = 'User.Users';

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['PrimaryGroup']
        ];
        $this->set('users', $this->paginate($this->Users));
        $this->set('_serialize', ['users']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['PrimaryGroup', 'Groups']
        ]);
        $this->set('user', $user);
        $this->set('_serialize', ['user']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        $user->accessible([
            'username', 'group_id', 'name', 'email', 'password'
        ], true);
        if ($this->request->is('post')) {
            $user = $this->Users->add($this->request->data);
            if ($user->id) {
                $this->Flash->success(__('The {0} has been saved.', __('user')));
                return $this->redirect(['action' => 'edit', $user->id]);
            } else {
                $this->Flash->error(__('The {0} could not be saved. Please, try again.', __('user')));
            }
        }
        $primaryGroup = $this->Users->PrimaryGroup->find('list', ['limit' => 200]);
        $userGroups = $this->Users->Groups->find('list', ['limit' => 200]);
        $this->set(compact('user', 'primaryGroup', 'userGroups'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Groups']
        ]);
        $user->accessible('*', true);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The {0} has been saved.', __('user')));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The {0} could not be saved. Please, try again.', __('user')));
            }
        }
        $primaryGroup = $this->Users->PrimaryGroup->find('list', ['limit' => 200]);
        $userGroups = $this->Users->Groups->find('list', ['limit' => 200]);
        $this->set(compact('user', 'primaryGroup', 'userGroups'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The {0} has been deleted.', __('user')));
        } else {
            $this->Flash->error(__('The {0} could not be deleted. Please, try again.', __('user')));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Change password of current user
     * @param null $userId
     * @return \Cake\Network\Response|void
     */
    public function passwordChange($userId = null)
    {
        if ($userId === null) {
            $userId = $this->Auth->user('id');
        } elseif ($userId !== $this->Auth->user('id')) {
            $this->Flash->error(__('You are not allowed to do this'));
            return $this->redirect($this->referer(['action' => 'index']));
        }

        $user = $this->Users->get($userId);
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->changePassword($user, $this->request->data)) {
                $this->Flash->success(__('Your password has been changed.'));
                $this->redirect(['controller' => 'Backend', 'action' => 'index']);
            } else {
                $this->Flash->error(__('Ups, something went wrong'));
            }
        }
        $this->set('user', $user);
    }

    /**
     * Change password of current user
     * @param null $userId
     * @return \Cake\Network\Response|void
     */
    public function passwordReset($userId = null)
    {
        $authUserId = $this->Auth->user('id');
        if ($userId === null) {
            $userId = $authUserId;
        } elseif ($userId !== $authUserId && $authUserId !== 1) {
            $this->Flash->error(__('Only root can do this'));
            return $this->redirect($this->referer(['action' => 'index']));
        }

        $user = $this->Users->get($userId);
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Users->resetPassword($user, $this->request->data)) {
                $this->Flash->success(__('Your password has been changed.'));
                $this->redirect(['controller' => 'Backend', 'action' => 'index']);
            } else {
                $this->Flash->error(__('Ups, something went wrong'));
            }
        }
        $this->set('user', $user);
    }

}
