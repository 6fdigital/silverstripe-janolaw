<?php

namespace SixF\Janolaw\Control;

use Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\SiteConfig\SiteConfig;
use SixF\Janolaw\Extension\JanolawSiteConfig;


class JanolawClient
{
  use Configurable;

  private static $base_url = "https://www.janolaw.de/agb-service/shops";

  public const DOC_TYPE_TERMS = "agb";
  public const DOC_TYPE_IMPRINT = "impressum";
  public const DOC_TYPE_REVOCATION = "widerrufsbelehrung";
  public const DOC_TYPE_PRIVACY = "datenschutzerklaerung";
  public const DOC_TYPE_REVOCATION_FORM = "widerrufsformular";

  public const FILE_TYPE_HTML = "html";
  public const FILE_TYPE_PDF = "pdf";


  protected function _documentUrl(string $docType, string $fileType, string $language): string
  {
    //
    $config = SiteConfig::current_site_config();
    $baseUrl = Config::inst()->get(JanolawClient::class, "base_url"); //$this->config()->get("base_urL"); //Config::inst()->get("JanolawClient", "base_url");

    // check for user- and shop-id
    if (!JanolawSiteConfig::has_valid_config($config)) {
      user_error("Missing user- and shop-id for janolaw config. Please go to the Site-Config and add your user- and shop-id!");
    }

    // we must add the string '_include' after the
    // doc-type if file-type 'html' are requested
    $appendix = $fileType === self::FILE_TYPE_HTML ? "_include" : "";

    return sprintf("%s/%s/%s/%s%s.%s", $baseUrl, $config->JanolawUserID, $config->JanolawShopID, $docType, $appendix, $fileType);
  }

  /**
   * @throws Exception|\GuzzleHttp\Exception\GuzzleException
   */
  protected function _request($url): ?string
  {

    // Injector::inst()->get(LoggerInterface::class)->debug("url: " . $url);

    try {
      if (!$url || strlen($url) === 0) {
        throw new Exception("unable to perform api request. Received non valid url: '" . $url . "'");
      }


      $client = new Client();
      $response = $client->request("GET", $url);

      //
      if ($response->getStatusCode() !== 200) {
        throw new Exception("unable to load data from " . $url);
      }

      return $response->getBody();
    } catch (Exception $e) {
      throw new Exception($e->getMessage());;
    }
  }

  public function getTermsHtml(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_TERMS, self::FILE_TYPE_HTML, "de");

    return $this->_request($url);
  }

  public function getImprintHtml(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_IMPRINT, self::FILE_TYPE_HTML, "de");

    return $this->_request($url);
  }

  public function getRevocationHtml(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_REVOCATION, self::FILE_TYPE_HTML, "de");

    return $this->_request($url);
  }

  public function getPrivacyHtml(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_PRIVACY, self::FILE_TYPE_HTML, "de");

    return $this->_request($url);
  }

  public function getRevocationFormHtml(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_REVOCATION_FORM, self::FILE_TYPE_HTML, "de");

    return $this->_request($url);
  }

  public function getTermsPdf(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_TERMS, self::FILE_TYPE_PDF, "de");

    return $this->_request($url);
  }

  public function getImprintPdf(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_IMPRINT, self::FILE_TYPE_PDF, "de");

    return $this->_request($url);
  }

  public function getRevocationPdf(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_REVOCATION, self::FILE_TYPE_PDF, "de");

    return $this->_request($url);
  }

  public function getPrivacyPdf(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_PRIVACY, self::FILE_TYPE_PDF, "de");

    return $this->_request($url);
  }

  public function getRevocationFormPdf(): ?string
  {
    //
    $url = $this->_documentUrl(self::DOC_TYPE_REVOCATION_FORM, self::FILE_TYPE_PDF, "de");

    return $this->_request($url);
  }
}
