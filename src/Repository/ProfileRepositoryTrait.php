<?php

namespace App\Repository;

use App\Entity\User;

Trait ProfileRepositoryTrait
{
    /**
     * Cette fonction permet de récupérer un profil à partir d'un user
     * @return App\Entity\lientC|App\Entity\Student|App\Entity\Teacher
     */
    public function findOneByUser(User $user, $role = '')
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->andWhere('p.user = :user')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('user', $user)
            ->setParameter('role', "%{$role}%")
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}