<?php

namespace GeorgetteParty\BaseBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use GeorgetteParty\BaseBundle\Utils\ClassGuesser;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Abstract BaseManager
 */
abstract class BaseManager
{
    /**
     * Current manager $entityManager
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $entityManager;

    /**
     * Construct a BaseManager with its entityManager
     * @param ObjectManager $entityManager
     */
    public function __construct(ObjectManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Save an object
     * @param $object
     * @param bool $andFlush
     * @return BaseManager
     */
    public function save($object, $andFlush = true)
    {
        // TODO handle collections
        $entityManager = $this->getEntityManager();
        $entityManager->persist($object);

        if ($andFlush) {
            $entityManager->flush();
        }
        return $this;
    }

    /**
     * Delete an entity
     * @param $object
     * @param bool $andFlush
     * @return BaseManager
     */
    public function delete($object, $andFlush = true)
    {
        // TODO handle collections
        $this->getEntityManager()->remove($object);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
        return $this;
    }

    /**
     * Delete a collection of entities
     * @param array $collection
     * @param bool $andFlush
     */
    public function deleteCollection($collection, $andFlush = true)
    {
        foreach ($collection as $item) {
            $this->delete($item, $andFlush);
        }
    }

    /**
     * Delete an entity by its id
     * @param $id
     * @param bool $andFlush
     * @throws \Symfony\Component\Security\Acl\Exception\Exception
     * @return BaseManager
     */
    public function deleteById($id, $andFlush = true)
    {
        $object = $this->find($id);

        if (!$object) {
            throw new Exception();
        }
        $this->getEntityManager()->remove($object);

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
        return $this;
    }

    /**
     * Find all entities from the current repository
     * @return mixed
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Find one entity by its id
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Return the repository object by its name $repositoryName
     * If $repositoryName is null, it will try to guess it
     * @param null $repositoryName
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($repositoryName = null)
    {
        $guesser = new ClassGuesser($this);

        // try to find automatically the repository name
        if (!$repositoryName) {
            $repositoryName = $guesser->getClass(array('Manager', 'Controller'));
            $repositoryName = sprintf('%s%s:%s', $guesser->getNamespace(), $guesser->getBundle(), $repositoryName);

            // get bundle and repository in camel case
            $repositoryName = Container::camelize($repositoryName);
        }
        return $this->getEntityManager()->getRepository($repositoryName);
    }

    /**
     * Return current entityManager
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}