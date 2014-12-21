<?php
namespace Zizaco\Confide;

use Doctrine\ORM\EntityRepository;

/**
 * A service that abstracts all database interactions that happens
 * in Confide using Doctrine.
 *
 * @license MIT
 * @package Zizaco\Confide
 */
class DoctrineRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * Create a new ConfideRepository
     *
     * @param \Illuminate\Foundation\Application $app Laravel application object
     */
    public function __construct($app = null)
    {
        $app = $app ?: app();

        $em = $app['Doctrine\ORM\EntityManager'];

        parent::__construct($em, new \Doctrine\ORM\Mapping\ClassMetadata('User'));
    }

    /**
     * Find a user by one of the fields given as $identity.
     * If one of the fields in the $identity array matches the user
     * will be retrieved.
     *
     * @param array $identity An array of attributes and values to search for
     *
     * @return ConfideUser User object
     */
    public function getUserByIdentity($identity)
    {
        if (!is_array($identity) || empty($identity)) {
            throw new \Exception('Invalid identity param');
        }

        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from($this->_entityName, 'e');

        $firstWhere = true;
        foreach ($identity as $attribute => $value) {
            if ($firstWhere) {
                $qb->where($qb->expr()->eq('e.' . $attribute, ':' . $attribute));
            } else {
                $qb->orWhere($qb->expr()->eq('e.' . $attribute, ':' . $attribute));
            }
            $qb->setParameter($attribute, $value);

            $firstWhere = false;
        }

        $result = null;
        try {
            $result = $qb->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $ex) {
        }

        return $result;
    }

    /**
     * Find a user by the given email
     *
     * @param string $email The email to be used in the query
     *
     * @return ConfideUser User object
     */
    public function getUserByEmail($email)
    {
        return $this->getUserByIdentity(['email' => $email]);
    }

    /**
     * Find a user by the given email or username
     *
     * @param string $emailOrUsername Username of email to be used in the query
     *
     * @return ConfideUser User object
     */
    public function getUserByEmailOrUsername($emailOrUsername)
    {
        $identity = [
            'email' => $emailOrUsername,
            'username' => $emailOrUsername
        ];

        return $this->getUserByIdentity($identity);
    }

    /**
     * Update the confirmation status of a user to true if a user
     * is found with the given confirmation code.
     *
     * @param string $code
     *
     * @return bool Success
     */
    public function confirmByCode($code)
    {
        $identity = ['confirmation_code' => $code];

        $user = $this->getUserByIdentity($identity);

        if ($user) {
            return $this->confirmUser($user);
        } else {
            return false;
        }
    }

    /**
     * Updated the given user in the database. Set the 'confirmed' attribute to
     * true.
     *
     * @param  ConfideUser User object
     *
     * @return bool Success
     */
    protected function confirmUser($user)
    {
        $user->setConfirmed(true);

        $this->_em->persist($user);
        $this->_em->flush();
    }
}
