<?php
include(dirname(__FILE__).'/../../config/config.inc.php');

//namespace Netopia\Netcard\Controller\Adminhtml\Tools;



class NetopiaSendActivation
{
    protected $jsonData;
    protected $encData;

    private $outEnvKey  = null;
    private $outEncData = null;

    const ERROR_LOAD_X509_CERTIFICATE = 0x10000001;
    const ERROR_ENCRYPT_DATA          = 0x10000002;

    public function execute()
    {
        $ntpDeclare = array (
                      'completeDescription' => (bool) Configuration::get('NETOPIA_conditions_complete_description', null)=="1" ? true : false,
                      'priceCurrency'       => (bool) Configuration::get('NETOPIA_conditions_prices_currency', null)=="1" ? true : false,
                      'contactInfo'         => (bool) Configuration::get('NETOPIA_conditions_clarity_contact', null)=="1" ? true : false,
                      'forbiddenBusiness'   => (bool) Configuration::get('NETOPIA_conditions_forbidden_business', null)=="1" ? true : false
                    );

        $ntpUrl = array(
                  'termsAndConditions'      => $this->parsURL(Configuration::get('NETOPIA_terms_conditions_url', null)),
                  'privacyPolicy'           => $this->parsURL(Configuration::get('NETOPIA_privacy_policy_url', null)),
                  'deliveryPolicy'          => $this->parsURL(Configuration::get('NETOPIA_delivery_policy_url', null)),
                  'returnAndCancelPolicy'   => $this->parsURL(Configuration::get('NETOPIA_return_cancel_policy_url', null)),
                  'gdprPolicy'              => $this->parsURL(Configuration::get('NETOPIA_gdpr_policy_url', null))
                  );

        $ntpImg = array(
                  'netopiaLogoLink'     => $this->parsURL(Configuration::get('NETOPIA_image_netopia_logo_link', null)),
                  'logoStatus'          => Configuration::get('NETOPIA_conditions_logo_status', null) // need to be decided
                );

        $this->jsonData = $this->makeActivateJson($ntpDeclare, $ntpUrl, $ntpImg);
        $this->encrypt();

        $this->encData = array(
          'env_key' => $this->getEnvKey(),
          'data'    => $this->getEncData()
          );

        /*
         * To see Encrypted Data
         * */
        // echo "<pre>";
        // print_r($this->encData);
        // echo "</pre>";

        $result = json_decode($this->sendJsonCurl());


        if($result->code == 200) {
          $response = array(
            'status' =>  true,
            'msg' => 'succesfully sent your request' );
        } else {
          $response = array(
            'status' =>  false,
            'msg' => 'Error, '.$result->message );
        }
        /*
        * Send response to JS
        */
        echo (json_encode($response));

    }

    public function getConfigData($field)
    {
        $str = Configuration::get($field, null);
        return $str;
    }

    public function has_ssl() {
        // $domain = "https://netopia-payments.com"; // A example site with SSL
        $domain = 'https://'.$_SERVER['HTTP_HOST'];
        $stream = stream_context_create (array("ssl" => array("capture_peer_cert" => true)));
        if( $read = @fopen($domain, "rb", false, $stream)){
            $cont = stream_context_get_params($read);
            if(isset($cont["options"]["ssl"]["peer_certificate"])){
                $var = ($cont["options"]["ssl"]["peer_certificate"]);
                $result = (!is_null($var)) ? true : false;
            }else {
                $result = false;
            }            
        } else {
            $result = false;
        }
        
        return array(
                'status' => $result,
            );
    }
    

    public function parsURL($pageUrl) {
      $hostName = parse_url($pageUrl, PHP_URL_HOST);
      if(!is_null($hostName)) {
       if($this->verifyHost($hostName))
          return $pageUrl;
        else {
          $tmpPageUrl = substr($pageUrl, strpos($pageUrl, $hostName) + strlen($hostName));
          return 'https://'.$_SERVER['HTTP_HOST'].$tmpPageUrl;
        }
      }else {
        return $this->generateURL($pageUrl);
      }
    }

    public function verifyHost($hostName) {
      if($hostName === $_SERVER['HTTP_HOST'])
        return true;
      else
        return false;
    }

    public function generateURL($pageUrl) {
        return 'https://'.$_SERVER['HTTP_HOST'].'/'.$pageUrl;
    }

    // to add https in URL  
    function addHttp($url) { 
      // Search the pattern 
      if (!preg_match("~^(?:f|ht)tps?://~i", $url)) { 
          // If not exist then add http 
          $url = "https://" . $url; 
      } 
      return $url; 
    } 



    protected function _getUploadDir()
    {
        $certificateDir = getcwd().DIRECTORY_SEPARATOR.'certificates'.DIRECTORY_SEPARATOR;
        return $certificateDir;
    }


    function makeActivateJson($declareatins, $urls, $images) {
      $jsonData = array(
        "sac_key" => Configuration::get('NETOPIA_SIGNATURE', null),
        "agreements" => array(
              "declare" => $declareatins,
              "urls"    => $urls,
              "images"  => $images,
              "ssl"     => $this->has_ssl()
            ),
        "lastUpdate" => date("c", strtotime(date("Y-m-d H:i:s"))), // To have Date & Time format on RFC3339
        "platform" => 'Prestashop 1.7');
      
      // die(print_r($jsonData));

      $post_data = json_encode($jsonData, JSON_FORCE_OBJECT);

      // die(print_r($post_data));

      return $post_data;
    }


    public function encrypt()
      {
        $x509FilePath = $this->_getUploadDir().$this->getConfigData('NETOPIA_LIVE_PUB_KEY');
        if (!is_file($x509FilePath) || !file_exists($x509FilePath)) {
            $this->outEncData = null;
            $this->outEnvKey  = null;
            $errorMessage = "Error, The actual public key file is not exist.";
            throw new \Exception($errorMessage, self::ERROR_LOAD_X509_CERTIFICATE);
        }

        $publicKey = openssl_pkey_get_public("file://{$x509FilePath}");
        if($publicKey === false)
          {
            $this->outEncData = null;
            $this->outEnvKey  = null;
            $errorMessage = "Error while loading X509 public key certificate! Reason:";
            while(($errorString = openssl_error_string()))
            {
              $errorMessage .= $errorString . "\n";
            }
            throw new \Exception($errorMessage, self::ERROR_LOAD_X509_CERTIFICATE);
          }

        $srcData = $this->jsonData;
        $publicKeys = array($publicKey);
        $encData  = null;
        $envKeys  = null;
        $result   = openssl_seal($srcData, $encData, $envKeys, $publicKeys);
        if($result === false)
          {
            $this->outEncData = null;
            $this->outEnvKey  = null;
            $errorMessage = "Error while encrypting data! Reason:";
            while(($errorString = openssl_error_string()))
            {
              $errorMessage .= $errorString . "\n";
            }
            throw new Exception($errorMessage, self::ERROR_ENCRYPT_DATA);
          }

        $this->outEncData = base64_encode($encData);
        $this->outEnvKey  = base64_encode($envKeys[0]);
      }

      public function getEnvKey()
        {
          return $this->outEnvKey;
        }

      public function getEncData()
        {
          return $this->outEncData;
        }

      public function sendJsonCurl() {
        $url = 'https://netopia-payments-user-service-api-fqvtst6pfa-ew.a.run.app/financial/agreement/add2';
        $ch = curl_init($url);

        $payload = json_encode($this->encData);

        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the POST request
        $result = curl_exec($ch);

          if (!curl_errno($ch)) {
              switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                  case 200:  # OK
                      $arr = array(
                          'code'    => $http_code,
                          'message' => "You send your request, successfully",
                          'data'    => json_decode($result)
                      );
                      break;
                  case 404:  # Not Found
                      $arr = array(
                          'code'    => $http_code,
                          'message' => "You send request to wrong URL"
                      );
                      break;
                  case 400:  # Bad Request
                      $arr = array(
                          'code'    => $http_code,
                          'message' => "You send Bad Request"
                      );
                      break;
                  case 405:  # Method Not Allowed
                      $arr = array(
                          'code'    => $http_code,
                          'message' => "Your method of sending data are Not Allowed"
                      );
                      break;
                  default:
                      $arr = array(
                          'code'    => $http_code,
                          'message' => "Opps! Something happened, verify how you send data & try again!!!"
                      );
              }
          } else {
              $arr = array(
                  'code'    => 0,
                  'message' => "Opps! There is some problem, you are not able to send data!!!"
              );
          }


        // Close cURL resource
        curl_close($ch);

        $finalResult = json_encode($arr, JSON_FORCE_OBJECT);
        return $finalResult;
      }
}
$netopiaSendActivation = new NetopiaSendActivation();
$netopiaSendActivation->execute();
?>
