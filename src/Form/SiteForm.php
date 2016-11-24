<?php

namespace Drupal\multisite\Form;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\multisite\Manager\VocabularyManager;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\VocabularyInterface;

/**
 * Class SiteForm.
 *
 * @package Drupal\multisite\Form
 */
class SiteForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $form = parent::form($form, $form_state);
    $site = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $site->label(),
      '#description' => $this->t("Label for the Site."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $site->id(),
      '#machine_name' => [
        'exists' => '\Drupal\multisite\Entity\Site::load',
      ],
      '#disabled' => !$site->isNew(),
    ];

    $form['domain'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Domain'),
        '#maxlength' => 255,
        '#default_value' => $site->get('domain'),
        '#required' => true,
    ];

    /* You will need additional form elements for your custom properties. */

    $form['contacts'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Contact'),
        '#open' => true,
    ];

    foreach (['company', 'phone', 'mail', 'city', 'address'] as $field) {

      $form['contacts'][$field] = [
          '#type' => 'textfield',
          '#title' => $this->t(Unicode::ucfirst($field)),
          '#maxlength' => 255,
          '#default_value' => $site->get($field),
          '#required' => true,
      ];
    }

    $form['settings'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Settings'),
        '#open' => true,
    ];

    foreach (['brand', 'slogan'] as $field) {

      $form['settings'][$field] = [
          '#type' => 'textfield',
          '#title' => $this->t(Unicode::ucfirst($field)),
          '#maxlength' => 255,
          '#default_value' => $site->get($field),
          '#required' => true,
      ];
    }

    $form['settings']['brand_about'] = [
        '#type' => 'textarea',
        '#title' => $this->t('About'),
        '#maxlength' => 512,
        '#default_value' => $site->get('brand_about'),
        '#required' => true,
    ];

    $vocabularies = Vocabulary::loadMultiple();

    if(!isset($vocabularies[$site->get('vocabulary')])) {

      $form['settings']['create_catalog'] = array(
          '#type' => 'checkbox',
          '#title' => $this->t('Enable catalog'),
      );
    }
    else {
      /** @var VocabularyInterface $vocabulary */
      $vocabulary = $vocabularies[$site->get('vocabulary')];
      
      $form['settings']['create'] = array(
          '#type' => 'markup',
          '#markup' => $this->t('Using <a target="_blank" href="@vocabulary-url">@vocabulary-label</a> catalog.', [
              '@vocabulary-url' => Url::fromRoute('entity.taxonomy_vocabulary.overview_form', ['taxonomy_vocabulary' => $vocabulary->id()])->toString(),
              '@vocabulary-label' => $vocabulary->label()
          ]),
      );
    }


    $form['appearance'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Appearance'),
        '#open' => true,
    ];

    $form['appearance']['theme'] = [
        '#type' => 'select',
        '#title' => $this->t('Theme'),
        '#default_value' => $site->get('theme'),
        '#options' => $this->getThemes(),
    ];

    $this->getThemeVariants($form, $form_state);


    foreach (['logo'] as $field) {

      $form['appearance'][$field] = [
          '#type' => 'managed_file',
          '#title' => $this->t('Logo'),
          '#default_value' => $site->get($field),
          '#upload_location' => 'public://multisite/',
          '#required' => false,
      ];
    }

    $form['promo'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Promotion'),
        '#open' => true,
    ];

    $form['promo']['promo_image'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('Background'),
        '#default_value' => $site->get('promo_image'),
        '#upload_location' => 'public://multisite/',
        '#required' => false,
    ];

    $form['promo']['promo_theme'] = [
        '#type' => 'select',
        '#title' => $this->t('Theme'),
        '#default_value' => $site->get('promo_theme') ?: 'default_light',
        '#options' => [
            'default_light' => 'Default Light',
            'default_dark' => 'Default Dark'
        ],
    ];

    $form['about'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Company'),
        '#open' => true,
    ];

    $form['about']['company_about'] = [
        '#type' => 'textarea',
        '#title' => $this->t('About'),
        '#maxlength' => 2048,
        '#default_value' => $site->get('company_about'),
        '#required' => true,
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state)
  {

    // Create catalog
    if($form_state->getValue('create_catalog')) {

      $vm = new VocabularyManager();

      try {
        $vm->buildFromSite($this->entity);
      }
      catch (\Exception $e) {
        $form_state->setErrorByName('create_catalog', $this->t('@message', ['@message' => $e->getMessage()]));
      }
    }
  }


  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
/*
    if($this->entity->getLogo() && !$this->entity->getLogo()->isPermanent()) {
      $this->entity->getLogo()->setPermanent();
      $this->entity->getLogo()->save();
    }
*/

    $site = $this->entity;
    $status = $site->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Site.', [
          '%label' => $site->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Site.', [
          '%label' => $site->label(),
        ]));
    }
    $form_state->setRedirectUrl($site->urlInfo('collection'));
  }

  /**
   * Get list of themes.
   * @return array
   */
  private function getThemes()
  {

    $list = [];
    $themes = system_list('theme');

    foreach ($themes as $theme) {

      if(!empty($theme->base_theme) && $theme->base_theme === 'xtheme') {
        $list[$theme->getName()] = $theme->info['name'];
      }
    }

    return $list;
  }


  private function getThemeVariants(array &$form, FormStateInterface $form_state) {

    $themes = $this->getThemes();
    $theme = ($form_state->getValue('theme') ?? $this->entity->getTheme()) ?? key($themes);

    $variants = theme_get_setting('variant', $theme);

    $form['appearance']['theme_variant'] = [
        '#type' => 'select',
        '#title' => $this->t('Variant'),
        '#default_value' => $this->entity->get('theme_variant') ?? '',
        '#options' => $variants,
    ];
  }
}
