<?php namespace Wemagine\Enforta\EnfortaXMLService;

use Illuminate\Support\Facades\Auth;

class EnfortaXMLService {
    /**
     * This function wwill take data from the User and Create the new enrollment in enforta
     *
     */
    public function getCreateNewUserEnrollmentXML($data) {

        if(Auth::check()) {
            $planId = auth()->user()->plan_id;
            $package = config('constants.pricing')[$planId];
        } else {
            $package = config('constants.pricing')[$data['plan_id']];
        }

        $data = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <CreateNewUserEnrollment xmlns="https://api.identityprotection-services.com">'. "
              <APILoginName>".env('ENFORTRA_API_USERNAME')."</APILoginName>
              <APILoginPassword>".env('ENFORTRA_API_PASSWORD') ."</APILoginPassword>
              <PartnerID>". env('ENFORTRA_PARTNER_ID')."</PartnerID>
              <MemberID>".env('ENFORTRA_MEMBER_ID') ."</MemberID>
              <UserEmailAddress>{$data["email"]}</UserEmailAddress>
              <UserPassword>". env('ENFORTA_DEFAULT_PASSWORD') ."</UserPassword>
              <SSN>{$data["ssn"]}</SSN>
              <DOB>{$data["dob"]}</DOB>
              <FirstName>{$data["first_name"]}</FirstName>
              <MiddleName>{$data["middle_name"]}</MiddleName>
              <LastName>{$data["last_name"]}</LastName>
              <Suffix>{$data["suffix"]}</Suffix>
              <Address1>{$data["address1"]}</Address1>
              <Address2>{$data["address2"]}</Address2>
              <City>{$data["city"]}</City>
              <State>{$data["state"]}</State>
              <Zipcode>{$data["zip_code"]}</Zipcode>
              <CountryCode>US</CountryCode>
              <PhoneNumber>{$data["phone_number"]}</PhoneNumber>
              <PhoneType>{$data["phone_type"]}</PhoneType>
              <SMSNumber>{$data["sms_number"]}</SMSNumber>
              <SMSCarrier>{$data["sms_carrier"]}</SMSCarrier>
              <BundleID>" . $package["bundle_id"] ."</BundleID>
            </CreateNewUserEnrollment>
          </soap:Body>
        </soap:Envelope>";
        return $data;
    }

    /**
     * This function will return the version
     *
     */
    public function getEnfortaData() {
        return '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <APIVersion xmlns="https://api.identityprotection-services.com" />
          </soap:Body>
        </soap:Envelope>';
    }

    /**
     * This function will return XML for the report details
     *
     */
    public function getReportDetails() {
        $loggedInUserEmail = auth()->user()->email;
        return '<?xml version="1.0" encoding="utf-8"?>
        <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
          <soap12:Body>
            <GetCreditReportDetails xmlns="https://api.identityprotection-services.com">'."
                <APILoginName>".env('ENFORTRA_API_USERNAME')."</APILoginName>
                <APILoginPassword>".env('ENFORTRA_API_PASSWORD') ."</APILoginPassword>
                <UserEmailAddress>". $loggedInUserEmail ."</UserEmailAddress>
            </GetCreditReportDetails>
          </soap12:Body>
        </soap12:Envelope>";
    }

    /**
     * This function will return XML for the credit score
     *
     */
    public function getCreditScore() {
    }

    /**
     * This function will return XML for the enrollment details of a user
     */
    public function getEnrollmentDetails($email) {
        return '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <GetEnrollmentDetails xmlns="https://api.identityprotection-services.com">' . "
                 <APILoginName>".env('ENFORTRA_API_USERNAME')."</APILoginName>
                 <APILoginPassword>".env('ENFORTRA_API_PASSWORD') ."</APILoginPassword>
                 <UserEmailAddress>".$email."</UserEmailAddress>
                <OutputType>JSON</OutputType>
          ".'</GetEnrollmentDetails>
          </soap:Body>
        </soap:Envelope>';
    }

    /**
     *
     * This function will return the password
    */
    public function changeUserPassword($email, $password, $isSystemGenrated, $minLength) {
        $isSystemGenrated = $isSystemGenrated ? "true" : "false";
        return '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <ChangeUsersPassword xmlns="https://api.identityprotection-services.com">'. "
              <APILoginName>". env('ENFORTRA_API_USERNAME') ."</APILoginName>
              <APILoginPassword>". env('ENFORTRA_API_PASSWORD') ."</APILoginPassword>
              <PartnerID>". env("ENFORTRA_PARTNER_ID") ."</PartnerID>
              <MemberID>". env("ENFORTRA_MEMBER_ID") ."</MemberID>
              <RegisteredEmailAddress>{$email}</RegisteredEmailAddress>
              <HaveSystemGeneratePassword>{$isSystemGenrated}</HaveSystemGeneratePassword>
              <PasswordSize>{$minLength}</PasswordSize>
             <NewPassword>". env('ENFORTA_DEFAULT_PASSWORD') ."</NewPassword>
            </ChangeUsersPassword>". '
          </soap:Body>
        </soap:Envelope>';
    }





    /**
     *
     * This function will return Enforta XML
    */
    public function genrateEnfortaXML($functionName , $data) {

      $params = "";
      foreach( $data as $key => $value ){
        $params .= "<{$key}>{$value}</{$key}>";
      }

      return '<?xml version="1.0" encoding="utf-8"?>
      <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>'.
          "<{$functionName} xmlns=\"https://api.identityprotection-services.com\">
            {$params}
          </{$functionName}>"
        . '</soap:Body>
      </soap:Envelope>';
    }

    /**
     *
     * GetBkCcDlMdPpEmTMDetails
    */
    public function GetBkCcDlMdPpEmTM($email) {
      return $this->genrateEnfortaXML("GetBkCcDlMdPpEmTMDetails", [
        "APILoginName" => env('ENFORTRA_API_USERNAME'),
        "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
        "UserEmailAddress" => $email,
        "UserSeqNo" => 1,
        "RecordType" => "ALL",
        "OutputType" => "JSON",
      ]);
    }

    /**
     *
     * This function will return all alerts from enforta
    */
    public function getAllAlerts($email) {
      return $this->genrateEnfortaXML("GetAllAlerts", [
        "APILoginName" => env('ENFORTRA_API_USERNAME'),
        "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
        "UserEmailAddress" => $email,
        "OutputType" => "JSON",
      ]);
    }

    /**
     *
     * deleteBankAccount
    */
    public function DELETEBkCcDlMdPpEmTMDetails($email, $monitorId, $recordType) {
      return $this->genrateEnfortaXML("DELETEBkCcDlMdPpEmTMDetails", [
        "APILoginName" => env('ENFORTRA_API_USERNAME'),
        "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
        "UserEmailAddress" => $email,
        "UserSeqNo" => 1,
        "RecordType" => $recordType,
        "MonitorID" => $monitorId,
        "OutputType" => "JSON",
      ]);
    }

    public function ADDBkCcDlMdPpEmTmDetails($email, $valueToMonitor, $RecordType){
      return $this->genrateEnfortaXML("ADDBkCcDlMdPpEmTMDetails", [
        "APILoginName" => env('ENFORTRA_API_USERNAME'),
        "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
        "UserEmailAddress" => $email,
        "UserSeqNo" => 1,
        "RecordType" => $RecordType,
        "ValueToMonitor" => $valueToMonitor,
        "OutputType" => "JSON",
      ]);
    }

    public function getSexOffenderDetails( $miles ) {
        return $this->genrateEnfortaXML("GetSexOffenderDetails", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "RadiusInMiles" => $miles,
            "OutputType" => "XML",
        ]);
    }

    public function getCreditScoreDetails() {
        return $this->genrateEnfortaXML("GetCreditScores", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "OutputType" => "JSON",
        ]);
    }

    public function getSimulatorScore( $data ) {
        return $this->genrateEnfortaXML("GetSimulatedScore", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "OutputType" => "JSON",
            "NumNewApplications" => $data["NumNewApplications"],
            "TypeOfLoan" => $data["TypeOfLoan"],
            "LoanAmount" => $data["LoanAmount"],
            "CloseOldestCreditCardAccount" => $data["CloseOldestCreditCardAccount"],
            "IncreaseBalancesBy" => $data["IncreaseBalancesBy"],
            "AddLatePaymentsTo" => $data["AddLatePaymentsTo"],
            "IncreaseBalancesByHowMuch" => $data["IncreaseBalancesByHowMuch"],
            "LatePaymentsByHowManyDays" => $data["LatePaymentsByHowManyDays"],
            "RemoveLatePayments" => $data["RemoveLatePayments"],
            "MoveOneAccountToCollection" => $data["MoveOneAccountToCollection"],
            "AddTaxLien" => $data["AddTaxLien"],
            "AddForeclosure" => $data["AddForeclosure"],
            "AddChildSupport" => $data["AddChildSupport"],
            "AddWageGarnishment" => $data["AddWageGarnishment"],
            "DeclareBankruptcy" => $data["DeclareBankruptcy"]
        ]);
    }

    public function addSSN( $data ) {
        return $this->genrateEnfortaXML("AddSSN", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "ValueToMonitor" => $data["ValueToMonitor"]
        ]);
    }

    public function deleteSSN( $data ) {
        return $this->genrateEnfortaXML("DeleteSSN", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "MonitorID" => $data["MonitorID"]
        ]);
    }

    public function isUserVerified() {
        return $this->genrateEnfortaXML("GetKBAStatus", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "OutputType" => "JSON",
        ]);
    }

    public function getSSNs() {
        return $this->genrateEnfortaXML("GetSSNs", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "OutputType" => "JSON",
        ]);
    }

    public function getRedAlerts() {
        return $this->genrateEnfortaXML("GetRedAlerts", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "OutputType" => "JSON",
        ]);
    }

    public function cancelEnrollmentOfLoggedInUser() {
        return $this->genrateEnfortaXML("CancelEnrollment", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "OutputType" => "JSON",
        ]);
    }

    public function lockUnlockCreditReport() {
        return $this->genrateEnfortaXML("LockUnLockTuFile", [
            "APILoginName" => env('ENFORTRA_API_USERNAME'),
            "APILoginPassword" => env('ENFORTRA_API_PASSWORD'),
            "UserEmailAddress" => request()->user()->email,
            "OutputType" => "JSON",
        ]);
    }

}
