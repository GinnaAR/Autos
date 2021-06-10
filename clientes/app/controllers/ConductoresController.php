<?php
declare(strict_types=1);

 

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model;


class ConductoresController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        //
    }

    /**
     * Searches for conductores
     */
    public function searchAction()
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);
        $parameters = Criteria::fromInput($this->di, 'Conductores', $_GET)->getParams();
        $parameters['order'] = "id";

        $paginator   = new Model(
            [
                'model'      => 'Conductores',
                'parameters' => $parameters,
                'limit'      => 10,
                'page'       => $numberPage,
            ]
        );

        $paginate = $paginator->paginate();

        if (0 === $paginate->getTotalItems()) {
            $this->flash->notice("The search did not find any conductores");

            $this->dispatcher->forward([
                "controller" => "conductores",
                "action" => "index"
            ]);

            return;
        }

        $this->view->page = $paginate;
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        //
    }

    /**
     * Edits a conductore
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {
            $conductore = Conductores::findFirstByid($id);
            if (!$conductore) {
                $this->flash->error("conductore was not found");

                $this->dispatcher->forward([
                    'controller' => "conductores",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $conductore->id;

            $this->tag->setDefault("id", $conductore->id);
            $this->tag->setDefault("nombre", $conductore->nombre);
            $this->tag->setDefault("estado", $conductore->estado);
            $this->tag->setDefault("vehiculoplaca", $conductore->vehiculoplaca);
            
        }
    }

    /**
     * Creates a new conductore
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "conductores",
                'action' => 'index'
            ]);

            return;
        }

        $conductore = new Conductores();
        $conductore->id = $this->request->getPost("id");
        $conductore->nombre = $this->request->getPost("nombre");
        $conductore->estado = $this->request->getPost("estado", "int");
        $conductore->vehiculoplaca = $this->request->getPost("vehiculoplaca");

        

        if (!$conductore->save()) {
            foreach ($conductore->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "conductores",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("conductore was created successfully");

        $this->dispatcher->forward([
            'controller' => "conductores",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a conductore edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "conductores",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $conductore = Conductores::findFirstByid($id);

        if (!$conductore) {
            $this->flash->error("conductore does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "conductores",
                'action' => 'index'
            ]);

            return;
        }

        $conductore->id = $this->request->getPost("id");
        $conductore->nombre = $this->request->getPost("nombre");
        $conductore->estado = $this->request->getPost("estado", "int");
        $conductore->vehiculoplaca = $this->request->getPost("vehiculoplaca");
        

        if (!$conductore->save()) {

            foreach ($conductore->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "conductores",
                'action' => 'edit',
                'params' => [$conductore->id]
            ]);

            return;
        }

        $this->flash->success("conductore was updated successfully");

        $this->dispatcher->forward([
            'controller' => "conductores",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a conductore
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $conductore = Conductores::findFirstByid($id);
        if (!$conductore) {
            $this->flash->error("conductore was not found");

            $this->dispatcher->forward([
                'controller' => "conductores",
                'action' => 'index'
            ]);

            return;
        }

        if (!$conductore->delete()) {

            foreach ($conductore->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "conductores",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("conductore was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "conductores",
            'action' => "index"
        ]);
    }
}
