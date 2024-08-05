<?php

require_once 'dompdf-master/autoload.inc.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

class WPtest_Save_PDF
{
    function __construct() {
        $this->generatePDF();
    }

    private function generatePDF() {
        $filename = "Will.pdf";
        $dompdf = new Dompdf();
        
        // Assuming __DIR__ is correctly defined and accessible
        $dompdf->set_base_path(__DIR__ . "/");
        
        $dompdf->set_option('defaultMediaType', 'all');
        $dompdf->set_option('isFontSubsettingEnabled', true);

        // Example of including external CSS files
        $bootstrap_css = file_get_contents(__DIR__ . "/assets/css/bootstrap.css");
       
        // code function to generate pdf data
                function validateWillsForm($willsFormData = []){
                    if (!is_array($willsFormData)) {
                        $willsFormData = [];
                    }
                    $defaultValues = [
                        'sec1' => null,
                        'sec2' => [
                            'prefix' => '',
                            'suffix' => '',
                            'firstName' => get_user_meta(get_current_user_id(),'first_name',true),
                            'middleName' => '',
                            'lastName' => get_user_meta(get_current_user_id(),'last_name',true),
                            'email' => wp_get_current_user()->user_email,
                            'gender' => '',
                            'address1' => '',
                            'address2' => '',
                            'country' => '',
                            'parish' => '',
                            'state' => '',
                            'occupation' => '',
                            'city' => ''
                        ],
                        'sec3' => [
                            'status' => '',
                            'children' => '',
                            'numChildren' => 1,
                            'childDetails' => [['name'=>'','relation'=>'','dob'=>'']],
                            'grandChildren' => '',
                            'numGrandChildren' => 1,
                            'grandChildDetails' => [['name'=>'','relation'=>'','dob'=>'']],
                            'fullName' => '',
                            'relation' => '',
                            'partnerGender' => '',
                            'grandChildrenDirect' => 1,
                            'deceased' => 1,   
                            'deceasedDetails' => [['name'=>'','gender'=>'','relation'=>'']],
                            'numDeceased' => 1,
                        ],
                        'sec4' => [
                            'otherBeneficiaries' => 1,
                            'numBeneficiaries' => 1,
                            'beneficiaryDetails' => [['gender' => '', 'name' => '', 'email' => '', 'relation' => '', 'address' => '','eqShare' => '']],
                        ],
                        'sec5' => [
                            'guardianDetails' => [['childName'=>'','name'=>'','reason'=>'','alterName'=>'']]
                        ],
                        'sec6' => [
                            'executorDetails' => [['name'=>'','relation'=>'','email'=>'','address'=>'']],
                            'numExecutor' => 1,
                            'alterOptions' => 1,
                            'numAlterExecutor' => 0,
                            'alterExecutorDetails' => [['name'=>'','relation'=>'','address'=>'']],
                        ],
                        'sec7' => [
                            'funeralOccur' => '',
                            'funeralClothed' => '',
                            'funeralPlaced' => '',
                            'funeralsongs1' => '',
                            'funeralsongs2' => '',
                        ],
                        'sec8' => [
                            'positions' => '',
                        ],
                        'sec9' => [
                            'charitableDonation' => 1,
                            'numBequests' => 1,
                            'bequestDetails' => [['type'=>1,'amount'=>'','percentage'=>'','asset'=>'','charityName'=>'']],
                            // 'petTrust' => 1,
                            // 'numPets' => 1,
                            // 'petDetails' => [['name'=>'','type'=>'','amount'=>'','caretaker'=>'','alterCaretaker'=>'']],
                            'possessionDist' => 1,
                            'shareExp' => 1,
                            'equalShare' => [['benefID'=>0,'share'=>'']],
                            'numSpecifics' => 0,
                            'specificsDetails' => [['type'=>1,'gift'=>'','description'=>'','giftBenefIndex'=>-1,'alterGiftBenefIndex'=>-1]],
                            'everythingBenefIndex' => -1,
                            'specificThingBenefIndex' => -1,
                            'multiBenefProvisions' => [['radio'=>1,'alterBenefIndex'=>-1,'shareDesc'=>'']],
                            'alterBenefProvisions' => ['radio'=>1,'everythingAlterBenefIndex'=>0,'restAllBenefIndex'=>0],
                            'numAlterSpecifics' => 1,            
                            'alterSpecificsDetails' => [['type'=>1,'gift'=>'','description'=>'','giftBenefIndex'=>-1,'alterGiftBenefIndex'=>-1]],
                            'secondLevelAlter' => ['radio'=>1,'alterBenefIndex'=>0,'everythingDesc'=>''],
                            'descSpecificBequest' => '',
                            'residualAlterDetail' => ['residualDesc'=>'','residualBenefIndex'=>-1],
                            'describeAlterDesc' => '',

                        ],
                        'sec10' => [
                                    'forgive'=>'1',
                                    'attachement'=>'',
                                    'attachement_url'=>'',
                                    'forgiveDetails'=>''],
                        'sec11' => ['attachement'=>''],        
                    ];
                    $validatedData = [];
                   
                    foreach ($defaultValues as $section => $data) {
                        
                        if(is_array($defaultValues[$section])){ 
                            
                            if(array_key_exists($section,$willsFormData)){
                                $validatedData[$section] = is_array($data) ? array_merge($defaultValues[$section], $willsFormData[$section]) : $defaultValues[$section];
                            }else{
                                $validatedData[$section] = $defaultValues[$section];
                            }
                        }
                    }   
                    return $validatedData;
                }


            // $otp = sanitize_text_field($_GET['otp-validation']);

            if (isset($otp) && !empty($otp)) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'will_request_form';
                $thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));

                $query = $wpdb->prepare(
                    "SELECT user_id FROM $table_name WHERE otp = %s AND status = %d AND created_at >= %s",
                    $otp, 1, $thirty_days_ago
                );
                $will_id = $wpdb->get_var($query);

                if ($will_id) {
                        
                    } else {
                        echo "Invalid OTP, status is not 1, or record is older than 30 days.";
                    }
            }else{
                $will_id = '';
            } 
           

            // if (is_user_logged_in() || !empty($will_id)) {

                if (is_user_logged_in()) {
                    $user_id = get_current_user_id();
                } else {
                    $user_id = $will_id;
                }
                
                $userData = get_userdata($user_id);
                $userMeta = get_user_meta($user_id);
                
                // Ensure $userMeta is an array
                if (!is_array($userMeta)) {
                    $userMeta = [];
                }
               
                // Safely access first_name and last_name
                $first_name = isset($userMeta['first_name'][0]) ? $userMeta['first_name'][0] : '';
                $last_name = isset($userMeta['last_name'][0]) ? $userMeta['last_name'][0] : '';
                $fullName = sprintf("%s %s", $first_name, $last_name);
                
                // Initialize $will_data and validate it
                $will_data = [];
                if (array_key_exists('wills_form_data', $userMeta) && is_array($userMeta['wills_form_data'])) {
                    $will_data = unserialize($userMeta['wills_form_data'][0]);
                    $will_data = validateWillsForm($will_data);        
                } else {
                    $will_data = validateWillsForm($will_data);
                }
                
            // }
            $genderChild = [
                '1'=>'son',
                '2'=>'daughter',
                '3'=>'child',
                '4'=>'stepson',
                '5'=>'stepdaughter',
                '6'=>'stepchild',    
            ];
            $genderGrandChild = [
                '1'=>'grandson',
                '2'=>'granddaughter',
                '3'=>'grandchild',
                '4'=>'grand stepson',
                '5'=>'grand stepdaughter',
                '6'=>'grand stepchild',    
            ];;
            $name = $will_data['sec2']['prefix'] . ' ' . $will_data['sec2']['firstName'].' '.$will_data['sec2']['middleName'].' '.$will_data['sec2']['lastName'].' '.$will_data['sec2']['suffix'];
            $address = $will_data['sec2']['address1'].', '.$will_data['sec2']['address2'].', '.$will_data['sec2']['country'];
            $parish = $will_data['sec2']['parish'];
            $show_sign = $will_data['sec10']['attachement'];
            $sign = $will_data['sec10']['attachement_url'];
            $occupation = $will_data['sec2']['occupation'];

        
        // code function to generate pdf data

        $bootstrap = '<style>
            @page {
                margin: 0in;
            }
            ' . $bootstrap_css . '
            
        </style>';
        
        $html = '<!DOCTYPE html>
        <html> 
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <title>View Will</title>
                ' . $bootstrap . '
                    <style>
                        body {
                            font-family: Arial,Helvetica,sans-serif;
                            margin: 0;
                            padding: 0;
                        }

                        .pdf-content {
                            width: 80%;
                            margin: 50px auto;
                        }

                        h1 {
                            text-align: center;
                        }

                        /* #custom-data {
                            margin-bottom: 20px;
                            padding: 10px;            
                        } */

                        button {
                            display: block;
                            margin: 0 auto;
                            padding: 10px 20px;
                            font-size: 16px;
                            background-color: #007bff;
                            color: #fff;
                            border: none;
                            cursor: pointer;
                        }
                        li{
                            margin-bottom:10px;            
                        }
                        .main-list{
                            list-style-type: none;
                            counter-reset: my-counter;
                            padding-left: 0;
                            max-width: 980px;
                        }
                        .main-list > li{
                            counter-increment: my-counter;
                            margin-bottom: 10px;
                            position: relative;
                            padding-left: 60px;
                        }
                        .main-list > li::before {
                            content: counter(my-counter) ". ";
                            position: absolute;
                            left: 20px;
                        }        
                        button:hover {
                            background-color: #0056b3;
                        }

                    </style>
            </head>
            <body>
                ';  
               
                if (is_user_logged_in() || !empty($will_id)) :
                    $html .= '<div class="pdf-content">
                        <h1>Last Will And Testament</h1>
                        <hr>
                        <div>';
                        
                        $html .=  '<p><strong>This is the last will and Testament of me ' .$name ;
                        
                         $html .= '</strong>, a Occupation whose address is '. $address .', in the parish of '. $parish .'.</p>
                        </div>
                        <div>
                            <ol class="main-list">                
                                <li>
                                    <div>
                                        <p><strong>I hereby revoke</strong> all wills and testamentary dispositions heretofore by me made and declare this to be my last will and testament.</p>
                                    </div>
                                </li>
                                <li>
                                    <p><strong>Appointment of Executors</strong></p>';

                                    if (!empty($will_data['sec6']['executorDetails'])):
                                        $html .= '<p>';
                                        foreach ($will_data['sec6']['executorDetails'] as $key => $value):
                                            if ($key == 0):
                                                $html .= '<strong>I Hereby Appoint</strong> ';
                                            endif;
                                            $html .= $value['name'] . ' My ' . $value['relation'] . ' , Of ' . $value['address'] ;
                                            if ($key > 0):
                                                $html .= ' AND ';
                                            endif;
                                        endforeach;
                                        $html .= ', To Be The Executor And Trustee Of This My Will (Hereinafter Referred To As "My Trustee").</p>';
                                    endif; 
                                    $html .= '</li>
                                            <li>
                                                <p><strong>I Direct</strong> That As Soon As Possible After My Decease My Trustees Shall Pay All My Just Debts, Funeral, Tombing And Testamentary Expenses.</p>
                                            </li>
                                            <li>
                                                <p><strong>Funeral And Burial Arrangements</strong></p>
                                                <p>I Hereby Direct That My Body Be Prepared For Burial In An Appropriate Manner And That My Funeral Expenses And Any Debts Be Paid Out Of My Estate, Along With The Following:</p>
                                                <ol type="a">';
                                
                                if (!empty($will_data['sec7']['funeralOccur'])):
                                    $html .= '<li>
                                                    <p>That I Be Like To Occur At Your Funeral ' . $will_data['sec7']['funeralOccur'] . '</p>
                                                </li>';
                                endif;
                                if (!empty($will_data['sec7']['funeralClothed'])):
                                    $html .= '<li>
                                                    <p>That Be Clothed In ' . $will_data['sec7']['funeralClothed'] . '</p>
                                                </li>';
                                endif;
                                if (!empty($will_data['sec7']['funeralPlaced'])):
                                    $html .= '<li>
                                                    <p>That My Remains Be Placed ' . $will_data['sec7']['funeralPlaced'] . '</p>
                                                </li>';
                                endif;
                                if (!empty($will_data['sec7']['funeralsongs1']) || !empty($will_data['sec7']['funeralsongs2'])):
                                    $html .= '<li>
                                                    <p>That the following song is played at my wedding- 
                                                        <ul>';
                                    if ($will_data['sec7']['funeralsongs1']):
                                        $html .= '<li>' . $will_data['sec7']['funeralsongs1'] . '</li>';
                                    endif;
                                    if ($will_data['sec7']['funeralsongs2']):
                                        $html .= '<li>' . $will_data['sec7']['funeralsongs2'] . '</li>';
                                    endif;
                                    $html .= '</ul>
                                                    </p>
                                                </li>';
                                endif;
                                    $html .= '</ol>
                                            </li>
                                            <li>
                                                <p><strong>I Give Devise And Bequeath:</strong></p>
                                                <ol type="a">';
                                                
                                                if (!empty($will_data['sec8']['positions'])): 
                                                    $sec8_Property = $sec8_Shares = $sec8_Insurance = $sec8_Bank = $sec8_Vehicle = $sec8_Unpaid_Salary = $sec8_NHT = $sec8_Jewellery = $sec8_Furniture = $sec8_Paintings = $sec8_Firearm = $sec8_Residual = [];
                                                    $Property = $Shares = $Insurance = $Bank = $Vehicle = $Unpaid_Salary = $NHT = $Jewellery = $Furniture = $Paintings = $Firearm = $Residual = [];

                                                    
                                                    foreach ($will_data['sec8']['positions'] as $key => $value) {
                                                        switch ($value['position']) {
                                                            case 'Property':
                                                                $sec8_Property[] = $key;
                                                                break;
                                                            case 'Shares and Stocks':
                                                                $sec8_Shares[] = $key;
                                                                break;
                                                            case 'Insurance':
                                                                $sec8_Insurance[] = $key;
                                                                break;
                                                            case 'Bank Accounts':
                                                                $sec8_Bank[] = $key;
                                                                break;
                                                            case 'Motor Vehicle':
                                                                $sec8_Vehicle[] = $key;
                                                                break;
                                                            case 'Unpaid Salary and Emoluments':
                                                                $sec8_Unpaid_Salary[] = $key;
                                                                break;
                                                            case 'National Housing Trust (NHT) Contributions':
                                                                $sec8_NHT[] = $key;
                                                                break;
                                                            case 'Jewellery':
                                                                $sec8_Jewellery[] = $key;
                                                                break;
                                                            case 'Furniture':
                                                                $sec8_Furniture[] = $key;
                                                                break;
                                                            case 'Paintings':
                                                                $sec8_Paintings[] = $key;
                                                                break;
                                                            case 'Firearm':
                                                                $sec8_Firearm[] = $key;
                                                                break;
                                                            case 'Residual Estate':
                                                                $sec8_Residual[] = $key;
                                                                break;
                                                        }
                                                    } 
                                                    foreach ($will_data['sec9']['specificsDetails'] as $key_sec9 => $value_sec9) {
                                                        if (isset($value_sec9['positions'])) {
                                                            switch ($value_sec9['positions']) {
                                                                case 'Property':
                                                                    $Property[] = $key_sec9;
                                                                    break;
                                                                case 'Shares and Stocks':
                                                                    $Shares[] = $key_sec9;
                                                                    break;
                                                                case 'Insurance':
                                                                    $Insurance[] = $key_sec9;
                                                                    break;
                                                                case 'Bank Accounts':
                                                                    $Bank[] = $key_sec9;
                                                                    break;
                                                                case 'Motor Vehicle':
                                                                    $Vehicle[] = $key_sec9;
                                                                    break;
                                                                case 'Unpaid Salary and Emoluments':
                                                                    $Unpaid_Salary[] = $key_sec9;
                                                                    break;
                                                                case 'National Housing Trust (NHT) Contributions':
                                                                    $NHT[] = $key_sec9;
                                                                    break;
                                                                case 'Jewellery':
                                                                    $Jewellery[] = $key_sec9;
                                                                    break;
                                                                case 'Furniture':
                                                                    $Furniture[] = $key_sec9;
                                                                    break;
                                                                case 'Paintings':
                                                                    $Paintings[] = $key_sec9;
                                                                    break;
                                                                case 'Firearm':
                                                                    $Firearm[] = $key_sec9;
                                                                    break;
                                                                case 'Residual Estate':
                                                                    $Residual[] = $key_sec9;
                                                                    break; 
                                                            }
                                                        } 
                                                    } 
                                                    
                                                    if (!empty($Property)) {
                                                        $html .= '<li><strong>Property</strong>';
                                                        // $i = 1;
                                                        
                                                        // Iterate through sec8_Property
                                                        foreach ($sec8_Property as $key => $value_sec8) { 
                                                           
                                                            // Check if this property has related specifics in section 9
                                                            if (!empty($Property)) {
                                                                $html .= '<ul>';
                                                                foreach ($Property as $propKey => $propValue) {  
                                                                    
                                                                    if ($will_data['sec9']['specificsDetails'][$propValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        
                                                                       
                                                                        $html .= '<li><p> Property - Situate At ' . $will_data['sec8']['positions'][$value_sec8]['address'] . ', In The Parish Of ' . $will_data['sec8']['positions'][$value_sec8]['parish'] . ' Registered At ' . $will_data['sec8']['positions'][$value_sec8]['registeredAt'] . ' Of The Register Book Of Titles to ' . $will_data['sec9']['specificsDetails'][$propValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            } 
                                                        }
                                                    
                                                        $html .= '</li>';
                                                    }
                                                    if (!empty($sec8_NHT)) {
                                                        $html .= '<li><strong>National Housing Trust (NHT) Contributions</strong>'; 
                                                    
                                                        // Iterate through sec8_NHT
                                                        foreach ($sec8_NHT as $key => $value_sec8) { 
                                                    
                                                            // Check if this NHT has related specifics in section 9
                                                            if (!empty($NHT)) {
                                                                $html .= '<ul>';
                                                                foreach ($NHT as $nhtKey => $nhtValue) {
                                                                    if($will_data['sec9']['specificsDetails'][$nhtValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']){
                                                                        $html .= '<li><p>Refund Of National Housing Trust Contributions ' . $will_data['sec8']['positions'][$value_sec8]['refundDetails'] . ' To ' . $will_data['sec8']['positions'][$value_sec8]['address'] . '. In The Parish Of ' . $will_data['sec8']['positions'][$value_sec8]['parish'] . ' to ' . $will_data['sec9']['specificsDetails'][$nhtValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            } 
                                                        }
                                                        
                                                        $html .= '</li>';
                                                    }
                                                   
                                                    if (!empty($sec8_Shares)) {
                                                        $html .= '<li><strong>Shares And Stocks</strong>'; 
                                                    
                                                        // Iterate through sec8_Shares
                                                        foreach ($sec8_Shares as $key => $value_sec8) { 
                                                    
                                                            // Check if this Shares has related specifics in section 9
                                                            if (!empty($Shares)) {
                                                                $html .= '<ul>';
                                                                foreach ($Shares as $sharesKey => $sharesValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$sharesValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p>Shares In ' . $will_data['sec8']['positions'][$value_sec8]['sharesIn'] . ' Held In ' . $will_data['sec8']['positions'][$value_sec8]['country'] . ' At ' . $will_data['sec8']['positions'][$value_sec8]['investmentCompany'] . ' In Account Numbered ' . $will_data['sec8']['positions'][$value_sec8]['accountNumber'] . ' To ' . $will_data['sec8']['positions'][$value_sec8]['address'] . '. In The Parish Of ' . $will_data['sec8']['positions'][$value_sec8]['parish'] .  ' to ' . $will_data['sec9']['specificsDetails'][$sharesValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            }
                                                    
                                                            $html .= '</li>';
                                                        }
                                                    }
                                                   
                                                    if (!empty($sec8_Insurance)) {
                                                        $html .= '<li><strong>Insurance</strong>'; 
                                                    
                                                        // Iterate through sec8_Insurance
                                                        foreach ($sec8_Insurance as $key => $value_sec8) {
                                                             
                                                    
                                                            // Check if this Insurance has related specifics in section 9
                                                            if (!empty($Insurance)) {
                                                                $html .= '<ul>';
                                                                foreach ($Insurance as $insuranceKey => $insuranceValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$insuranceValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p>Proceeds Of Insurance Policy Numbered ' . $will_data['sec8']['positions'][$value_sec8]['policyNumber'] . ', Held At ' . $will_data['sec8']['positions'][$value_sec8]['investmentCompany'] . ' Located At ' . $will_data['sec8']['positions'][$value_sec8]['country'] . ', ' . $will_data['sec8']['positions'][$value_sec8]['address'] . '. In The Parish Of ' . $will_data['sec8']['positions'][$value_sec8]['parish'] . ' to ' . $will_data['sec9']['specificsDetails'][$insuranceValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            }
                                                    
                                                            $html .= '</li>'; 
                                                        }
                                                    
                                                    }
                                                    if (!empty($sec8_Bank)) {
                                                        $html .= '<li><strong>Bank Accounts</strong>'; 
                                                    
                                                        // Iterate through sec8_Bank
                                                        foreach ($sec8_Bank as $key => $value_sec8) { 
                                                    
                                                            // Check if this Bank Account has related specifics in section 9
                                                            if (!empty($Bank)) {
                                                                $html .= '<ul>';
                                                                foreach ($Bank as $bankKey => $bankValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$bankValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p> Proceeds Of Bank Account Numbered ' . $will_data['sec8']['positions'][$value_sec8]['accountNumber'] . ', Held At ' . $will_data['sec8']['positions'][$value_sec8]['financialInstitution'] . ' Located At ' . $will_data['sec8']['positions'][$value_sec8]['address'] . ' ' . $will_data['sec8']['positions'][$value_sec8]['country'] . '. In The Parish Of ' . $will_data['sec8']['positions'][$value_sec8]['parish'] . ' to ' . $will_data['sec9']['specificsDetails'][$bankValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            } 
                                                        }
                                                    
                                                        $html .= '</li>';
                                                    }

                                                    if (!empty($sec8_Vehicle)) {
                                                        $html .= '<li><strong>Motor Vehicle</strong>';
                                                       

                                                        // Iterate through sec8_Vehicle
                                                        foreach ($sec8_Vehicle as $key => $value_sec8) {
                                                             

                                                            // Check if this Motor Vehicle has related specifics in section 9
                                                            if (!empty($Vehicle)) {
                                                                $html .= '<ul>';
                                                                foreach ($Vehicle as $vehKey => $vehValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$vehValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p>  ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['color'] . ' ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['make'] . ' ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['model'] . ' Motor Vehicle Bearing Licence Plate Number ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['licenceNumber'] . ' And Engine And Chassis Numbers ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['engineChassisNumbers'] . ' To ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['address'] . '. In The Parish Of ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['parish'] . ' to ' . $will_data['sec9']['specificsDetails'][$vehValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            }

                                                            $html .= '</li>';
                                                            
                                                        }
 
                                                    }
                                                                                                        
                                                    if (!empty($sec8_Unpaid_Salary)) {
                                                        $html .= '<li><strong>Unpaid Salary And/Or Emoluments</strong><ol type="i">';
                                                        $i = 1;
                                                    
                                                        // Iterate through sec8_Unpaid_Salary
                                                        foreach ($sec8_Unpaid_Salary as $key => $value_sec8) {
                                                            
                                                    
                                                            // Check if this Unpaid Salary has related specifics in section 9
                                                            if (!empty($Unpaid_Salary)) {
                                                                $html .= '<ul>';
                                                                foreach ($Unpaid_Salary as $salaryKey => $salaryValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$salaryValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p>  Unpaid Salary And/Or Emoluments With My Employer, ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['employerName'] . ' Located At ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['address'] . '. In The Parish Of ' . 
                                                                    $will_data['sec8']['positions'][$value_sec8]['parish'] . ' to ' . $will_data['sec9']['specificsDetails'][$salaryValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            }
                                                    
                                                            $html .= '</li>';
                                                            
                                                        }
                                                     
                                                    }
                                                    
                                                    
                                                    
                                                    if (!empty($sec8_Jewellery)) {
                                                        $html .= '<li><strong>Jewellery</strong><ol type="i">';
                                                        $i = 1;
                                                    
                                                        // Iterate through sec8_Jewellery
                                                        foreach ($sec8_Jewellery as $key => $value_sec8) { 
                                                    
                                                            // Check if this Jewellery has related specifics in section 9
                                                            if (!empty($Jewellery)) {
                                                                $html .= '<ul>';
                                                                foreach ($Jewellery as $jewelleryKey => $jewelleryValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$jewelleryValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p> . ' . $will_data['sec8']['positions'][$value_sec8]['description'] . ' Described As My Jewellery to ' . $will_data['sec9']['specificsDetails'][$jewelleryValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            }
                                                    
                                                            $html .= '</li>';
                                                            
                                                        }
                                                     
                                                    }
                                                    
                                                    if (!empty($sec8_Furniture)) {
                                                        $html .= '<li><strong>Furniture</strong><ol type="i">';
                                                        $i = 1;
                                                    
                                                        // Iterate through sec8_Furniture
                                                        foreach ($sec8_Furniture as $key => $value_sec8) {
                                                           
                                                    
                                                            // Check if this Furniture has related specifics in section 9
                                                            if (!empty($Furniture)) {
                                                                $html .= '<ul>';
                                                                foreach ($Furniture as $furnitureKey => $furnitureValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$furnitureValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p> . ' . $will_data['sec8']['positions'][$value_sec8]['description'] . ' Described As My Furniture. to ' . $will_data['sec9']['specificsDetails'][$furnitureValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            }
                                                    
                                                            $html .= '</li>';
                                                            
                                                        }
                                                     
                                                    } 
                                                     
                                                    if (!empty($sec8_Paintings)) {
                                                        $html .= '<li><strong>Paintings</strong><ol type="i">';
                                                        $i = 1;
                                                    
                                                        // Iterate through sec8_Paintings
                                                        foreach ($sec8_Paintings as $key => $value_sec8) { 
                                                    
                                                            // Check if this Paintings has related specifics in section 9
                                                            if (!empty($Paintings)) {
                                                                $html .= '<ul>';
                                                                foreach ($Paintings as $paintingKey => $paintingValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$paintingValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p>'  . $i . '. Paintings To ' . $will_data['sec8']['positions'][$value_sec8]['description'] . ' to ' . $will_data['sec9']['specificsDetails'][$paintingValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            }
                                                    
                                                            $html .= '</li>';
                                                            
                                                        }
                                                     
                                                    }

                                                    
                                                    if (!empty($sec8_Firearm)) {
                                                        $html .= '<li><strong>Firearm</strong><ol type="i">';
                                                        $i = 1;
                                                    
                                                        // Iterate through sec8_Firearm
                                                        foreach ($sec8_Firearm as $key => $value_sec8) { 
                                                    
                                                            // Check if this Firearm has related specifics in section 9
                                                            if (!empty($Firearm)) {
                                                                $html .= '<ul>';
                                                                foreach ($Firearm as $firearmKey => $firearmValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$firearmValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p>'. $i . '. Firearm Bearing Serial And Firearm Licence Numbers ' . $will_data['sec8']['positions'][$value_sec8]['description'] . ' to ' . $will_data['sec9']['specificsDetails'][$firearmValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            }
                                                    
                                                            $html .= '</li>';
                                                            
                                                        }
                                                     
                                                    }

                                                    if (!empty($sec8_Residual)) {
                                                        $html .= '<li><strong>Residual</strong><ol type="i">';
                                                        $i = 1;
                                                    
                                                        // Iterate through sec8_Residual
                                                        foreach ($sec8_Residual as $key => $value_sec8) { 
                                                    
                                                            // Check if this Residual has related specifics in section 9
                                                            if (!empty($Residual)) {
                                                                $html .= '<ul>';
                                                                foreach ($Residual as $residualKey => $residualValue) {
                                                                    if ($will_data['sec9']['specificsDetails'][$residualValue]['positions'] == $will_data['sec8']['positions'][$value_sec8]['position']) {
                                                                        $html .= '<li><p> . I Give, Devise And Bequeath All The Rest, Residue And Remainder Of My Estate, Including Any Proceeds From The Sale Of Assets To ' . $will_data['sec8']['positions'][$value_sec8]['description'] . ' In Equal Shares.' . $will_data['sec9']['specificsDetails'][$residualValue]['giftBenefIndex'] . '.</p></li>';
                                                                    }
                                                                }
                                                                $html .= '</ul>';
                                                            }
                                                    
                                                            $html .= '</li>';
                                                            
                                                        }
                                                     
                                                    }
                                                endif;
                                    $html .= '</ol>
                                </li>
                            </ol>
                            <div style="margin-top:40px;">
                                <p><strong>In Witness Whereof</strong> I Have Hereunto Set My Hand And Seal This ...............day of .........20</p>
                                ';
                                $current_user = wp_get_current_user();
                                $username = $current_user->user_login;
                                
                                // Construct the file path
                                $folder_path = '/wp-content/uploads/will-signatures/';
                                $image_name = $username.'.png';
                                $image_path = get_site_url() . $folder_path . $image_name;
                                $path = get_site_url() . $folder_path . $image_name;
                                $signature = $image_path;
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $sign_data = file_get_contents($signature);
                                $sign = 'data:image/' . $type . ';base64,' . base64_encode($sign_data);

                                if (($image_path)) {
                                    $html .='<img width="100px" src="'.$sign.'" alt="">';
                                } else {
                                    $html .= '<div class="mb-2">
                                        <h5>Signature Pad</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <canvas id="signature-pad"></canvas>
                                            </div>
                    
                                            <div class="col-md-4">
                                                <button id="clear">Clear</button>
                                                <br>
                                                <button id="save">Save</button>
                                            </div>
                                        </div>
                    
                                        <form id="signature-form" action="" method="post">
                                            <input type="hidden" name="signature" id="signature-input">
                                        </form>
                                    </div>';
                                }               
                             
                                $html .= '<p>(Testator to sign here) .........................</p>';
                                
                                $html .= '<p><strong>SIGNED</strong> by the Testator the said ' . $name . ', a ' . $occupation . ' of ' . $address . ', in the parish of ' . ($parish ? $parish : '') . ', as my Last Will and Testament. I declare that I have signed and executed this Last Will and Testament willingly and in the presence of the following witnesses, who are present at the same time and who have signed as witnesses in my presence:</p><br><br><br><br>';

                               
                                $allEmpty = true;

                                foreach ($will_data['sec6']['executorDetails'] as $executor) {
                                    if (!empty(array_filter($executor))) {
                                        $allEmpty = false;
                                        break;
                                    }
                                }
                                if (!$allEmpty){
                                    foreach ($will_data['sec6']['executorDetails'] as $key => $value){
                                        $html .= '<p><strong>WITNESSES</strong></p>
                                        <div>
                                            <div style="display:inline-block;width:10%;padding:10px;text-align: center;">
                                            </div>
                                            <div style="display:inline-block;width:30%;padding:10px;">
                                                <div>
                                                    <p>Name: '.$value['name'].'</p>
                                                    <p>Address:'.$value['address'].'</p>
                                                    <p>Occupation: '.$value['occupation'].'</p>
                                                    
                                                </div>
                                            </div> 
                                        </div>';
                                    }  
                                        

                                    
                                }
                            $html .=  '</div>
                        </div>';
                        
                        if($show_sign=='true'){  
                            $html .=  '<div style="text-align:right;">
                                <img style="max-width: 180px;" src="'.$sign.'" alt="">
                            </div>';
                        } 
                        
                        $html .=  '</div>';
                        ?>
                       
                       
                <?php else:  
                    $html .=  '<div class="otp-validation">
                        <div class="otp-validation-box">
                            <h1>Enter Otp to show the will Details</h1>
                            <form id="otp-validation" method="get" action="#">
                                <input type="text" name="otp-validation" placeholder="Please Enter Yor otp" required>
                                <button type="submit">Submit</button>
                            </form>
                        </div>
                    </div>
                    <style type="text/css">
                        .otp-validation {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            position: absolute;
                            top: 0;
                            left: 0;
                            right: 0;
                            bottom: 0;
                        }
                        .otp-validation-box {
                            width: 50%;
                            padding: 40px 20px;
                            box-shadow: 0 0 3px #000;
                            border-radius: 5px;
                        }
                        #otp-validation{
                            display: flex;
                        }
                        .otp-validation-box button {
                            margin: 0;
                        }
                        .otp-validation-box input[type="text"] {
                            width: 85%;
                            padding: 10px;
                        }
                    </style>';
                 endif; 

                $html .= '</body>
                </html>' ;
               

        $dompdf->loadHtml($html);
       
        
       
        
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render(); 
        $dompdf->stream($filename, array("Attachment" => 0));


        
        
    }
}

// Instantiate the class to trigger PDF generation
new WPtest_Save_PDF();

?>
