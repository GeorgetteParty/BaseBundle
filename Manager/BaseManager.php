<?php

namespace GeorgetteParty\BaseBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use GeorgetteParty\BaseBundle\Utils\ClassGuesser;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityNotFoundException;

/**
 * @todo comment here
 */
abstract class BaseManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $entityManager;

    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $object
     * @param bool $andFlush
     * @return BaseManager
     */
    public function save($object, $andFlush = true)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($object);

        if ($andFlush) {
            $entityManager->flush();
        }

        return $this;
    }

    /**
     * @param $object
     * @param bool $andFlush
     * @return BaseManager
     */
    public function delete($object, $andFlush = true)
    {
        $this->getEntityManager()->remove($object);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }

        return $this;
    }

    /**
     * @param $id
     * @param bool $andFlush
     * @return BaseManager
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function deleteById($id, $andFlush = true)
    {
        $object = $this->find($id);

        if (!$object) {
            throw new EntityNotFoundException;
        }
        $this->getEntityManager()->remove($object);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @param $id
     * @return mixed
     */
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

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}