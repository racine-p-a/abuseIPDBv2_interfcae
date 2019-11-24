<?php
/**
 * INTERFACE FOR NEW VERSION OF ABUSEIPDB API.
 * Project : abuseIPDBv2_interfcae
 * @author  Pierre-Alexandre RACINE <patcha.dev at{@} gmail dot[.] com>
 * @copyright Pierre-Alexandre RACINE <patcha.dev at{@} gmail dot[.] com>
 * @date 23/11/19 22:32
 *
 * @link https://github.com/racine-p-a/abuseIPDBv2_interfcae
 *
 * todo make a license
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

class AbuseIPDBInterface
{
    /**
     * Pick you user key here : https://www.abuseipdb.com/account/api and generate one if you do not already own one.
     * I strongly recommend to create and use one different for each application/website you use.
     *
     * @var string Your AbuseIPDB user key
     */
    private $apiKey = '128b87698b58fb8be1861c9959a6896b2cbfd97494a5baa58f5a7a0d4e46afbff8c824cd48cb3c99';

    /**
     * AbuseIPDBInterface constructor.
     */
    public function __construct()
    {

    }


    /**
     * Report here a suspicious IP address
     * @param string $IPToBan The IP you want to report to abuseIPDB
     * @param array $categories Optionnal array containing categories of reasons why you report this IP (more here : https://www.abuseipdb.com/categories)
     * @param string $comment Optionnal comment. STRIP ANY PERSONALLY IDENTIFIABLE INFORMATION (PPI). ABUSEIPDB IS NOT RESPONSIBLE FOR PPI YOU REVEAL... NOR AM I...
     * @return bool|string|void
     */
    public function reportIP($IPToBan='', array $categories=array(), $comment='') {
        if (filter_var($IPToBan, FILTER_VALIDATE_IP) && $this->apiKey!='') {
            // Works using POST : https://docs.abuseipdb.com/?php#report-endpoint
            $postdata = http_build_query(
                array(
                    'ip' => $IPToBan,
                    'categories' => implode(",", $categories),
                    'comment' => ''
                )
            );

            // CURL request.
            $headers =  array('Key: ' . $this->apiKey, 'Accept: application/json');
            $curlRequest = curl_init("https://api.abuseipdb.com/api/v2/report");
            curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, 1 ); // Set to 0 for testing to display response from AbuseIPDB
            curl_setopt($curlRequest, CURLOPT_POST,           1 );
            curl_setopt($curlRequest, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($curlRequest, CURLOPT_HTTPHEADER, $headers);

            if(curl_error($curlRequest)) {
                return;
            }
            $output=curl_exec($curlRequest);
            curl_close($curlRequest);
            return $output;
        }
    }


    /**
     * Grab the freshest blacklist from ABuseIPDB.
     * @param int $confidenceMinimum The minimal confidence that abuseIPDB has in the information (100=sure)
     * @return bool|string A list of bad IP you should blacklist (in a json object in our case).
     */
    public function getBlacklist($confidenceMinimum=90) {
        $blackList = '';

        // Works using GET : https://docs.abuseipdb.com/?php#blacklist-endpoint
        // URI construction
        $uri = 'https://api.abuseipdb.com/api/v2/blacklist?confidenceMinimum=' . intval($confidenceMinimum);

        // CURL request
        $headers =  array('Key: ' . $this->apiKey, 'Accept: application/json');

        $curlRequest = curl_init($uri);
        curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlRequest, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curlRequest, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlRequest, CURLOPT_HTTPHEADER, $headers);

        if(curl_error($curlRequest)) {
            return $blackList;
        }
        $blackList=curl_exec($curlRequest);
        curl_close($curlRequest);
        return $blackList;
    }

    /**
     * Check an API in the AbuseIPDB database and send back informations.
     * @param string $IPToCheck The IP we want to check (IPV4 or IPV6 accepted)
     * @param int $maxAge The age of the reports taken in account.
     * @param int $verbose Verbose answer ?
     * @return bool|string Informations about an IP from AbuseIPDB in a json object.
     */
    public function checkIP($IPToCheck='', $maxAge=0, $verbose=0) {
        $IPdata = '';

        // Works using GET : https://docs.abuseipdb.com/?php#check-endpoint
        if (filter_var($IPToCheck, FILTER_VALIDATE_IP) && ($this->apiKey != '')) {

            // URI construction
            $uri = 'https://api.abuseipdb.com/api/v2/check?ipAddress=' . urlencode($IPToCheck);
            if(intval($maxAge) > 0) {
                $uri .= '&maxAgeInDays=' . intval($maxAge);
            }
            if(boolval($verbose) == 1) {
                $uri .= '&verbose=' . boolval($verbose);
            }

            // CURL request
            $headers =  array('Key: ' . $this->apiKey, 'Accept: application/json');

            $curlRequest = curl_init($uri);
            curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlRequest, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curlRequest, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curlRequest, CURLOPT_HTTPHEADER, $headers);

            if(curl_error($curlRequest)) {
                return $IPdata;
            }
            $IPdata=curl_exec($curlRequest);
            curl_close($curlRequest);
        }
        return $IPdata;
    }



    public function checkBlock() {

    }

    public function bulkReport() {

    }


}