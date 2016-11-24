<?php

namespace Drupal\multisite\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Annotation\ConfigEntityType;
use Drupal\file\Entity\File;

/**
 * Defines the Site entity.
 *
 * @ConfigEntityType(
 *   id = "site",
 *   label = @Translation("Site"),
 *   handlers = {
 *     "list_builder" = "Drupal\multisite\SiteListBuilder",
 *     "form" = {
 *       "add" = "Drupal\multisite\Form\SiteForm",
 *       "edit" = "Drupal\multisite\Form\SiteForm",
 *       "delete" = "Drupal\multisite\Form\SiteDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\multisite\SiteHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "site",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/site/{site}",
 *     "add-form" = "/admin/structure/site/add",
 *     "edit-form" = "/admin/structure/site/{site}/edit",
 *     "delete-form" = "/admin/structure/site/{site}/delete",
 *     "collection" = "/admin/structure/site"
 *   }
 * )
 */
class Site extends ConfigEntityBase implements SiteInterface
{

    /**
     * The Site ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The Site label.
     *
     * @var string
     */
    protected $label;

    /**
     * The Site domain.
     *
     * @var string
     */
    protected $domain;

    /**
     * The Site company name.
     *
     * @var string
     */
    protected $company;

    /**
     * The Site phone.
     *
     * @var string
     */
    protected $phone;

    /**
     * The Site e-mail.
     *
     * @var string
     */
    protected $mail;

    /**
     * The Site city.
     *
     * @var string
     */
    protected $city;

    /**
     * The Site address.
     *
     * @var string
     */
    protected $address;

    /**
     * The Site brand name.
     *
     * @var string
     */
    protected $brand;

    /**
     * The Site slogan.
     *
     * @var string
     */
    protected $slogan;

    /**
     * The Site theme.
     *
     * @var string
     */
    protected $theme;

    /**
     * The Site logo fid.
     *
     * @var string
     */
    protected $logo;

    /**
     * The Site's promo image.
     *
     * @var string
     */
    protected $promo_image;

    /**
     * The Site's promo theme.
     *
     * @var string
     */
    protected $promo_theme;

    /**
     * The Site's vocabulary.
     *
     * @var string
     */
    protected $vocabulary;


    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return string
     */
    public function getSlogan()
    {
        return $this->slogan;
    }

    /**
     * Get site file object.
     * @return \Drupal\file\FileInterface
     */
    public function getLogo()
    {
        if ($fid = $this->get('logo')) {
            return File::load(reset($fid));
        }

        return false;
    }

    /**
     * Get site file object.
     * @return \Drupal\file\FileInterface
     */
    public function getPromoImage()
    {
        if ($fid = $this->get('promo_image')) {
            return File::load(reset($fid));
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPromoTheme()
    {
        return $this->promo_theme;
    }

    /**
     * Get vocabulary id.
     * @return string
     */
    public function getVocabularyId()
    {
        return $this->get('vocabulary');
    }
}
