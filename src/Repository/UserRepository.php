<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param String $usrId user ID of logged user
     * @return array
     */
    public function findContainersForLoggedUser(string $usrId) {
        return $this->createQueryBuilder('usr')
            ->select('cnt.id', 'cnt.containerName', 'cnt.visibility', 'u2c.mode')
            ->innerJoin('usr.u2container', 'u2c')
            ->innerJoin('u2c.container', 'cnt')
            ->andWhere('usr.id = :val')
            ->setParameter('val', $usrId)
            ->orderBy('cnt.containerName', 'asc')
            ->getQuery()
            ->getArrayResult();
    }

    public function findContainersForLoggedUserById(string $usrId, int $containerId) {
        return $this->createQueryBuilder('usr')
            ->select('cnt.containerName', 'cnt.visibility', 'u2c.mode')
            ->innerJoin('usr.u2container', 'u2c')
            ->innerJoin('u2c.container', 'cnt')
            ->andWhere('usr.id = :usrId')
            ->andWhere('cnt.id = : cntId')
            ->setParameters(['usrId' => $usrId, 'cntId' => $containerId])
            ->getQuery()
            ->getArrayResult();
    }

    public function isContainerForLoggedUserByName(string $usrId, string $containerName) {
        return $this->createQueryBuilder('usr')
            ->select('1')
            ->innerJoin('usr.u2container', 'u2c')
            ->innerJoin('u2c.container', 'cnt')
            ->andWhere('usr.id = :usrId')
            ->setParameter('usrId', $usrId)
            ->andWhere('cnt.containerName = :cntName')
            ->setParameter('cntName', $containerName)
            ->getQuery()
            ->getArrayResult();
    }

    public function isContainerForLoggedUserByContainerId(string $usrId, int $containerId) {
        return $this->createQueryBuilder('usr')
            ->select('1')
            ->innerJoin('usr.u2container', 'u2c')
            ->innerJoin('u2c.container', 'cnt')
            ->andWhere('usr.id = :usrId')
            ->setParameter('usrId', $usrId)
            ->andWhere('cnt.id = :cntId')
            ->setParameter('cntId', $containerId)
            ->getQuery()
            ->getArrayResult();
    }

    public function isContainerForLoggedUserByContainerIdRW(string $usrId, int $containerId) {
        return $this->createQueryBuilder('usr')
            ->select('1')
            ->innerJoin('usr.u2container', 'u2c')
            ->innerJoin('u2c.container', 'cnt')
            ->andWhere('usr.id = :usrId')
            ->setParameter('usrId', $usrId)
            ->andWhere('cnt.id = :cntId')
            ->setParameter('cntId', $containerId)
            ->andWhere('u2c.mode in (\'RW\', \'RWM\')')
            ->getQuery()
            ->getArrayResult();
    }

    public function isContainerForLoggedUserByContainerIdRWM(string $usrId, int $containerId) {
        return $this->createQueryBuilder('usr')
            ->select('1')
            ->innerJoin('usr.u2container', 'u2c')
            ->innerJoin('u2c.container', 'cnt')
            ->andWhere('usr.id = :usrId')
            ->setParameter('usrId', $usrId)
            ->andWhere('cnt.id = :cntId')
            ->setParameter('cntId', $containerId)
            ->andWhere('u2c.mode = \'RWM\'')
            ->getQuery()
            ->getArrayResult();
    }

}
