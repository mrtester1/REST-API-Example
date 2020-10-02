<?php

namespace App\Repository;

use App\Entity\Acl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Acl|null find($id, $lockMode = null, $lockVersion = null)
 * @method Acl|null findOneBy(array $criteria, array $orderBy = null)
 * @method Acl[]    findAll()
 * @method Acl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AclRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Acl::class);
    }

    private function stripUrl(string $url) :string {
        $matches = [];
        $base_url = $url;
        if (preg_match('/\/(.*?)\/(.*?)\/(.*?)\//', $url, $matches)) {
            $base_url = "/".$matches[1]."/".$matches[2]."/".$matches[3];
        }
        return $base_url;
    }

    public function hasAccess(string $group, string $url, string $method) :bool {
        $result = $this->findOneBy([
                'group_id' => $group,
                'url' => $this->stripUrl($url),
                'method' => $method
            ]);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
