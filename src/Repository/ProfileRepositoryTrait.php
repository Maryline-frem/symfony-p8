<?php

namespace App\Repository;

use App\Entity\User;

Trait ProfileRepositoryTrait
{
    /**
     * Cette fonction permet de récupérer un profil à partir d'un user
     * @return App\Entity\lientC|App\Entity\Student|App\Entity\Teacher
     */
    public function findOneByUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}