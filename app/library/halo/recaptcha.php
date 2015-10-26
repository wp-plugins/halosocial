<?php
/**
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          https://developers.google.com/recaptcha/docs/php
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * @copyright Copyright (c) 2014, Google Inc.
 * @link      http://www.google.com/recaptcha
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * A ReCaptchaResponse is returned from checkAnswer().
 */
class HALOReCaptchaResponse
{
    public $success;
    public $errorCodes;
}

class HALOReCaptcha
{
    private static $_signupUrl = "https://www.google.com/recaptcha/admin";
    private static $_siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";
    private $_secret;
    private static $_version = "php_1.0";

    /**
     * Constructor.
     *
     * @param string $secret shared secret between site and ReCAPTCHA server.
     */
    function HALOReCaptcha($secret=null)
    {
        if ($secret == null || $secret == "") {
            //load secret key from configuration
            $secret = HALOReCaptcha::getSecretKey('6LcBRQwTAAAAACPMCCZuY-BVrsUxiqOKv_Rv7fr3');
        }
        $this->_secret=$secret;
    }

    /**
     * return secret key settings
     *
     *
     * @return string secret key
     */
    public static function getSecretKey($default=null) {
        return HALOConfig::get('recaptcha.secret', $default);
    }
    
    /**
     * return secret key settings
     *
     *
     * @return string secret key
     */
    public static function getSiteKey($default=null) {
        return HALOConfig::get('recaptcha.site', $default);
    }
    
    
    /**
     * Encodes the given data into a query string format.
     *
     * @param array $data array of string elements to be encoded.
     *
     * @return string - encoded request.
     */
    private function _encodeQS($data)
    {
        $req = "";
        foreach ($data as $key => $value) {
            $req .= $key . '=' . urlencode(stripslashes($value)) . '&';
        }

        // Cut the last '&'
        $req=substr($req, 0, strlen($req)-1);
        return $req;
    }

    /**
     * Submits an HTTP GET to a reCAPTCHA server.
     *
     * @param string $path url path to recaptcha server.
     * @param array  $data array of parameters to be sent.
     *
     * @return array response
     */
    private function _submitHTTPGet($path, $data)
    {
        $req = $this->_encodeQS($data);
        $response = file_get_contents($path . $req);
        return $response;
    }

    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes
     * CAPTCHA test.
     *
     * @param string $remoteIp   IP address of end user.
     * @param string $response   response string from recaptcha verification.
     *
     * @return GBReCaptchaResponse
     */
    public function verifyResponse($remoteIp, $response)
    {
        // Discard empty solution submissions
        if ($response == null || strlen($response) == 0) {
            $recaptchaResponse = new HALOReCaptchaResponse();
            $recaptchaResponse->success = false;
            $recaptchaResponse->errorCodes = 'missing-input';
            return $recaptchaResponse;
        }

        $getResponse = $this->_submitHttpGet(
            self::$_siteVerifyUrl,
            array (
                'secret' => $this->_secret,
                'remoteip' => $remoteIp,
                'v' => self::$_version,
                'response' => $response
            )
        );
        $answers = json_decode($getResponse, true);
        $recaptchaResponse = new HALOReCaptchaResponse();

        if (isset($answers['success']) && trim($answers['success']) == true) {
            $recaptchaResponse->success = true;
        } else {
            $recaptchaResponse->success = false;
            $recaptchaResponse->errorCodes = isset($answers['error-codes'])?$answers['error-codes']:'Unknown error';
        }

        return $recaptchaResponse;
    }
    
    /**
     * Check if the input data has recaptcha verified
     *
     * @param string $address remote address
     * @param string $postData
     *
     * @return boolean true/false
     */
    public static function isVerified($postData, $address=null) {
        $reCaptchaVerified = false;
        $address = is_null($address)?$_SERVER["REMOTE_ADDR"]:$address;
        $reCaptcha = new HALOReCaptcha();
        // Was there a reCAPTCHA response?
        if (isset($postData["g-recaptcha-response"])) {
            $resp = $reCaptcha->verifyResponse($address, $postData["g-recaptcha-response"]);
            if ($resp != null && $resp->success) {
                $reCaptchaVerified = true;
            }
        }
        return $reCaptchaVerified;
    }
    
}

?>