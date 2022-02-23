<?php

namespace SixF\Janolaw\Extension;

use DateInterval;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Folder;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\SiteConfig\SiteConfig;
use SixF\Janolaw\Control\JanolawClient;

/**
 * Created by PhpStorm.
 * User: marcokernler
 * Date: 18.12.14
 * Time: 14:18
 */
class JanolawSiteConfig extends DataExtension
{
  /**
   * @var string
   */
  private static $default_cache_time = "8";

  /**
   * @var string[]
   */
  private static $db = [
    "JanolawLastSync" => "Datetime",
    "JanolawUserID" => "Varchar(100)",
    "JanolawShopID" => "Varchar(100)",
    "JanolawCacheTime" => "Varchar(25)",
    "JanolawAPIVersion" => "Varchar(25)",
    "JanolawSyncDocumentFiles" => "Boolean",
  ];

  /**
   * @var string[]
   */
  private static $has_one = [
    "JanolawTermsPage" => SiteTree::class,
    "JanolawImprintPage" => SiteTree::class,
    "JanolawRevocationPage" => SiteTree::class,
    "JanolawPrivacyPage" => SiteTree::class,
    "JanolawRevocationFormPage" => SiteTree::class,
    "JanolawAssetsFolder" => Folder::class,
  ];

  /**
   * @param SiteConfig $config
   * @return bool
   */
  public static function has_valid_config(SiteConfig $config): bool
  {
    // Injector::inst()->get(LoggerInterface::class)->debug(sprintf("%s | %s -> %s", $config->JanolawUserID, $config->JanolawShopID, (strlen($config->JanolawUserID) > 0 && strlen($config->JanolawShopID) > 0 ? "true" : "false")));
    return strlen($config->JanolawUserID) > 0 && strlen($config->JanolawShopID) > 0;
  }

  /**
   * Return whether we should update the contents
   * from the janolaw api. Determined by the cache-time
   * property.
   * @param SiteConfig $config
   * @return bool
   */
  public static function must_update(SiteConfig $config): bool
  {
    //
    if (!$config->JanolawLastSync) return true;

    //
    $now = new DateTime();
    $nextSync = new DateTime($config->JanolawLastSync);
    $nextSync->add(self::cache_time_interval());

    // Injector::inst()->get(LoggerInterface::class)->debug(sprintf("now: %s | lastSync: %s | nextSync: %s | elapsed: %s", $now->format('Y-m-d H:i:s'), $config->JanolawLastSync, $nextSync->format('Y-m-d H:i:s'), ($now > $nextSync ? "true" : "false")));

    return $now > $nextSync;
  }

  /**
   * @throws Exception
   */
  protected static function cache_time_interval(): DateInterval
  {
    $defaultCacheTime = Config::inst()->get(self::class, "default_cache_time");
    return new DateInterval(sprintf("PT%sH", $defaultCacheTime));
  }


  public function Sync()
  {
    // Injector::inst()->get(LoggerInterface::class)->debug("Sync()");
    //
    if (self::has_valid_config($this->owner)) {
      //
      $janolawClient = new JanolawClient();
      //
      $this->_processPage($janolawClient, $this->owner->JanolawTermsPage());
      $this->_processPage($janolawClient, $this->owner->JanolawTermsPage());
      $this->_processPage($janolawClient, $this->owner->JanolawImprintPage());
      $this->_processPage($janolawClient, $this->owner->JanolawRevocationPage());
      $this->_processPage($janolawClient, $this->owner->JanolawPrivacyPage());

      $this->owner->JanolawLastSync = DBDatetime::now()->Rfc2822();
      $this->owner->write();
    }
  }


  /**
   * @param FieldList $fieldList
   */
  public function updateCMSFields(FieldList $fieldList)
  {
    //
    $tabTitle = _t("SixF\Janolaw\Extension\JanolawSiteConfig.TAB_TITLE", "Janolaw");

    // create the cache time textfield
    $txtCacheTime = new TextField("JanolawCacheTime", _t('SixF\Janolaw\Extension\JanolawSiteConfig.CACHE_TIME', 'Cache Time'));
    // populate the field with the default value if empty
    if (strlen($this->owner->CacheTime) == 0) {
      $txtCacheTime->setAttribute("value", Config::inst()->get(JanolawSiteConfig::class, "default_cache_time"));
    }

    //
    $txtApiVersion = new TextField("JanolawAPIVersion", _t('SixF\Janolaw\Extension\JanolawSiteConfig.API_VERSION', 'API Version'));
    $txtApiVersion->setDisabled(true);

    $txtLastSync = DatetimeField::create("JanolawLastSync", "");
    $txtLastSync->setDisabled(true);

    //
    $fieldList->addFieldsToTab("Root." . $tabTitle, [
      new TextField("JanolawUserID", _t('SixF\Janolaw\Extension\JanolawSiteConfig.USERID', 'User ID')),
      new TextField("JanolawShopID", _t('SixF\Janolaw\Extension\JanolawSiteConfig.SHOPID', 'Shop ID')),
      $txtCacheTime,
      $txtApiVersion,
      $txtLastSync,
      new CheckboxField("JanolawSyncDocumentFiles", _t('SixF\Janolaw\Extension\JanolawSiteConfig.SYNC_DOCUMENT_FILES', 'Sync document files?')),

      new LiteralField("Line1", "<p>&nbsp;</p>"),

      new TreeDropdownField("JanolawTermsPageID", _t('SixF\Janolaw\Extension\JanolawSiteConfig.TERMS_PAGE', 'Terms Page'), SiteTree::class),
      new TreeDropdownField("JanolawImprintPageID", _t('SixF\Janolaw\Extension\JanolawSiteConfig.IMPRINT_PAGE', 'Imprint Page'), SiteTree::class),
      new TreeDropdownField("JanolawRevocationPageID", _t('SixF\Janolaw\Extension\JanolawSiteConfig.REVOCATION_PAGE', 'Revocation Page'), SiteTree::class),
      new TreeDropdownField("JanolawPrivacyPageID", _t('SixF\Janolaw\Extension\JanolawSiteConfig.PRIVACY_PAGE', 'Privacy Page'), SiteTree::class),
      new TreeDropdownField("JanolawRevocationFormPageID", _t('SixF\Janolaw\Extension\JanolawSiteConfig.REVOCATION_FORM_PAGE', 'Revocation Form Page'), SiteTree::class),
      new TreeDropdownField("JanolawAssetsFolderID", _t('SixF\Janolaw\Extension\JanolawSiteConfig.ASSETS_FOLDER', 'Assets Folder'), Folder::class),
    ]);
  }


  public function onAfterWrite()
  {
    parent::onAfterWrite();

    //
    if (self::has_valid_config($this->owner) && self::must_update($this->owner)) {
      //
      $this->Sync();
    }
  }

  /**
   * @param JanolawClient $client
   * @param SiteTree $page
   * @return void
   */
  protected function _processPage(JanolawClient $client, SiteTree $page): void
  {
    //
    if ($page->exists()) {
      try {
        //
        $content = $client->getTermsHtml();

        $page->Content = $content;
        $page->write();
      } catch (Exception $e) {
        //user_error($e->getMessage());
        Injector::inst()->get(LoggerInterface::class)->error(sprintf("[JanolawSiteConfig] => Error: %s", $e->getMessage()));
      }
    }
  }
}
