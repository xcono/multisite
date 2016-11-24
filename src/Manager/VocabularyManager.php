<?php

namespace Drupal\multisite\Manager;

use Drupal\multisite\Entity\SiteInterface;
use Drupal\taxonomy\Entity\Vocabulary;

class VocabularyManager
{
    /**
     * Create a vocabulary based on vid and name.
     * @param $vid
     * @param $name
     * @return \Drupal\Core\Entity\EntityInterface|static
     * @throws \Exception
     */
    public function build($vid, $name)
    {

        $vocabularies = Vocabulary::loadMultiple();

        if (isset($vocabularies[$vid])) {
            throw new \Exception(\Drupal::translation()->translate('Vocabulary @vid already exist', ['@vid' => $vid]));
        }

        $vocabulary = Vocabulary::create([
            'vid' => $vid,
            'name' => $name,
        ]);

        $vocabulary->save();

        return $vocabulary;
    }

    /**
     * @param SiteInterface $site
     * @throws \Exception
     */
    public function buildFromSite(SiteInterface $site)
    {

        try {

            // build vocabulary
            $vocabulary = $this->build($site->id(), $site->label());
            $site->set('vocabulary', $site->id());

            // add vocabulary to list of available for product_catalog field
            $config_factory = \Drupal::configFactory();
            $config = $config_factory->getEditable('field.field.node.product.field_product_catalog');

            $settings = $config->get('settings');
            $settings['handler_settings']['target_bundles'][$vocabulary->id()] = $vocabulary->id();

            $config->set('settings', $settings);
            $config->save();

        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}