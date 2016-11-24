<?php

namespace Drupal\multisite;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\multisite\Entity\Site;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\VocabularyInterface;

/**
 * Provides a listing of Site entities.
 */
class SiteListBuilder extends ConfigEntityListBuilder {

  private $vocabularies;

  /**
   * @param $id
   * @return VocabularyInterface
   */
  public function getVocabularyLink($id)
  {
    if($this->vocabularies === null) {
      $this->vocabularies = Vocabulary::loadMultiple();
    }

    $link = '';

    if($id && !empty($this->vocabularies[$id])) {

      $vocabulary = $this->vocabularies[$id];
      $link = Link::createFromRoute($vocabulary->label(), 'entity.taxonomy_vocabulary.overview_form', ['taxonomy_vocabulary' => $vocabulary->id()]);
    }

    return $link;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {

    $header['label'] = $this->t('Site');
    $header['domain'] = $this->t('Domain');
    $header['company'] = $this->t('Company');
    $header['catalog'] = $this->t('Catalog');
    $header['theme'] = $this->t('Theme');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {

    $row['label'] = $entity->label();
    $row['domain'] = $entity->getDomain();
    $row['company'] = $entity->getCompany();
    $row['catalog'] = $this->getVocabularyLink($entity->id());
    $row['theme'] = $entity->getTheme();

    return $row + parent::buildRow($entity);
  }

}
