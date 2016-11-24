<?php

namespace Drupal\multisite\Manager;

interface MultisiteManagerInterface
{
    /**
     * Get current site entity.
     * @return \Drupal\multisite\Entity\Site
     */
    public function getSite();
}