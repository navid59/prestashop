<?php
include(dirname(__FILE__).'/../../config/config.inc.php');

class NetopiaSelfValidation
{
    protected $_domain;
    public function execute()
    {
        $this->_domain = $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR;

        $declareValidation = $this->hasDeclarations();
        $pagesVerification = $this->hasMandatoryPages();
        $imgVerification = $this->hasMandatoryImages();
        $sslVerification = $this->hasSsl();

        $validateResult = array(
            'declare' => $declareValidation,
            'urls' => $pagesVerification,
            'images' => $imgVerification,
            'ssl' => $sslVerification
        );

        $isValid = true;
        $msg = null;
        foreach ($validateResult as $key => $value) {
            // echo "<hr>".$key.' - '.$value['status'];
            if ($value['status'] != 1) {
                $isValid = false;
                switch ($key) {
                    case 'declare':
                        $msg .= "Please, review the Conditions / Agreements / Not accpeted business section!!\n. ";
                        break;
                    case 'urls':
                        $msg .= "Please, review the mandatory pages URL!!\n. ";
                        break;
                    case 'images':
                        $msg .= "Please, review the mandatory logo links!!\n. ";
                        break;
                    case 'ssl':
                        $msg .= "Please, make sure to use Valid SSL Certificate!!\n. ";
                        break;
                }
            }
        }


        if ($isValid) {
            $jsonResponse = array(
                'status' => true,
                'msg' => 'Self validation is passed');
        } else {
            $jsonResponse = array(
                'status' => false,
                'msg' => "There is still problem on Validation\n. " . $msg);
        }
        /*
        * Send response to JS
        */


        echo(json_encode($jsonResponse));

    }

    public function hasDeclarations()
    {
        $isValid = 1;
        $declarations = array(
            'completeDescription' => Configuration::get('NETOPIA_conditions_complete_description', null),
            'priceCurrency' => Configuration::get('NETOPIA_conditions_prices_currency', null),
            'contactInfo' => Configuration::get('NETOPIA_conditions_clarity_contact', null),
            'forbiddenBusiness' => Configuration::get('NETOPIA_conditions_forbidden_business', null)
        );

        foreach ($declarations as $key => $value) {
            $declared[$key]['accepted'] = $value ? 1 : 0;
            if (!$value)
                $isValid = 0;
        }

        return array(
            'status' => $isValid,
            'result' => $declared
        );
    }

    public function hasMandatoryPages()
    {
        $isValid = 1;
        $mandatoryPages = array(
            'termsAndConditions'    => Configuration::get('NETOPIA_terms_conditions_url', null),
            'privacyPolicy'         => Configuration::get('NETOPIA_privacy_policy_url', null),
            'deliveryPolicy'        => Configuration::get('NETOPIA_delivery_policy_url', null),
            'returnAndCancelPolicy' => Configuration::get('NETOPIA_return_cancel_policy_url', null),
            'gdprPolicy'            => Configuration::get('NETOPIA_gdpr_policy_url', null)
        );

        foreach ($mandatoryPages as $key => $value) {
            $pages[$key]['url'] = $value;
            $checkUrl = $this->checkUrlValidation($value);
            if ($checkUrl['status'] == 0)
                $isValid = 0;
            $pages[$key]['status'] = $checkUrl['status'];
            $pages[$key]['code'] = $checkUrl['code'];
        }

        return array(
            'status' => $isValid,
            'result' => $pages
        );
    }

    public function hasMandatoryImages()
    {
        $isValid = 1;
        $MandatoryImages = array(
            // 'visaLogoLink'      => Configuration::get('NETOPIA_image_visa_logo_link', null),
            // 'masterLogoLink'    => Configuration::get('NETOPIA_image_master_logo_link', null),
            'netopiaLogoLink'   => Configuration::get('NETOPIA_image_netopia_logo_link', null)
        );

        foreach ($MandatoryImages as $key => $value) {
            $img[$key]['url'] = $value;
            $checkUrl = $this->checkUrlValidation($value);
            if ($checkUrl['status'] == 0)
                $isValid = 0;
            $img[$key]['status'] = $checkUrl['status'];
            $img[$key]['code'] = $checkUrl['code'];
        }

        return array(
            'status' => $isValid,
            'result' => $img
        );
    }

    function checkUrlValidation($url)
    {
        $isValid = false;
        $code = null;
        $url = 'http://' . $this->_domain . $url;
        if (isset($url) && is_string($url) && preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url)) {
            $ch = curl_init($url);
            if ($ch !== false) {
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                curl_exec($ch);
                if (!curl_errno($ch)) {
                    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    if ($code == 200) {
                        $isValid = true;
                    }
                }
            }
        }

        return array(
            'status' => $isValid,
            'code' => $code
        );
    }

    public function hasSsl()
    {
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
}
$netopiaSelfValidation = new NetopiaSelfValidation();
$netopiaSelfValidation->execute();
?>
