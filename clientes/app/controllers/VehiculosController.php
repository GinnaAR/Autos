<?php
declare(strict_types=1);

 

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model;


class VehiculosController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        //
    }

    /**
     * Searches for vehiculos
     */
    public function searchAction()
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);
        $parameters = Criteria::fromInput($this->di, 'Vehiculos', $_GET)->getParams();
        $parameters['order'] = "placa";

        $paginator   = new Model(
            [
                'model'      => 'Vehiculos',
                'parameters' => $parameters,
                'limit'      => 10,
                'page'       => $numberPage,
            ]
        );

        $paginate = $paginator->paginate();

        if (0 === $paginate->getTotalItems()) {
            $this->flash->notice("The search did not find any vehiculos");

            $this->dispatcher->forward([
                "controller" => "vehiculos",
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
     * Edits a vehiculo
     *
     * @param string $placa
     */
    public function editAction($placa)
    {
        if (!$this->request->isPost()) {
            $vehiculo = Vehiculos::findFirstByplaca($placa);
            if (!$vehiculo) {
                $this->flash->error("vehiculo was not found");

                $this->dispatcher->forward([
                    'controller' => "vehiculos",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->placa = $vehiculo->placa;

            $this->tag->setDefault("placa", $vehiculo->placa);
            $this->tag->setDefault("marca", $vehiculo->marca);
            $this->tag->setDefault("modelo", $vehiculo->modelo);
            
        }
    }

    /**
     * Creates a new vehiculo
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "vehiculos",
                'action' => 'index'
            ]);

            return;
        }

        $vehiculo = new Vehiculos();
        $vehiculo->placa = $this->request->getPost("placa");
        $vehiculo->marca = $this->request->getPost("marca");
        $vehiculo->modelo = $this->request->getPost("modelo");
               

        if (!$vehiculo->save()) {
            foreach ($vehiculo->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "vehiculos",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("vehiculo was created successfully");

        $this->dispatcher->forward([
            'controller' => "vehiculos",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a vehiculo edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "vehiculos",
                'action' => 'index'
            ]);

            return;
        }

        $placa = $this->request->getPost("placa");
        $vehiculo = Vehiculos::findFirstByplaca($placa);

        if (!$vehiculo) {
            $this->flash->error("vehiculo does not exist " . $placa);

            $this->dispatcher->forward([
                'controller' => "vehiculos",
                'action' => 'index'
            ]);

            return;
        }

        $vehiculo->placa = $this->request->getPost("placa");
        $vehiculo->marca = $this->request->getPost("marca");
        $vehiculo->modelo = $this->request->getPost("modelo");
        

        if (!$vehiculo->save()) {

            foreach ($vehiculo->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "vehiculos",
                'action' => 'edit',
                'params' => [$vehiculo->placa]
            ]);

            return;
        }

        $this->flash->success("vehiculo was updated successfully");

        $this->dispatcher->forward([
            'controller' => "vehiculos",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a vehiculo
     *
     * @param string $placa
     */
    public function deleteAction($placa)
    {
        $vehiculo = Vehiculos::findFirstByplaca($placa);
        if (!$vehiculo) {
            $this->flash->error("vehiculo was not found");

            $this->dispatcher->forward([
                'controller' => "vehiculos",
                'action' => 'index'
            ]);

            return;
        }

        if (!$vehiculo->delete()) {

            foreach ($vehiculo->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "vehiculos",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("vehiculo was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "vehiculos",
            'action' => "index"
        ]);
    }
}
