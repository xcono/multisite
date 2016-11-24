<?php

namespace Drupal\multisite\Repository;


use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\multisite\Entity\Site;

class SiteRepository {

    private $entityService;
    /**
     * SiteRepository constructor.
     * @param $entityService
     */
    public function __construct($entityService)
    {
        $this->entityService = $entityService;
    }

    /**
     * @return QueryInterface
     */
    public function getQuery()
    {
        return $this->entityService->get('site');
    }

    /**
     * @param $host
     * @return bool|Site
     */
    public function findByDomain($host)
    {

        try {

            $ids = $this->getQuery()
                ->condition('domain', $host)
                ->execute();

            $site = $ids ? \Drupal::entityTypeManager()->getStorage('site')->load(reset($ids)) : false;
        }
        catch (\Exception $e) {

            \Drupal::logger('multisite')->error('Fetch current site from database failed for host @host. @message', [
                '@host' => $host,
                '@message' => $e->getMessage()
            ]);

            $site = false;
        }

        return $site;
    }
}