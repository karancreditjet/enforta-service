<?php namespace Wemagine\Enforta;

use Wemagine\Enforta\EnfortaXMLService\EnfortaXMLService;
use Illuminate\Support\Facades\Http;
use ABGEO\XmlToJson\StringConverter;
use Illuminate\Support\Facades\Log;

class EnfortaService implements EnfortaServiceInterface {

    /**
     * @var Wemagine\Enforta\EnfortaXMLService\EnfortaXMLService;
     */
    private $enfortaXMLService;

    /**
     * @var Wemagine\Enforta\EnfortaXMLService\EnfortaXMLService;
     */
    private $enfortaHttpClient;

    public function __construct(EnfortaXMLService $enfortaXMLService) {
        $this->enfortaXMLService = $enfortaXMLService;
        $this->enfortaHttpClient = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.identityprotection-services.com/Enfortrav1.06.asmx'
        ]);
    }

    /**
     * This function will create a new user in Enforta
     *
     */
    public function createNewUserEnrollment($data) {
        try{
            $newUserEnrollmentXML = $this->enfortaXMLService->getCreateNewUserEnrollmentXML($data);
            $client = $this->enfortaHttpClient->post("https://api.identityprotection-services.com/Enfortrav1.06.asmx", [
                'body' => $newUserEnrollmentXML,
                'headers' => [
                    'Content-Type' => 'text/xml',
                    'SOAPAction' => 'https://api.identityprotection-services.com/CreateNewUserEnrollment',
                ],
                'verify' => false,
            ]);
            $dataInXML = $client->getBody()->getContents();
            $jsonContent = $this->XMLtoJSON($dataInXML);
            return $jsonContent["Envelope"]["soap:Body"]["CreateNewUserEnrollmentResponse"];
        } catch(\Exception $e) {
            dd($e);
            return false;
        }
    }

    /**
     * This function will return Enforta API Version
     */
    public function getEnfortaVersion() {
        $versionXML = $this->enfortaXMLService->getEnfortaData();
        $client = $this->enfortaHttpClient->post("https://api.identityprotection-services.com/Enfortrav1.06.asmx", [
            'body' => $versionXML,
            'headers' => [
                'Content-Type' => 'text/xml',
                'SOAPAction' => 'https://api.identityprotection-services.com/APIVersion',
            ],
            'verify' => false,
        ]);

        $dataInXML = $client->getBody()->getContents();
        var_dump(simplexml_load_string($dataInXML)->asXML());
    }

    /**
     * This function will convert XML to JSON
     *
     */
    public function XMLtoJSON($xml) {
        $converter = new StringConverter();
        $jsonContent = $converter->convert(simplexml_load_string($xml)->asXML());
        return json_decode($jsonContent, true);
    }

    /**
     * This function will get report details
     *
     */
    public function getReportDetailsXML() {
        $requestXML = $this->enfortaXMLService->getReportDetails();
        $client = $this->enfortaHttpClient->post("https://api.identityprotection-services.com/Enfortrav1.06.asmx", [
            'body' => $requestXML,
            'headers' => [
                'Content-Type' => 'text/xml',
                'SOAPAction' => 'https://api.identityprotection-services.com/GetCreditReportDetails',
            ],
            'verify' => false,
        ]);
        $dataInXML = $client->getBody()->getContents();
        return $this->XMLtoJSON($dataInXML);
    }

    /**
     *
     * This function will return the user enrollment information.
     * **/
    public function getEnrollmentDetails($email) {
        $getUserEnrollmentDetailsXML = $this->enfortaXMLService->getEnrollmentDetails($email);
        $client = $this->enfortaHttpClient->post("https://api.identityprotection-services.com/Enfortrav1.06.asmx", [
            'body' => $getUserEnrollmentDetailsXML,
            'headers' => [
                'Content-Type' => 'text/xml',
                'SOAPAction' => 'https://api.identityprotection-services.com/GetEnrollmentDetails',
            ],
            'verify' => false,
        ]);
        $dataInJSON = $this->XMLtoJSON($client->getBody()->getContents());
        $details = $dataInJSON['Envelope']['soap:Body']['GetEnrollmentDetailsResponse']['GetEnrollmentDetailsResult'];
        return $details;
    }

    /**
     *
     * This function will return the changed user password
     **/
    public function changeLoggedInUserPassword($password, $isSystemGenrated) {
        $user = auth()->user();
        $getUserEnrollmentDetailsXML = $this->enfortaXMLService->changeUserPassword($user->email, $password, $isSystemGenrated, 6);
        $client = $this->enfortaHttpClient->post("https://api.identityprotection-services.com/Enfortrav1.06.asmx", [
            'body' => $getUserEnrollmentDetailsXML,
            'headers' => [
                'Content-Type' => 'text/xml',
                'SOAPAction' => 'https://api.identityprotection-services.com/ChangeUsersPassword',
            ],
            'verify' => false,
        ]);

        $dataInJSON = $this->XMLtoJSON($client->getBody()->getContents());
        $newPassword = $dataInJSON["Envelope"]["soap:Body"]["ChangeUsersPasswordResponse"]["NewPassword"];
        return ($newPassword);
    }

    /**
     *
     * This function will return All getBankAccounts
     **/
    public function GetBkCcDlMdPpEmTM() {
        $user = auth()->user();
        $data = [];
        $getBankAccountsXML = $this->enfortaXMLService->GetBkCcDlMdPpEmTM($user->email);
        $alertsData = $this->enfortaAPICall( "GetBkCcDlMdPpEmTMDetails", $getBankAccountsXML );
        if( isset($alertsData["GetBkCcDlMdPpEmTMDetailsResponse"]) && isset($alertsData["GetBkCcDlMdPpEmTMDetailsResponse"]["GetBkCcDlMdPpEmTMDetailsResult"]) ){
            $data = $alertsData["GetBkCcDlMdPpEmTMDetailsResponse"]["GetBkCcDlMdPpEmTMDetailsResult"];
        }
        return json_decode($data,true);
    }
    /**
     *
     * This function will deleteBankAccount
     **/
    public function DELETEBkCcDlMdPpEmTMDetails($monitorId, $recordType) {
        $user = auth()->user();
        $targetXML = $this->enfortaXMLService->DELETEBkCcDlMdPpEmTMDetails($user->email, $monitorId, $recordType);
        return $this->enfortaAPICall( "DELETEBkCcDlMdPpEmTMDetails", $targetXML );
    }
    /**
     *
     * This function will addBankAccount
     **/
    public function ADDBkCcDlMdPpEmTmDetails($valueToMonitor, $RecordType) {
        $user = auth()->user();
        $targetXML = $this->enfortaXMLService->ADDBkCcDlMdPpEmTmDetails($user->email, $valueToMonitor, $RecordType);
        return $this->enfortaAPICall( "ADDBkCcDlMdPpEmTMDetails", $targetXML );
    }
    /**
     *
     * This function will return All Alerts
     **/
    public function getAllAlerts() {
        $user = auth()->user();
        $data = [];
        $getAllAlertsXML = $this->enfortaXMLService->getAllAlerts($user->email);
        $alertsData = $this->enfortaAPICall( "GetAllAlerts", $getAllAlertsXML );
        if( isset($alertsData["GetAllAlertsResponse"]) && isset($alertsData["GetAllAlertsResponse"]["GetAllAlertsResult"]) ){
            $data = ($alertsData["GetAllAlertsResponse"]["GetAllAlertsResult"]) != null ? $alertsData["GetAllAlertsResponse"]["GetAllAlertsResult"] : "[]";
        } else {
            $data = [];
        }
        return json_decode($data,true);
    }

    /**
     *
     * This function will return the sex offenders in a perticular Miles
     */
    public function getSexOffenderDetails( $miles ) {
        $sexOffenderDetailsXML = $this->enfortaXMLService->getSexOffenderDetails( $miles );
        $data = $this->enfortaAPICall( "GetSexOffenderDetails", $sexOffenderDetailsXML);
        if( isset($data['GetSexOffenderDetailsResponse']) ) {
            $jsonData = $this->XMLtoJSON($data['GetSexOffenderDetailsResponse']['GetSexOffenderDetailsResult']);
            try {
                return ($jsonData["DocumentElement"]["SexOffender"]);
            } catch(\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /***
     *
     * This function will return the details about the credit score
     */
    public function getCreditScoreDetails() {
        $dataInXML = $this->enfortaXMLService->getCreditScoreDetails();
        $data = $this->enfortaAPICall("GetCreditScores", $dataInXML);
        try {
            $output = $data["GetCreditScoresResponse"]["GetCreditScoresResult"] != null ? $data["GetCreditScoresResponse"]["GetCreditScoresResult"] : "[]";
            return json_decode($output, true);
        } catch ( \Exception $e ) {
            return [];
        }
    }

    /***
     *
     * This function will return simulate score
     */
    public function getSimulateScore($data) {
        $dataInXml = $this->enfortaXMLService->getSimulatorScore($data);
        $data = $this->enfortaAPICall("GetSimulatedScore", $dataInXml);
        try {
            $response = $data["GetSimulatedScoreResponse"];
            if(isset($response["ErrMsg"]) && strpos($response["ErrMsg"], "ERR-CS002") !== false) {
                return [
                    'score' => $response['GetSimulatedScoreResult'],
                    'status' => false,
                    'message' => 'Customer is not fully enrolled. Please complete enrollment process. <a href="'. route('report.getReportDetails').'" > Click Here </a>',
                ];
            } else {
                return [
                    'status' => true,
                    'score' => $response['GetSimulatedScoreResult'],
                ];
            }
        } catch(\Exception $e) {
            return [
                'score' => [],
                'status' => false,
                'message' => 'Something went wrong. Try again later.s',
            ];
        }
    }

    /**
     *
     * This will add the ssn
     **/
    public function addSSN( $data ) {
        $dataInXml = $this->enfortaXMLService->addSSN($data);
        $data = $this->enfortaAPICall("AddSSN", $dataInXml);
        try {
            $response = $data['AddSSNResponse'];
            // Check if data is added\
            if($response["AddSSNResult"] == "False") {
                return [
                    'status' => false,
                    'message' => $response["ErrMsg"]
                ];
            } else {
                $ssnResult = $this->XMLtoJSON($response["AddSSNResult"]);
                return [
                    'status' => true,
                    'data' => $ssnResult["Response"]["SSN"],
                    'message' => 'SSN created successfully',
                ];
            }
        } catch(\Exception $e) {
            dd($e);
            return [
                'status' => false,
                'message' => "Something went wrong. Try again later",
            ];
        }
    }

     /**
     *
     * This will add the ssn
     **/
    public function deleteSSN( $data ) {
        $dataInXml = $this->enfortaXMLService->deleteSSN($data);
        $data = $this->enfortaAPICall("DeleteSSN", $dataInXml);
        try {
            $response = $data['DeleteSSNResponse'];
            // Check if data is added\
            if($response["DeleteSSNResult"] == "False") {
                return [
                    'status' => false,
                    'message' => $response["ErrMsg"]
                ];
            } else {
                return [
                    'status' => true,
                    'data' => $response,
                    'message' => 'SSN deleted successfully',
                ];
            }
        } catch(\Exception $e) {
            dd($e);
            return [
                'status' => false,
                'message' => "Something went wrong. Try again later",
            ];
        }
    }

    /***
     *
     * This function will return enforta API call
     */
    private function enfortaAPICall( $function ,$body ){
        try{
            $client = $this->enfortaHttpClient->post("https://api.identityprotection-services.com/Enfortrav1.06.asmx", [
                'body' => $body,
                'headers' => [
                    'Content-Type' => 'text/xml',
                    'SOAPAction' => "https://api.identityprotection-services.com/$function",
                ],
                'verify' => false,
            ]);

            $dataInJSON = $this->XMLtoJSON($client->getBody()->getContents());
            return $dataInJSON["Envelope"]["soap:Body"];
        }catch(\Exception $e){
            dd($e);
            // Log::error($e);
            return false;
        }
    }

    public function isUserVerified() {
        $dataInXML = $this->enfortaXMLService->isUserVerified();
        $verified = $this->enfortaAPICall("GetKBAStatus", $dataInXML);
        $verified = (
            $verified["GetKBAStatusResponse"]["GetKBAStatusResult"]
        );

        return $verified == "true" ? true : false;
    }

    public function getSSNs() {
        $dataInXML = $this->enfortaXMLService->getSSNs();
        $result = $this->enfortaAPICall("GetSSNs", $dataInXML);
        try {
            $response = json_decode($result["GetSSNsResponse"]["GetSSNsResult"], true)["Response"]["SSN"];
            // Checking if they are returning single array
            if(isset($response["MonitorID"])) {
                return [
                    $response
                ];
            }
            return is_array($response) ? $response : [];
        } catch(\Exception $e) {
            return [];
        }

    }

    public function getRedAlerts() {
        $dataInXml = $this->enfortaXMLService->getRedAlerts();
        $result = $this->enfortaAPICall("GetRedAlerts", $dataInXml);
        try {
            $data = json_decode($result["GetRedAlertsResponse"]["GetRedAlertsResult"], true);
            return is_array($data)  ? $data : [];
        } catch(\Exception $e) {
            return false;
        }

    }

    public function cancelEnrollmentOfLoggedInUser() {
        $dataInXml = $this->enfortaXMLService->cancelEnrollmentOfLoggedInUser();
        $result = $this->enfortaAPICall("CancelEnrollment", $dataInXml);
        try {
            return ($result["CancelEnrollmentResponse"]["CancelEnrollmentResult"]);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function lockUnlockCreditReport() {
        $dataInXml = $this->enfortaXMLService->lockUnlockCreditReport();
        $client = $this->enfortaHttpClient->post("https://api.identityprotection-services.com/Enfortrav1.06.asmx?LockUnLockTUFile", [
            'body' => $dataInXml,
            'headers' => [
                'Content-Type' => 'text/xml',
                'SOAPAction' => "https://api.identityprotection-services.com/LockUnLockTUFile",
            ],
            'verify' => false,
        ]);
        $dataInJSON = $this->XMLtoJSON($client->getBody()->getContents());
        $result = $dataInJSON["Envelope"]["soap:Body"];
        try {
            dd($result);
            return ($result["CancelEnrollmentResponse"]["CancelEnrollmentResult"]);
        } catch(\Exception $e) {
            return false;
        }
    }

    public function getAccountTradeLines() {
        $dataInXML = $this->enfortaXMLService->getAccountTradeLines();
        $result = $this->enfortaAPICall("GetAccountTradeLines", $dataInXML);
        try {
            return $result['GetAccountTradeLinesResponse']['GetAccountTradeLinesResult'];
        } catch(\Exception $e) {
            return false;
        }
    }
}
