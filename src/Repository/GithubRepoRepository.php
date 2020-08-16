<?php

namespace App\Repository;

use App\Entity\GithubRepo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GithubRepo|null find($id, $lockMode = null, $lockVersion = null)
 * @method GithubRepo|null findOneBy(array $criteria, array $orderBy = null)
 * @method GithubRepo[]    findAll()
 * @method GithubRepo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GithubRepoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GithubRepo::class);
    }

    /**
     * Get all repos from database, keyed by repository_id and sorted by stars descending.
     * 
     * @return GithubRepo[] Returns an array of GithubRepo objects.
     */
    public function findAllKeyedByRepositoryId()
    {
        $rows = $this->createQueryBuilder('r')
            ->orderBy('r.stars', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        $repos = [];
        foreach ($rows as $row)
        {
            $repos[$row->getRepositoryId()] = $row;
        }
        return $repos;
    }

    /**
     * Delete repos from the database specified by repository_ids.
     * 
     * @param array|int $repository_ids Can be either an array of repository_ids, or a single repository_id
     */
    public function deleteByRepositoryId($repository_ids)
    {
        // Only proceed if $repository_ids is not empty.
        if (!empty($repository_ids))
        {
            // Convert $repository_ids to an array if it is not already.
            if (!is_array($repository_ids)) $repository_ids = [$repository_ids];
            $this->createQueryBuilder('r')
                ->delete()
                ->where('r.repository_id IN (:ids)')
                ->setParameter('ids', $repository_ids)
                ->getQuery()
                ->execute()
            ;
        }
    }
}
