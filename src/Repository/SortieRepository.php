<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    /*    public function findNotSubscribeEvent($userConnected) : array{
            $query = $this->getEntityManager()->createQuery(
                'SELECT s
               FROM App\Entity\Sortie s
               JOIN App\Entity\User u
               ON s.user_id = u.id
               WHERE u.id != :userConnected'
            );
        $query->setParameter('userConnected', $userConnected);
            return $query->getResult();
        }*/

    //table sortie sans jointure, on n'a pas info des l'organisateur
    //table sortie avec jointure, on a l'info de l'organisateur, mais la liste des participants
    /*    public function findAllUsersEvent(): array
        {
            return $this->createQueryBuilder('s')
                ->innerJoin('s.user','u','WITH','u.users = :users')
                ->setParameter('users',$users)
                ->getQuery()
                ->getResult()
                ;
        }*/

    public function findAllUsersEvent($userConnected): array
    {
        $query = $this->createQueryBuilder('s');
        $query
            ->innerjoin('s.users', 'u')

            ->andWhere(':participant IN (u)')
            ->setParameter('participant', $userConnected);
//            ->setParameter('participants', 'u');
        $result = $query->getQuery();
//        dd($query);
        return $result->getResult();
    }

    /* Filtres */
    /*organisateur OK */
    public function findOrganizer($userConnected): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.user', 'u')
            ->andWhere('u.id = :userConnected')
            ->setParameter('userConnected', $userConnected)
            ->getQuery()
            ->getResult();
    }

    public function findSubscribeEvent($userConnected): array
    {
        $query = $this->createQueryBuilder('s');
        $query
            ->innerjoin('s.users', 'u')
            ->andWhere(':participant IN (u)')
            ->setParameter('participant', $userConnected);
        $result = $query->getQuery();
        return $result->getResult();
    }

    /*Non inscrit OK */
    public function findNotSubscribeEvent($userConnected): array
    {
        return $this->createQueryBuilder('s')
            ->join('s.user', 'u')
            ->andWhere('u.id != :userConnected')
            ->setParameter('userConnected', $userConnected)
            ->getQuery()
            ->getResult();
    }

    /*Sortie passées OK */
    public function findFinishedEvent(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.etat =5')
            ->getQuery()
            ->getResult();
    }

    /*Date debut OK */
    public function findEventByStartPeriod($dateSortieDebut): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateDebut >= :dateSortieDebut')
            ->setParameter('dateSortieDebut', $dateSortieDebut)
            ->getQuery()
            ->getResult();
    }

    /*Date Fin OK */
    public function findEventByStartEndPeriod($dateSortieFin): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateDebut <= :dateSortieFin')
            ->setParameter('dateSortieFin', $dateSortieFin)
            ->getQuery()
            ->getResult();
    }

    /*Searchbar OK */
    public function findByEventName($searchbar): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :searchbar')
            ->setParameter('searchbar', "%{$searchbar}%")
            ->getQuery()
            ->getResult();
    }
    /*    public function findSite($site) : array{
            return $this->createQueryBuilder('s')
                ->join('s.site','si')
                ->andWhere('si.nom = :site')
                ->setParameter('site',$site)
                ->getQuery()
                ->getResult()
                ;
        }*/
    /*Site ID OK */
    public function findSiteId($siteId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.site = :siteId')
            ->setParameter('siteId', $siteId)
            ->getQuery()
            ->getResult();
    }

    public function queryfilter($userConnected, $orgaCheckbox, $nonInscritCheckbox,$mesSortiesCheckbox, $searchbar, $sortiesFiniesCheckbox, $dateSortieDebut, $dateSortieFin, $siteId): array
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder
            ->join('s.user', 'user');

        if ($orgaCheckbox) {
            $queryBuilder
                ->andWhere('user.id = :userConnected')
                ->setParameter('userConnected', $userConnected);
        }
        if($mesSortiesCheckbox){
            $queryBuilder
                ->innerjoin('s.users', 'u')
                ->andWhere(':participant IN (u)')
                ->setParameter('participant', $userConnected);
        }
        if ($nonInscritCheckbox) {
            $queryBuilder
                ->andWhere('user.id != :userConnected')
                ->setParameter('userConnected', $userConnected);
        }
        if ($searchbar!='') {
            $queryBuilder
                ->andWhere('s.nom LIKE :searchbar')
                ->setParameter('searchbar', "%{$searchbar}%");
        }
        if ($sortiesFiniesCheckbox) {
            $queryBuilder->andWhere('s.etat =5');
        }
        if ($dateSortieDebut) {
            $queryBuilder
                ->andWhere('s.dateDebut >= :dateSortieDebut')
                ->setParameter('dateSortieDebut', $dateSortieDebut);
        }
        if ($dateSortieFin) {
            $queryBuilder
                ->andWhere('s.dateDebut <= :dateSortieFin')
                ->setParameter('dateSortieFin', $dateSortieFin);
        }
        if ($siteId) {
            $queryBuilder
                ->andWhere('s.site = :siteId')
                ->setParameter('siteId', $siteId);
        }
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}

