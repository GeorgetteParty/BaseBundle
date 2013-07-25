<?php

namespace GeorgetteParty\BaseBundle\Manager;
use Doctrine\Common\Persistence\ObjectManager;
use GeorgetteParty\BaseBundle\Utils\ClassGuesser;
use Symfony\Component\DependencyInjection\Container;

// TODO comment here
abstract class BaseManager
{
    protected $entity_manager;

    public function __construct(ObjectManager $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    public function save($object_to_persist)
    {
        $entity_manager = $this->getEntityManager();
        $entity_manager->persist($object_to_persist);
        $entity_manager->flush();
    }

    public function delete($mixed)
    {
        $object_to_delete = $mixed;

        if (!is_object($mixed)) {
            $object_to_delete = $this->find($mixed);

            if (!$object_to_delete) {
                throw new \Exception('Entity not found');
            }
        }
        $this->getEntityManager()->remove($object_to_delete);
        $this->getEntityManager()->flush();
    }

    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @param null $repositoryName
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($repositoryName = null)
    {
        $guesser = new ClassGuesser($this);
        $bundle = $guesser->getBundle();

        // try to find automatically the repository name
        if (!$repositoryName) {
            $repositoryName = $guesser->getClass(array('Manager', 'Controller'));

            // get bundle and repository in camel case
            $repositoryName = Container::camelize($repositoryName);
        }
        // TODO make this more permissive (here, other wont work)
        // add bundle prefix
        if (substr($repositoryName, 0, 7) != $bundle) {
            $repositoryName = $bundle . $repositoryName;
        }
        return $this->getEntityManager()->getRepository($repositoryName);
    }

    public function getEntityManager()
    {
        return $this->entity_manager;
    }
}