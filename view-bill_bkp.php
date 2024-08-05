<?php


function validateWillsForm($willsFormData = []){
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
            'country' => '',
            'state' => '',
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
            'beneficiaryDetails' => [['gender' => '', 'name' => '', 'relation' => '', 'address' => '','eqShare' => '']],
        ],
        'sec5' => [
            'guardianDetails' => [['childName'=>'','name'=>'','reason'=>'','alterName'=>'']]
        ],
        'sec6' => [
            'executorDetails' => [['name'=>'','relation'=>'','address'=>'']],
            'numExecutor' => 1,
            'alterOptions' => 1,
            'numAlterExecutor' => 0,
            'alterExecutorDetails' => [['name'=>'','relation'=>'','address'=>'']],
        ],
        'sec7' => [
            'charitableDonation' => 1,
            'numBequests' => 1,
            'bequestDetails' => [['type'=>1,'amount'=>'','percentage'=>'','asset'=>'','charityName'=>'']],
            'petTrust' => 1,
            'numPets' => 1,
            'petDetails' => [['name'=>'','type'=>'','amount'=>'','caretaker'=>'','alterCaretaker'=>'']],
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
        'sec8' => [
            'youngBenefs' => [['trust'=>1,'expiryAge'=>-1,'shareType'=>1,'fraction'=>'','ageGranted'=>-1,'fractionRemainder'=>'','atThisAge'=>-1]],
        ],
        'sec9' => ['forgive'=>'1','forgiveDetails'=>''],
        'sec10' => ['attachement'=>''],        
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
if (is_user_logged_in()) {
    $userData = get_userdata(get_current_user_id());
    $userMeta = get_user_meta(get_current_user_id());
    $fullName = sprintf("%s %s", $userMeta['first_name'][0], $userMeta['last_name'][0]);
    $will_data = [];
    if(array_key_exists('wills_form_data',$userMeta) && is_array($userMeta['wills_form_data'])){
        $will_data = unserialize($userMeta['wills_form_data'][0]);
        $will_data = validateWillsForm($will_data);        
    }else{
        $will_data = validateWillsForm($will_data);
    }
    // echo '<pre>';
    // print_r($will_data);
    // echo '</pre>';
    // exit;
}
$name = $will_data['sec2']['prefix'] . ' ' . $will_data['sec2']['firstName'].' '.$will_data['sec2']['middleName'].' '.$will_data['sec2']['lastName'].' '.$will_data['sec2']['suffix'];
$address = $will_data['sec2']['state'].', '.$will_data['sec2']['country'];
$parish = $will_data['sec2']['city'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Will</title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
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

        #custom-data {
            margin-bottom: 20px;
            padding: 10px;
            /* border: 1px solid #ccc; */
        }

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
    <div class="pdf-content">
        <h1>Last Will And Testament</h1>
        <hr>
        <div>
            <p><strong>THIS IS THE LAST WILL AND TESTAMENT</strong> of me <strong><?= $name ?></strong>, a [OCCUPATION] whose address is <?= $address ?> in the parish of <?= $parish ?>.</p>
        </div>
        <div>
            <ol class="main-list">
                <li><strong>I HEREBY REVOKE</strong> all Wills and Testamentary dispositions heretofore by me made <strong>AND DECLARE</strong> this to be my Last Will and Testament.</li>
                <li>
                    <div>
                        <p><strong>APPOINTMENT OF EXECUTORS</strong></p>
                        <ul style="list-style:none;text-indent:0;margin-bottom: 25px;">                        
                        <?php for($i=0;$i<count($will_data['sec6']['executorDetails']);$i++){ ?>
                            <li>I HEREBY APPOINT <?= $will_data['sec6']['executorDetails'][$i]['name']?>, my <?= $will_data['sec6']['executorDetails'][$i]['relation']?> [please insert occupation], of <strong><?= $will_data['sec6']['executorDetails'][$i]['address']?>, in the parish of [INSERT PARISH]</strong> AND <?= $will_data['sec6']['executorDetails'][$i]['name']?> my <?= $will_data['sec6']['executorDetails'][$i]['relation']?> [please insert occupation], of <strong>[INSERT PARISH], in the parish of [INSERT PARISH]</strong>, to be the Executor and Trustee of this my Will (hereinafter referred to as "my Trustee").</li>
                        <?php } ?>
                        </ul>
                    </div>
                </li>
                <li><strong>I DIRECT</strong> that as soon as possible after my decease my Trustees shall pay all my just debts, funeral, tombing and testamentary expenses.</li>
                <li>
                    <div>
                        <p><strong>FUNERAL AND BURIAL ARRANGEMENTS</strong></p>
                        <ul style="list-style:none;text-indent:0;margin-bottom: 25px;">
                            <p><strong>I HEREBY DIRECT that my body be prepared for burial in an appropriate manner and that my funeral expenses and any debts be paid out of my estate, along with the</strong> following:</p>
                            <ol type="a" style="text-indent: 10px;">
                                <li>That I be [specify any specific details that you would like to occur at your funeral]</li>
                                <li>That be clothed in [ please specify color and type]</li>
                                <li>That my remains be placed [ please specify how and where you would like your remains to be placed]</li>
                                <li>That the following songs be included in my funeral programme</li>
                                <li>
                                    <div>
                                        <p>
                                            That the following song is played at my wedding-
                                        </p>
                                        <ul type="none">
                                            <li>[please insert name of song1]</li>
                                            <li>[please insert name of song1]</li>
                                            <li>[please insert name of song1]</li>
                                        </ul>
                                    </div>
                                </li>
                            </ol>
                        </ul>
                    </div>
                </li>
                <li>
                    <div>
                        <p><strong>I GIVE DEVISE AND BEQUEATH:</strong></p>
                        <ol type="a">
                            <li>
                                <div>
                                    <p><strong>PROPERTY</strong></p>
                                    <ol type="i">
                                        <li>1st Property- situate at (please insert civic/ street address of the property), in the parish of [ INSERT PARISH]registered at (please insert Volume and Folio) of the Register Book of Titles to (please insert name of beneficiary).</li>
                                        <li>2nd Property- situate at (please insert civic/ street address of the property), in the parish of [ INSERT PARISH]registered at (please insert Volume and Folio) of the Register Book of Titles to (please insert name of beneficiary).</li>
                                        <li>3rd Property- situate at (please insert civic/ street address of the property), in the parish of [ INSERT PARISH]registered at (please insert Volume and Folio) of the Register Book of Titles to (please insert name of beneficiary).</li>
                                    </ol>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>SHARES AND STOCKS</strong></p>
                                    <ol type="i">
                                        <li>Shares in (please insert name of company) held in [INSERT COUNTRY] at [ INSERT NAME OF INVESTMENT COMPANY OR STOCK EXCHANGE] in account numbered (please insert account number) to (please insert name of beneficiary) of [ INSERT ADDRESS].</li>
                                        <li>Shares in (please insert name of company) held in [INSERT COUNTRY] at [ INSERT NAME OF INVESTMENT COMPANY OR STOCK EXCHANGE] in account numbered (please insert account number) to (please insert name of beneficiary) of [ INSERT ADDRESS].</li>
                                    </ol>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>INSURANCE</strong></p>
                                    <ol type="i">
                                        <li>Proceeds of insurance policy numbered (please insert account number), held at (please insert name of insurance company) located at (please insert address) , [INSERT COUNTRY]to (please insert name of beneficiary).</li>
                                        <li>Proceeds of insurance policy numbered (please insert account number), held at (please insert name of insurance company) located at (please insert address) , [INSERT COUNTRY]to (please insert name of beneficiary).</li>
                                    </ol>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>BANK ACCOUNTS</strong></p>
                                    <ol type="i">
                                        <li>Proceeds of bank account numbered [INSERT ACCOUNT NUMBER], held at [INSERT NAME OF FINANCIAL INSTITUTION] located at (please insert address) [INSERT COUNTRY] to (please insert name of beneficiary) of [ INSERT ADDRESS].</li>
                                        <li>Proceeds of bank account numbered [INSERT ACCOUNT NUMBER], held at [INSERT NAME OF FINANCIAL INSTITUTION] located at (please insert address) [INSERT COUNTRY] to (please insert name of beneficiary) of [ INSERT ADDRESS].</li>
                                        <li>Proceeds of bank account numbered [INSERT ACCOUNT NUMBER], held at [INSERT NAME OF FINANCIAL INSTITUTION] located at (please insert address) [INSERT COUNTRY] to (please insert name of beneficiary) of [ INSERT ADDRESS].</li>
                                    </ol>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>MOTOR VEHICLE</strong></p>
                                    <ol type="i">
                                        <li>[INSERT COLOR] [INSERT MAKE] [INSERT MODEL] Motor vehicle bearing Licence plate number [ INSERT NUMBER] and engine and chassis numbers (please insert numbers) to (please insert name of beneficiary) of [ INSERT ADDRESS].</li>
                                        <li>[INSERT COLOR] [INSERT MAKE] [INSERT MODEL] Motor vehicle bearing Licence plate number [ INSERT NUMBER] and engine and chassis numbers (please insert numbers) to (please insert name of beneficiary) of [ INSERT ADDRESS].</li>
                                        <li>[INSERT COLOR] [INSERT MAKE] [INSERT MODEL] Motor vehicle bearing Licence plate number [ INSERT NUMBER] and engine and chassis numbers (please insert numbers) to (please insert name of beneficiary) of [ INSERT ADDRESS].</li>
                                    </ol>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>UNPAID SALARY AND/EMOLUMENTS</strong></p>
                                    <p>Unpaid salary and/or emoluments with my employer, [Please insert Name of Employer] located at (please insert address) to (please insert name of beneficiary) of (please insert address).</p>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>NATIONAL HOUSING TRUST(NHT) CONTRIBUTIONS</strong></p>
                                    <p>Refund of National Housing Trust Contributions (please insert your National Insurance Scheme and Taxpayer Registration Numbers) to (please insert name of beneficiary) of (please insert address).</p>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>JEWELLERY</strong></p>
                                    <p>[ INSERT detail description] described as my Jewellery to (please insert name of beneficiary) of (please insert address).</p>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>FURNITURE</strong></p>
                                    <p>Furniture to (please insert name of beneficiary) of (please insert address).</p>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>PAINTINGS</strong></p>
                                    <p>Paintings to (please insert name of beneficiary) of (please insert address).</p>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <p><strong>FIREARM</strong></p>
                                    <p>Firearm bearing serial and firearm licence numbers (please insert numbers) to (please insert name of beneficiary) of (please insert address).</p>
                                </div>
                            </li>
                        </ol>
                    </div>
                </li>
                <li>
                    <div>
                        <p><strong>RESIDUAL ESTATE</strong></p>
                        <p>I give, devise and bequeath all the rest, residue and remainder of my estat, including any proceeds from the sale of assets to [(please insert name of residuary beneficiary) in equal shares.</p>                        
                    </div>
                </li>
            </ol>
            <div style="margin-top:40px;">
                <p><strong>IN WITNESS WHEREOF</strong> I have hereunto set my hand and seal this ...............day of .........20</p>
                <p>(Testator to sign here) .........................</p>
                <p><strong>SIGNED</strong> by the Testator the said (please insert name), a (please insert occupation) of (please insert address), in the parish of (please insert Parish), as my Last Will and Testament I delare that I have signed and executed this Last will and testament willingly and in the presence of the following witnesses, who are present at the same time and who have signed as witnesses in my presence:</p>
                <p><strong>WITNESSES</strong></p>
                <div>
                    <div style="display:inline-block;width:10%;padding:10px;border-right: 2px solid black;text-align: center;">
                        <p>Witnesses to sign here.</p>
                    </div>
                    <div style="display:inline-block;width:30%;padding:10px;">
                        <div>
                            <p>Name and Signature:  .........................</p>
                            <p>Address: .........................</p>
                            <p>Occupation:  .........................</p>
                            
                        </div>
                    </div>
                    <div style="display:inline-block;width:30%;padding:10px;">
                        <div>
                            <p>Name and Signature:  .........................</p>
                            <p>Address: .........................</p>
                            <p>Occupation:  .........................</p>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <button id="download-btn">Download PDF</button> -->
    </div>

    <!-- <script src="script.js"></script> -->
</body>
</html>
