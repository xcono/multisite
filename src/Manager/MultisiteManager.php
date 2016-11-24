<?php

namespace Drupal\multisite\Manager;


use Drupal\multisite\Repository\SiteRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class MultisiteManager implements MultisiteManagerInterface
{

    private $request;
    private $repo;

    /**
     * MultisiteManager constructor.
     * @param RequestStack $requestStack
     * @param SiteRepository $repo
     */
    public function __construct(RequestStack $requestStack, SiteRepository $repo)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->repo = $repo;
    }

    /**
     * Get current site entity.
     * @return \Drupal\multisite\Entity\Site
     */
    public function getSite()
    {

        $site = &drupal_static(__CLASS__ . __METHOD__);

        if(!$site) {

            $site = $this->repo->findByDomain($this->request->getHost());
        }

        return $site;
    }

}