<?php
/* Template Name: Will Testament Service  */

//Terminate if the Wills Helper plugin is not activated
// if (!is_plugin_active('wills-helper/wills-helper.php')) {
//     echo 'Wills Helper Plugin is not active, activate it first.';
//     exit;
// }

add_action('wp_head', function () {
    $html = <<<'HTML'
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/datepicker.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


        <script>
                tailwind.config = {
                important: true,
            }
            if (jQuery('body').hasClass('.logged-in')) {
                // Hide login items and show logout items for logged-in users
                jQuery('.menu-item-type-pmpro-login').hide();
                jQuery('.menu-item-type-pmpro-logout').show();
            } else {
                // Hide logout items and show login items for logged-out users
                jQuery('.menu-item-type-pmpro-logout').hide();
                jQuery('.menu-item-type-pmpro-login').show();
            }
        </script>

    HTML;
    $script = '<script src="'.get_stylesheet_directory_uri().'/assets/js/countries.js"></script>';
    echo $script;
    echo $html; 
});

get_header();
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
            'address1' => '',
            'address2' => '',
            'parish' => '',
            'occupation' => ''
        ],
        'sec3' => [
            'status' => '',
            'children' => '',
            'numChildren' => 1,
            'childDetails' => [['name'=>'','relation'=>'','dob'=>'','email'=>'','occupation'=>'','address'=>'','parish'=>'']],
            'grandChildren' => '',
            'numGrandChildren' => 1,
            'grandChildDetails' => [['name'=>'','relation'=>'','dob'=>'','email'=>'','occupation'=>'','address'=>'','parish'=>'']],
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
            'guardianDetails' => [['childName'=>'','name'=>'','reason'=>'','email'=>'','occupation'=>'','address'=>'','parish'=>'','acceptTerms'=>'']]
        ],
        'sec6' => [
            'executorDetails' => [['name'=>'','relation'=>'','address'=>'','occupation'=>'','parish'=>'']],
            'numExecutor' => 1,
            'alterOptions' => 1,
            'numAlterExecutor' => 0,
            'alterExecutorDetails' => [['name'=>'','relation'=>'','address'=>'','occupation'=>'','parish'=>'']],
        ],
        'sec7' => [
            'funeralOccur'  => '',
            'funeralClothed'  => '',
            'funeralPlaced' => '',
            'funeralsongs1' => '',
            'funeralsongs2' => '',
        ],
        'sec8' => [
            'positions' => [['position'=>'']],
        ],    
        'sec9' => [
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
            'specificsDetails' => [['type'=>1,'giftBenefIndex'=>0,'positions'=>0]],
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
        'sec10' => ['forgive'=>'1','forgiveDetails'=>''],
        'sec11' => ['attachement'=>'','meet_link'=>'','attachement_url'=>'','video_url'=>''],        
    ];
    $validatedData = [];
    if (is_array($defaultValues)) {
        foreach ($defaultValues as $section => $data) {
            if(is_array($defaultValues[$section])){            
                if(isset($willsFormData) && is_array($willsFormData) && array_key_exists($section,$willsFormData)){
                    $validatedData[$section] = is_array($data) ? array_merge($defaultValues[$section], $willsFormData[$section]) : $defaultValues[$section];
                }else{
                    $validatedData[$section] = $defaultValues[$section];
                }
            }
        }   
    }   
   return $validatedData;
}
$user_id = get_current_user_id();
function is_pmpro_member_active($member) {
    global $wpdb;
      $member_status = $wpdb->get_var('SELECT `status` from wp_pmpro_memberships_users where user_id='.$member.' order by ID DESC limit 1'); 
      return $member_status;
}

if (is_user_logged_in() && is_pmpro_member_active(get_current_user_id())=='active') {
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
    

?>
    
    <!-- Code here -->
    <script>
        const willsFormData = <?php echo json_encode($will_data); ?>;
        // console.log(willsFormData);        
    </script>
    <div x-data="data" x-init="updateState();" class="w-full">
        <div x-cloak>
            <div class="w-11/12 mx-auto p-8">
                <a href="<?= site_url() ?>/online-estate-planning/">
                    <button class="bg-green-600 text-white rounded border border-green-600 px-4 py-2 hover:bg-green-500">Back</button>
                </a>

                <div class="border-b-2 border-red-700 py-4">
                    <h1 class="text-4xl text-blue-500">MyWill™ - Main Menu For <?= $fullName ?></h1>
                </div>

            </div>
            <div x-show="activeForm === 'home'">
                <div class="w-10/12 mx-auto p-10">

                    <div class="mx-auto">

                        <div class="my-8">
                            <p>Here you can create a legal Last Will and Testament, custom-made for your local jurisdiction. You must print, sign and witness your Last Will and Testament to make it a legal document.</p>
                        </div>

                        <div class="border-2 border-blue-400 px-8 py-4 w-9/12 rounded-lg mb-8">
                            <ul>
                                <li @click="openPage('createMod')" class="text-xl font-bold my-4 text-blue-400 hover:text-blue-500 cursor-pointer"><span class="border-2 text-lg rounded-full p-4 inline-block mr-2 border-blue-400 text-center"><i class="fa-regular fa-file"></i> </span>Create or Modify your Will</li>
                                <li @click="openPage('view')" class="text-xl font-bold my-4 text-blue-400 hover:text-blue-500 cursor-pointer"><span class="border-2 text-lg rounded-full p-4 inline-block mr-2 border-blue-400 text-center"><i class="fa-regular fa-eye"></i> </span>View your Will</li>
                                <li @click="openPage('delete')" class="text-xl font-bold my-4 text-blue-400 hover:text-blue-500 cursor-pointer"><span class="border-2 text-lg rounded-full p-4 inline-block mr-2 border-blue-400 text-center"><i class="fa-regular fa-trash-can"></i> </span>Delete your Will</li>
                            </ul>
                        </div>


                        <div style="display:none;" class="border-2 border-blue-400 px-8 py-4 w-9/12 rounded-lg mb-8">
                            <ul>
                                <li @click="openPage('downPdf')" class="text-xl font-bold my-4 text-blue-400 hover:text-blue-500 cursor-pointer"><span class="border-2 text-lg rounded-full p-4 inline-block mr-2 border-blue-400 text-center"><i class="fa-regular fa-file"></i> </span>Download your Will (PDF file) [requires PDF viewer (e.g. Adobe Reader)]</li>
                                <li @click="openPage('email')" class="text-xl font-bold my-4 text-blue-400 hover:text-blue-500 cursor-pointer"><span class="border-2 text-lg rounded-full p-4 inline-block mr-2 border-blue-400 text-center"><i class="fa-regular fa-eye"></i> </span>Have your Will sent to you by Email (PDF file)</li>
                                <li @click="openPage('downWord')" class="text-xl font-bold my-4 text-blue-400 hover:text-blue-500 cursor-pointer"><span class="border-2 text-lg rounded-full p-4 inline-block mr-2 border-blue-400 text-center"><i class="fa-regular fa-trash-can"></i> </span>Download your Will (Word document) [requires Microsoft Word]</li>
                            </ul>
                        </div>

                        <div style="display:none;" class="border-2 border-blue-400 px-8 py-4 w-9/12 rounded-lg mb-8">
                            <ul>
                                <li @click="openPage('instructions')" class="text-xl font-bold my-4 text-blue-400 hover:text-blue-500 cursor-pointer"><span class="border-2 text-lg rounded-full p-4 inline-block mr-2 border-blue-400 text-center"><i class="fa-regular fa-file"></i> </span>Instructions for Printing, Signing and Updating your Will</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="activeForm === 'createMod'">
                <div class="w-10/12 mx-auto p-10">
                    <p>
                        Below is a list of the sections in the MyWill™ question-and-answer wizard.</p>
                    <br>

                    <p>If you have not yet started to answer the questions, or you'd like to review and possibly make changes to your answers, or even if you're not sure, click on "Start Here". Otherwise, you may click on any section to continue from that point or to make modifications by jumping to a particular section.
                    </p>

                    <p class="font-bold text-blue-400 hover:text-blue-500 my-8"><a href="">Return to the MyWill™ main menu</a></p>

                    <div>
                        <ul>

                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec1')">
                                Section 1: Introduction <span class="text-red-600">START HERE</span>
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec2')">
                                Section 2: Personal Details
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec3')">
                                Section 3: Family Status
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec4')">
                                Section 4: Other Beneficiaries
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec5')">
                                Section 5: Guardians for Minor Children
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec6')">
                                Section 6: Executor
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec7')">
                                Section 7: Funeral and Burial Arrangements
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec8')">
                                Section 8: Add Your Possession
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec9')">
                                Section 9: Distribute Your Possessions
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec10')">
                                Section 10: Forgive Debts
                            </li>
                            <li class="text-blue-400 hover:text-blue-500 cursor-pointer w-max" @click="openPage('sec11')">
                                Section 11: Next Steps
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div x-show="mainForm" class="w-10/12 mx-auto p-10">
                <div class="flex flex-row justify-between">
                    <div class="flex flex-row justify-center gap-4">
                        <div class="text-xl">Progress</div>
                        <div class="flex flex-col">
                            <progress x-bind:value="progressValue*10" max="100"></progress>
                            <div>Section <span x-text="progressValue"></span> of 11</div>
                        </div>
                    </div>
                    <div>
                        <select id="sec-select" x-model="selectedOpt" @change="selectChanged($event)">
                            <template x-for="(item, index) in sectionSelOption">
                                <option :value="allPages[index]" :selected="selectedOpt == allPages[index]" x-text="item"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="mt-10">
                    <div class="w-full">
                        <div class="flex flex-row justify-start gap-12">
                            <!-- Q n A -->
                            <div class="w-4/12 bg-blue-600 text-white">
                                <div class="flex-initial px-10 py-4">
                                    <p class="text-2xl">Common Questions:</p>
                                    <div class="mt-8">
                                        <template x-for="[question, answer] in Object.entries(qna[activeForm])">
                                            <div>
                                                <details>
                                                    <summary x-text="question"></summary>
                                                    <p class="text-sm" x-text="answer"></p>
                                                </details>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="w-8/12">
                                <div x-show="activeForm === 'sec1'">
                                    <h1 class="text-4xl text-blue-500">Introduction</h1>
                                    <i class="font-bold">We've made this easy! This should only take a short amount of your time...</i>
                                    <p>You will be asked a series of questions to help you create your Last Will and Testament.</p>
    
                                    <p>While answering the questions, if you need general assistance on the section, just read the Common Questions which appear on every page. If you don't see the questions, simply click on the big near the top of the page.</p>
    
                                    <p>Specific help for parts of a page that may be unclear is available by tapping (or moving your mouse over) the small symbol which appears next to some questions.</p>
    
                                    <p>At any point you can save your work and return later.</p>
    
                                    <p>When you are done, you should print and sign your document in the presence of witnesses to make it a legal Will.</p>
    
                                    <p>To begin stepping through these questions, click on the "NEXT" button which appears below...</p>
                                </div>
                                <div x-show="activeForm === 'sec2'">
                                    <h1 class="text-4xl text-blue-500">Personal Details</h1>
                                    <p>
                                        It is important that you provide the information below so that the MyWill™ wizard can format a document that is custom-made based on your name, gender and local jurisdiction.
                                    </p>
    
                                    <p><span class="text-red-500">*</span> = required information</p>
                                    <div>
                                        <div>
                                            <p></p>
                                            <div>
    
                                            </div>
                                            <div>
                                                <div>
                                                    <label for="prefix">Prefix (eg. Mr., Ms., Dr.)<span class="text-red-500">*</span></label>
                                                    <input x-model="formData.sec2.prefix" type="text" id="prefix" value="" @input="validate('prefix')" :class="{'border-red-500': validateError.prefix}">
                                                    <small x-text="validateError.prefix" class="text-red-500 block"></small>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-xl-4">
                                                    <div>
                                                        <label for="firstName">First Name<span class="text-red-500">*</span></label>
                                                        <input x-model="formData.sec2.firstName" type="text" value="" id="firstName" @input="validate('firstName')" :class="{'border-red-500': validateError.firstName}">
                                                        <small x-text="validateError.firstName" class="text-red-500 block"></small>
    
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-xl-4">
                                                    <div>
                                                        <label for="middleName">Middle Name</label>
                                                        <input x-model="formData.sec2.middleName" type="text" value="" id="middleName" @input="validate('middleName')" :class="{'border-red-500': validateError.middleName}">
                                                        <small x-text="validateError.middleName" class="text-red-500 block"></small>
    
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-xl-4">
                                                    <div>
                                                        <label for="lastName">Last Name/Surname<span class="text-red-500">*</span></label>
                                                        <input x-model="formData.sec2.lastName" type="text" value="" id="lastName" @input="validate('lastName')" :class="{'border-red-500': validateError.lastName}">
                                                        <small x-text="validateError.lastName" class="text-red-500 block"></small>
    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 col-xl-4">
                                                    <div>
                                                        <label for="suffix">Suffix (eg. Jr., Sr.)</label>
                                                        <input x-model="formData.sec2.suffix" type="text" value="" id="suffix" @input="validate('suffix')" :class="{'border-red-500': validateError.suffix}">
                                                        <small x-text="validateError.suffix" class="text-red-500 block"></small>
    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <div>
                                                        <label for="country">Country<span class="text-red-500">*</span></label>
                                                        <select x-model="formData.sec2.country" id="country" class="select" @change="validate('country')" :class="{'border-red-500': validateError.country}"></select>
                                                        <small x-text="validateError.country" class="text-red-500 block"></small>
                                                    </div>
                                                </div>
                                            <!-- <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="state">State / Province / County <span class="text-red-500">*</span></label>
                                                        <select x-model="formData.sec2.state" id="state" class="select" @change="validate('state')" :class="{'border-red-500': validateError.state}"></select>
                                                        <input type="hidden" id="prePopulatedState" x-model="formData.sec2.state">
                                                        <small x-text="validateError.state" class="text-red-500 block"></small>
                                                    </div>
                                                </div> -->
                                                <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="address1">Address 1<span class="text-red-500">*</span></label>
                                                        <input x-model="formData.sec2.address1" type="text" value="" id="address1" @input="validate('address1')" :class="{'border-red-500': validateError.address1}">
                                                        <small x-text="validateError.address1" class="text-red-500 block"></small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="address2">Address 2</label>
                                                        <input x-model="formData.sec2.address2" type="text" value="" id="address2" @input="validate('address2')" :class="{'border-red-500': validateError.address2}">
                                                        <small x-text="validateError.address2" class="text-red-500 block"></small>
                                                    </div>
                                                </div>
                                                <!-- Populate the Countires and States using assets/js/countries.js -->
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div>
                                                    <label for="parish">Parish<span class="text-red-500">*</span></label>
                                                    <input x-model="formData.sec2.parish" type="text" value="" id="parish" @input="validate('parish')" :class="{'border-red-500': validateError.parish}">
                                                    <small x-text="validateError.parish" class="text-red-500 block"></small>
    
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div>
                                                    <label for="occupation">Occupation<span class="text-red-500">*</span></label>
                                                    <input x-model="formData.sec2.occupation" type="text" value="" id="occupation" @input="validate('occupation')" :class="{'border-red-500': validateError.occupation}">
                                                    <small x-text="validateError.occupation" class="text-red-500 block"></small>
    
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div>
                                                    <label for="email">Email Address<span class="text-red-500">*</span></label>
                                                    <input x-model="formData.sec2.email" type="email" value="" id="email" @input="validate('email')" :class="{'border-red-500': validateError.email}">
                                                    <small x-text="validateError.email" class="text-red-500 block"></small>
    
                                                </div>
                                            </div>
                                            <div class="col-12 mt-4">
                                                <div>
                                                    <p class="form-label mt-2">Gender pronoun:<span class="text-red-500">*</span></p>
                                                    <div class="flex flex-col">
                                                        <div>
                                                            <input x-model="formData.sec2.gender" type="radio" name="gender" value="1" class="mr-4" @change="validate('gender')"><label for="">Male (he/his)</label>
                                                        </div>
                                                        <div>
                                                            <input x-model="formData.sec2.gender" type="radio" name="gender" value="2" class="mr-4" @change="validate('gender')"><label for="">Female (she/her)</label>
                                                        </div>
                                                        <div>
                                                            <input x-model="formData.sec2.gender" type="radio" name="gender" value="3" class="mr-4" @change="validate('gender')"><label for="">Neutral (they/their)</label>
                                                        </div>
                                                        <small x-text="validateError.gender" class="text-red-500 block"></small>
    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="activeForm === 'sec3'">

                                    <!-- Section 3 first form - Intro  -->
                                    <div x-show="activeSubForm === 'intro'">
                                        <h1 class="text-4xl text-blue-500">Family Status</h1>
                                        <p>* = required information</p>
                                        <div>
                                            <label for="status">Marital Status</label>
                                            <select x-model="formData.sec3.status" name="status" id="status" @change="validate('status')" :class="{'border-red-500': validateError.status}">
                                                <option value="">[make selection]</option>
                                                <option value="1">single</option>
                                                <option value="2">married</option>
                                                <option value="3">separated</option>
                                                <option value="4">separated, but want my spouse to be the main beneficiary</option>
                                                <option value="5">divorced</option>
                                                <option value="6">widowed</option>
                                                <option value="7">in a civil union / domestic partnership</option>
                                            </select>
                                            <small x-text="validateError.status" class="text-red-500 block"></small>
                                        </div>
    
                                        <div class="flex flex-col">
                                            <p>Living Children:</p>
    
                                            <div>
                                                <input type="radio" name="children" value="1" class="mr-4" x-model="formData.sec3.children" @change="validate('children')"><label for="">Yes</label>
                                            </div>
                                            <div>
                                                <input type="radio" name="children" value="2" class="mr-4" x-model="formData.sec3.children" @change="validate('children')"><label for="">No</label>
                                            </div>
                                            <small x-text="validateError.children" class="text-red-500 block"></small>
                                        </div>
    
                                        <div class="flex flex-col">
                                            <p>Living Grand-Children:</p>
    
                                            <div>
                                                <input type="radio" name="grandChildren" value="1" class="mr-4" x-model="formData.sec3.grandChildren" @change="validate('grandChildren')"><label for="">Yes</label>
                                            </div>
                                            <div>
                                                <input type="radio" name="grandChildren" value="2" class="mr-4" x-model="formData.sec3.grandChildren" @change="validate('grandChildren')"><label for="">No</label>
                                            </div>
                                            <small x-text="validateError.grandChildren" class="text-red-500 block"></small>
                                        </div>
                                    </div>
    
                                    <!-- Partner/Spouse Details -->
                                    <div x-show="activeSubForm === 'partner'">
                                        <h1 class="text-4xl text-blue-500">Spouse/Partner Details</h1>
                                        <p>* = required information</p>
    
                                        <div>
                                            <label for="">Full Name</label>
                                            <input type="text" name="" id="" x-model="formData.sec3.fullName" @input="validate('fullName')">
                                            <small x-text="validateError.fullName" class="text-red-500 block"></small>
                                        </div>
    
                                        <div>
                                            <label for="">Relation</label>
                                            <select name="" id="" x-model="formData.sec3.relation" @change="validate('relation')">
                                                <option>[make selection]</option>
                                                <option value="wife">wife</option>
                                                <option value="husband">husband</option>
                                                <option value="common law wife">common law wife</option>
                                                <option value="common law husband">common law husband</option>
                                                <option value="partner">partner</option>
                                            </select>
                                            <small x-text="validateError.relation" class="text-red-500 block"></small>
                                        </div>
    
                                        <div>
                                            <p class="form-label mt-2">Gender pronoun:<span class="text-red-500">*</span></p>
                                            <div class="flex flex-col">
                                                <div>
                                                    <input type="radio" name="genderS" value="1" x-model="formData.sec3.partnerGender" @change="validate('partnerGender')" class="mr-4"><label for="">Male (he/his)</label>
                                                </div>
                                                <div>
                                                    <input type="radio" name="genderS" value="2" x-model="formData.sec3.partnerGender" @change="validate('partnerGender')" class="mr-4"><label for="">Female (she/her)</label>
                                                </div>
                                                <div>
                                                    <input type="radio" name="genderS" value="3" x-model="formData.sec3.partnerGender" @change="validate('partnerGender')" class="mr-4"><label for="">Neutral (they/their)</label>
                                                </div>
                                                <small x-text="validateError.partnerGender" class="text-red-500 block"></small>
                                            </div>
                                        </div>
                                    </div>
    
                                    <!-- Children -->
                                    <div x-show="activeSubForm === 'children'">
                                        <h1 class="text-4xl text-blue-500">Identify Children</h1>
                                        <p>* = required information</p>
                                        <template x-for="(item,childIndex) in formData.sec3.childDetails">
                                            <div class="mb-8">
                                                <div>
                                                    <label for=""><span x-show="childIndex >= 1" x-text="'#'+(childIndex+1)"></span> Child's Full Name</label>
                                                    <input x-model="formData.sec3.childDetails[childIndex].name" type="text">
                                                </div>
            
                                                <div>
                                                    <p class="form-label mt-2">Relationship:<span class="text-red-500">*</span></p>
                                                    <div class="flex flex-col">
                                                        <div>
                                                            <input type="radio" :name="'relation'+childIndex" x-model="formData.sec3.childDetails[childIndex].relation" value="1" class="mr-4"><label for="">Son</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'relation'+childIndex" x-model="formData.sec3.childDetails[childIndex].relation" value="2" class="mr-4"><label for="">Daughter</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'relation'+childIndex" x-model="formData.sec3.childDetails[childIndex].relation" value="3" class="mr-4"><label for="">Gender Neutral Child</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'relation'+childIndex" x-model="formData.sec3.childDetails[childIndex].relation" value="4" class="mr-4"><label for="">Stepson</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'relation'+childIndex" x-model="formData.sec3.childDetails[childIndex].relation" value="5" class="mr-4"><label for="">Stepdaughter</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'relation'+childIndex" x-model="formData.sec3.childDetails[childIndex].relation" value="6" class="mr-4"><label for="">Gender neutral stepchild</label>
                                                        </div>
                                                    </div>
                                                </div>                                                
                                                <div class="mt-2 mb-3">
                                                    <label for=""><span x-show="childIndex >= 1" x-text="'#'+(childIndex+1)"></span> Child's Date of birth</label>
                                                    <input type="date" x-model="formData.sec3.childDetails[childIndex].dob" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" max="2018-12-31"> 
                                                </div>            


                                                <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="email">Email Address</label>
                                                        <input x-model="formData.sec3.childDetails[childIndex].email" type="email" >
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="occupation">Occupation</label>
                                                        <input x-model="formData.sec3.childDetails[childIndex].occupation" type="text">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="address">Address</label>
                                                        <input x-model="formData.sec3.childDetails[childIndex].address" type="text">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="parish">Parish</label>
                                                        <input x-model="formData.sec3.childDetails[childIndex].parish" type="text">
                                                    </div>
                                                </div>


                                                <button @click="removeChild(childIndex)" class="bg-red-500 px-6 py-2 text-white hover:bg-red-600">Remove Child</button>
                                            </div>
                                        </template>
    
                                        <div>
                                            <button x-show="formData.sec3.numChildren < 5" @click="addChild($event)" class="bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add Child</button>
                                        </div>
                                    </div>
    
                                    <!-- Grand Child -->    
                                    <div x-show="activeSubForm === 'grandChildren'">
                                        <h1 class="text-4xl text-blue-500">Identify Grandchildren</h1>
    
                                        <p>Identifying all of your grandchildren is optional. It allows you to choose them later in this wizard if you decide to leave them some of your assets. If you do not plan on leaving anything specific to your grandChildren, you can simply skip this page.</p>
    
                                        <p>* = required information</p>
                                        
                                        <div>
                                            <div>
                                                <input type="radio" value="1" x-model="formData.sec3.grandChildrenDirect" id="">
                                                <label for="">I have grandchildren, but I am NOT leaving them something directly in my Will</label>
                                            </div>
                                            <div>
                                                <input type="radio" value="2" x-model="formData.sec3.grandChildrenDirect" id="">
                                                <label for="">I have grandchildren, and I MIGHT OR MIGHT NOT leave them something in my Will</label>
                                            </div>
                                        </div>
                                        <template x-for="(item,childIndex) in formData.sec3.grandChildDetails">
                                            <div class="mt-8 mb-8">
                                                <div>
                                                    <label for=""><span x-show="childIndex >= 1" x-text="'#'+(childIndex+1)"></span> Grandchild's Full Name</label>
                                                    <input x-model="formData.sec3.grandChildDetails[childIndex].name" type="text">
                                                </div>
            
                                                <div>
                                                    <p class="form-label mt-2">Relationship:<span class="text-red-500">*</span></p>
                                                    <div class="flex flex-col">
                                                        <div>
                                                            <input type="radio" :name="'relationGrandChild'+childIndex" x-model="formData.sec3.grandChildDetails[childIndex].relation" value="1" class="mr-4"><label for="">Grand Son</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'relationGrandChild'+childIndex" x-model="formData.sec3.grandChildDetails[childIndex].relation" value="2" class="mr-4"><label for="">Grand Daughter</label>
                                                        </div>
                                                        <!-- <div>
                                                            <input type="radio" :name="'relationGrandChild'+childIndex" x-model="formData.sec3.grandChildDetails[childIndex].relation" value="3" class="mr-4"><label for="">Gender Neutral Child</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'relationGrandChild'+childIndex" x-model="formData.sec3.grandChildDetails[childIndex].relation" value="4" class="mr-4"><label for="">Stepson</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'relationGrandChild'+childIndex" x-model="formData.sec3.grandChildDetails[childIndex].relation" value="5" class="mr-4"><label for="">Stepdaughter</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'relationGrandChild'+childIndex" x-model="formData.sec3.grandChildDetails[childIndex].relation" value="6" class="mr-4"><label for="">Gender neutral stepchild</label>
                                                        </div> -->
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <label for=""><span x-show="childIndex >= 1" x-text="'#'+(childIndex+1)"></span> Child's Date of birth</label>
                                                    <input type="date" x-model="formData.sec3.grandChildDetails[childIndex].dob" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" max="2018-12-31">                                                    
                                                </div>

                                                 <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="email">Email Address</label>
                                                        <input x-model="formData.sec3.grandChildDetails[childIndex].email" type="email" >
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="occupation">Occupation</label>
                                                        <input x-model="formData.sec3.grandChildDetails[childIndex].occupation" type="text">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="address">Address</label>
                                                        <input x-model="formData.sec3.grandChildDetails[childIndex].address" type="text">
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <div>
                                                        <label for="parish">Parish</label>
                                                        <input x-model="formData.sec3.grandChildDetails[childIndex].parish" type="text">
                                                    </div>
                                                </div>
                                                <!-- <button x-show="childIndex >= 1" @click="removeGrandChild(childIndex)" class="bg-red-500 px-6 py-2 text-white hover:bg-red-600">Remove Child</button> -->
                                                <button @click="removeGrandChild(childIndex)" class="bg-red-500 px-6 py-2 text-white hover:bg-red-600">Remove Child</button>
                                            </div>
                                        </template>
    
                                        <div>
                                            <button x-show="formData.sec3.numChildren < 5" @click="addGrandChild($event)" class="bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add Child</button>
                                        </div>
                                    </div>

                                    <!-- Deceased Members -->
                                    <div x-show="activeSubForm === 'deceased'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Deceased Family Members</h1>
                                            <p>If any members of your immediate family (a spouse or a child) have passed away, you should list them here.</p>
                                            <div>
                                                <div>
                                                    <input type="radio" value="1" x-model="formData.sec3.deceased" id="">
                                                    <label for="">I DO NOT have any deceased family members</label>
                                                </div>
                                                <div>
                                                    <input type="radio" value="2" x-model="formData.sec3.deceased" id="">
                                                    <label for="">I have deceased family members</label>
                                                </div>                                                
                                            </div>
                                            <div x-show="formData.sec3.deceased==2">
                                                <div class="mt-6">
                                                    <template x-for="(item,childIndex) in formData.sec3.deceasedDetails">
                                                        <div class="mt-8 mb-8">
                                                            <p><span class="text-red-500">*</span> = required information</p>
                                                            <div>
                                                                <label for=""><span x-show="childIndex >= 1" x-text="'#'+(childIndex+1)"></span> Member's Full Name</label>
                                                                <input x-model="formData.sec3.deceasedDetails[childIndex].name" type="text">
                                                            </div>
                        
                                                            <div>                                                                
                                                                <label for="">Gender pronoun</label>
                                                                <select id="" x-model="formData.sec3.deceasedDetails[childIndex].gender">
                                                                    <option value="">[make selection]</option>
                                                                    <option value="Male (he/his)">Male (he/his)</option>
                                                                    <option value="Female (she/her)">Female (she/her)</option>
                                                                    <option value="Neutral (they/their)">Neutral (they/their)</option>                                                                    
                                                                </select>
                                                                <small x-text="validateError.gender" class="text-red-500 block"></small>
                                                            </div>
                                                            <div class="mt-2">
                                                                <label for="">Relationship</label>
                                                                <select id="" x-model="formData.sec3.deceasedDetails[childIndex].relation">
                                                                    <option value="">[make selection]</option>
                                                                    <option value="wife">wife</option>
                                                                    <option value="husband">husband</option>
                                                                    <option value="common law wife">common law wife</option>
                                                                    <option value="common law husband">common law husband</option>
                                                                    <option value="partner">partner</option>
                                                                    <option value="son">son</option>
                                                                    <option value="daughter">daughter</option>
                                                                    
                                                                </select>
                                                                <small x-text="validateError.gender" class="text-red-500 block"></small>
                                                            </div>  
                                                            <button x-show="childIndex >= 1" @click="removeDeceased(childIndex)" class="bg-red-500 px-6 py-2 text-white hover:bg-red-600">Remove</button>
                                                        </div>
                                                    </template>
                                                    <div>
                                                        <button x-show="formData.sec3.numChildren < 5" @click="addDeceased($event)" class="bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add</button>
                                                    </div>                                                    
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div x-show="activeForm === 'sec4'">
                                    <div>
                                        <h1 class="text-4xl text-blue-500">Identify Others to be Included in your Will</h1>                                        
                                    </div>
                                    <div>
                                        <p class="mb-2">If there are other people or organizations to be included in your Will, you can name them now. You can also add more names later, as you are working through this wizard.</p>
                                        <p class="mb-2">You should not include your spouse/partner, children, or grandchildren on this page, because they would have been named in previous pages of this wizard.</p>
                                        <p class="mb-2">By listing the beneficiaries here, it makes it easier to select them later on for receiving a bequest. You are also able to set up a trust for them. They do not appear in your Will unless they are specifically selected in Section 7.</p>                                        
                                    </div>
                                    <div>                                      
                                        <div>
                                            <input type="radio" value="1" name="otherBeneficiaries" x-model="formData.sec4.otherBeneficiaries" id="">
                                            <label for="otherBeneficiaries">I have no other beneficiaries, or will add them later</label>
                                        </div>
                                        <div>
                                            <input type="radio" value="2" name="otherBeneficiaries" x-model="formData.sec4.otherBeneficiaries" id="">
                                            <label for="otherBeneficiaries">I would like to add beneficiaries now</label>
                                        </div>                                                                                                                             
                                    </div>
                                    <div x-show="formData.sec4.otherBeneficiaries==2">
                                        <div class="mt-6">
                                            <template x-for="(item,childIndex) in formData.sec4.beneficiaryDetails">
                                                <div class="mt-8 mb-8">
                                                    <p><span class="text-red-500">*</span> = required information</p>
                                                    <div>
                                                        <p class="form-label mt-2"><span class="text-red-500">*</span> Gender/Type:</p>
                                                        <div class="flex flex-col">
                                                            <div>
                                                                <input type="radio" :name="'genderBeneficiary'+childIndex" :id="'genderBeneficiary1'+childIndex" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="1" class="mr-4"><label :for="'genderBeneficiary1'+childIndex">Male</label>
                                                            </div>
                                                            <div>
                                                                <input type="radio" :name="'genderBeneficiary'+childIndex" :id="'genderBeneficiary2'+childIndex" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="2" class="mr-4"><label :for="'genderBeneficiary2'+childIndex">Female</label>
                                                            </div>
                                                            <div>
                                                                <input type="radio" :name="'genderBeneficiary'+childIndex" :id="'genderBeneficiary3'+childIndex" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="3" class="mr-4"><label :for="'genderBeneficiary3'+childIndex">Neutral</label>
                                                            </div>
                                                            <div>
                                                                <input type="radio" :name="'genderBeneficiary'+childIndex" :id="'genderBeneficiary4'+childIndex" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="4" class="mr-4"><label :for="'genderBeneficiary4'+childIndex">Charity/Org</label>
                                                            </div>
                                                            <div>
                                                                <input type="radio" :name="'genderBeneficiary'+childIndex" :id="'genderBeneficiary5'+childIndex" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="5" class="mr-4"><label :for="'genderBeneficiary5'+childIndex">Group</label>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="mb-2">
                                                        <div class="mt-2">
                                                            <label for=""><span class="text-red-500">*</span> Full Name</label>
                                                            <input x-model="formData.sec4.beneficiaryDetails[childIndex].name" type="text">
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for=""><span class="text-red-500">*</span> Relationship</label>
                                                            <input x-model="formData.sec4.beneficiaryDetails[childIndex].relation" type="text">
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for=""><span class="text-red-500">*</span> Email Address</label>
                                                            <input x-model="formData.sec4.beneficiaryDetails[childIndex].email" type="email">
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for=""><span class="text-red-500">*</span> Address</label>
                                                            <input x-model="formData.sec4.beneficiaryDetails[childIndex].address" type="text">
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for="parish"><span class="text-red-500">*</span> Parish</label>
                                                            <input x-model="formData.sec4.beneficiaryDetails[childIndex].parish" type="text">
                                                    </div>
                                                    </div>
                                                    <button @click="removeBeneficiary(childIndex)" class="bg-red-500 px-6 py-2 text-white hover:bg-red-600">Remove</button>                                                   
                                                </div>
                                            </template>
                                            <div>
                                                <button x-show="formData.sec4.numBeneficiaries < 5" @click="addBeneficiary($event)" class="bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add</button>
                                            </div>                                                    
                                        </div>
                                    </div>
                                </div>
                                <div x-show="activeForm === 'sec5'">
                                    <div>
                                        <h1 class="text-4xl text-blue-500">Identify Guardians for Minor Children</h1>
                                    </div>
                                    <!-- Minor Children Details -->
                                    <div x-show="hasMinorChild()">
                                        <div>
                                            <p>It is very important that you name a guardian for each of your minor children. This person will be responsible for the care of your child if there are no parents available.</p>
                                        </div>
                                        <div class="mt-2">
                                            <div class="flex justify-start gap-6 border-2">
                                                <div class="w-4/12 p-4">
                                                    <div>
                                                        <p>Child's name	</p>
                                                    </div>                                                    
                                                </div>
                                                <div class="w-8/12 border-l-2 p-4">
                                                    <div>
                                                        <p>Guardians</p>
                                                    </div>                                                    
                                                </div>                                                
                                            </div>
                                            <template x-for="(item,childIndex) in formData.sec5.guardianDetails">
                                                <div class="flex justify-start gap-6 border-2">
                                                    <div class="w-4/12 p-4">
                                                        <div>
                                                            <p x-text="minorChildren[childIndex]"></p>
                                                        </div>                                                    
                                                    </div>
                                                    <div class="w-8/12 border-l-2 p-4">
                                                        <div>
                                                            <div>
                                                                <input type="hidden" :value="minorChildren[childIndex]" x-model="formData.sec5.guardianDetails[childIndex].childName">
                                                                
                                                                <div>
                                                                    <label for="">Personal guardian's full name:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[childIndex].name" type="text">
                                                                </div>
                                                                <div>
                                                                    <label for="">Reason for choosing this guardian:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[childIndex].reason" type="text">
                                                                </div>
                                                                <div>
                                                                    <label for="">Email Address:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[childIndex].email" type="email">
                                                                </div>
                                                                <div>
                                                                    <label for="">Occupation:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[childIndex].occupation" type="text">
                                                                </div>
                                                                <div>
                                                                    <label for="">Address:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[childIndex].address" type="text">
                                                                </div>
                                                                <div>
                                                                    <label for="">Parish:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[childIndex].parish" type="text">
                                                                </div>
                                                                <div>
                                                                    <input type="checkbox" id="acceptTerms" x-model="formData.sec5.guardianDetails[childIndex].acceptTerms" :checked="formData.sec5.guardianDetails[childIndex].acceptTerms=='true'">
                                                                    <label for="acceptTerms">The Guardian of this minor child is 18 above</label>
                                                                </div>
                                                                <!-- <div>
                                                                    <label for="">Alternate guardian's full name:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[childIndex].alterName" type="text">
                                                                </div> -->
                                                            </div>
                                                        </div>                                                    
                                                    </div>                                                
                                                </div>    
                                            </template>                                            
                                                                                
                                        </div>
                                    </div>
                                    <div x-show="hasMinorgrChild()"> 
                                        <div class="mt-2">                                      
                                            <template x-for="(item, ChildIndex) in formData.sec5.guardianDetails" :key="ChildIndex">
                                                
                                                <div class="flex justify-start gap-6 border-2">
                                                    <div class="w-4/12 p-4">
                                                        <div>
                                                            
                                                            <p x-text="minorgrandChildren[ChildIndex]"></p>
                                                        </div>                                                    
                                                    </div>
                                                    <div class="w-8/12 border-l-2 p-4">
                                                        <div>
                                                            <div>
                                                                <input type="hidden" :value="grandChildren[ChildIndex]" x-model="formData.sec5.guardianDetails[ChildIndex].childName">
                                                                
                                                                <div>
                                                                    <label for="">Personal guardian's full name:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[ChildIndex].name" type="text">
                                                                </div>
                                                                <div>
                                                                    <label for="">Reason for choosing this guardian:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[ChildIndex].reason" type="text">
                                                                </div>
                                                                <div>
                                                                    <label for="">Email Address:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[ChildIndex].email" type="email">
                                                                </div>
                                                                <div>
                                                                    <label for="">Occupation:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[ChildIndex].occupation" type="text">
                                                                </div>
                                                                <div>
                                                                    <label for="">Address:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[ChildIndex].address" type="text">
                                                                </div>
                                                                <div>
                                                                    <label for="">Parish:</label>
                                                                    <input x-model="formData.sec5.guardianDetails[ChildIndex].parish" type="text">
                                                                </div>
                                                                <div>
                                                                    <input type="checkbox" id="acceptTerms" x-model="formData.sec5.guardianDetails[ChildIndex].acceptTerms" :checked="formData.sec5.guardianDetails[ChildIndex].acceptTerms=='true'">
                                                                    <label for="acceptTerms">The Guardian of this minor child is 18 above</label>
                                                                </div>
                                                            </div>
                                                        </div>                                                    
                                                    </div>                                                
                                                </div>    
                                            </template>                                        
                                                                                
                                        </div>
                                    </div>
                                    <!-- No Minor Children -->
                                    <div x-show="!hasMinorChild()">
                                        <div>
                                            <p class="mb-2">You have indicated that you have no minor children, so you do not need to identify any guardians.</p>
                                            <p class="mb-2">Click "NEXT" to continue...</p>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="activeForm === 'sec6'">
                                    <!-- Sub section intro of section 6 -->
                                    <div x-show="activeSubForm === 'intro'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Executor</h1>
                                        </div>
                                        <div>
                                            <p class="mb-2">
                                                Here you name the person you would like to be the executor of your Will. This person will be responsible for carrying out your wishes as specified in your Will, including the distribution of your possessions to your beneficiaries.
                                            </p>
                                            <p class="mb-2">
                                                You must identify somebody here. Although it is common to list a single executor, you may name up to 3 executors who must then work together to carry out your wishes. On the next page you will be able to name alternate  executors to take the place of those unable to serve.
                                            </p>
                                            <p class="mb-2">
                                                We understand that you may need to talk to other people before naming an executor. However, if you are stuck, you can name a person now and come back and change it later.
                                            </p>
                                            <p class="mb-2">
                                                I would like the following to be the executor of my Will:
                                            </p>
                                            <p class="mb-2">
                                                <span class="text-red-500">*</span> = required information
                                            </p>
                                        </div>
                                        <div>
                                            <template x-for="(item,childIndex) in formData.sec6.executorDetails">
                                                <div class="mb-4">
                                                    <div>
                                                        <h2 x-text="getNumFormat(childIndex+1)+' Executor'" class="text-2xl text-blue-500"></h2>
                                                    </div>
                                                    <div>
                                                        <label for=""><span class="text-red-500">*</span> Full Name</label>
                                                        <input x-model="formData.sec6.executorDetails[childIndex].name" type="text">
                                                    </div>
                                                    <div>
                                                        <label for=""><span class="text-red-500">*</span> Relationship</label>
                                                        <input x-model="formData.sec6.executorDetails[childIndex].relation" type="text">
                                                    </div>
                                                    <div>
                                                        <label for=""><span class="text-red-500">*</span> Email Address</label>
                                                        <input x-model="formData.sec6.executorDetails[childIndex].email" type="email">
                                                    </div>
                                                    <div>
                                                        <label for=""><span class="text-red-500">*</span> Occupation</label>
                                                        <input x-model="formData.sec6.executorDetails[childIndex].occupation" type="text">
                                                    </div>
                                                    <div>
                                                        <label for=""><span class="text-red-500">*</span> Address</label>
                                                        <input x-model="formData.sec6.executorDetails[childIndex].address" type="text">
                                                    </div>

                                                    <div>
                                                        <label for=""><span class="text-red-500">*</span> Parish</label>
                                                        <input x-model="formData.sec6.executorDetails[childIndex].parish" type="text">
                                                    </div>
                                                    
                                                    <button @click="removeExecutor(childIndex)" class="mt-2 bg-red-500 px-6 py-2 text-white hover:bg-red-600">
                                                        <span x-text="'DELETE ' + getNumFormat(childIndex+1) + ' EXECUTOR'"></span>
                                                    </button> 
                                                </div>
                                            </template>
                                            <div>
                                                <button x-show="formData.sec6.numExecutor < 5" @click="addExecutor($event)" class="mt-2 bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sub section Alternate Executor of section 6 -->
                                 <!--    <div x-show="activeSubForm === 'alterExecutor'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Alternate Executor</h1>
                                        </div>
                                        <div x-show="getNumExecutors()==1">
                                            <p class="mb-2">On the previous page, I identified a single person (<span x-text="getExecutorNames()"></span>) to be the executor of my Will.</p>                                            
                                            <p class="mb-2">If for some reason <span x-text="getExecutorNames()"></span> is unable to serve as the executor of my Will:</p>
                                        </div>
                                        <div x-show="getNumExecutors()>1">
                                            <p class="mb-2">On the previous page, I identified multiple executors (<span x-text="getExecutorNames()"></span>), who must work together to carry out my wishes.</p>                                            
                                            <p class="mb-2">I choose:</p>
                                        </div>
                                        <div>
                                            <div class="flex flex-col">
                                                <div>
                                                    <input type="radio" name="alterOptions" x-model="formData.sec6.alterOptions" id="alterOpt1" value="1" class="mr-4"><label for="alterOpt1">NO ALTERNATES --- If for some reason ANY or ALL of these people are unable to serve, I do not want to identify any alternates</label>
                                                </div>
                                                <div>
                                                    <input type="radio" name="alterOptions" x-model="formData.sec6.alterOptions" id="alterOpt2" value="2" class="mr-4"><label for="alterOpt2">LIST OF REPLACEMENTS --- If for some reason ANY of these people are unable to serve, I would like the following to take their place, in the order listed below:</label>
                                                </div>
                                                <div>
                                                    <input type="radio" name="alterOptions" x-model="formData.sec6.alterOptions" id="alterOpt3" value="3" class="mr-4"><label for="alterOpt3">ALTERNATE PLAN --- If for some reason ALL of these people are unable to serve, I would like the following to take their place (if I identify more than 1 person below, then they must work together to carry out my wishes):</label>
                                                </div>                                                                                                            
                                            </div>
                                            <div x-show="formData.sec6.alterOptions!=1">
                                                <div class="mt-8">
                                                    <template x-for="(item,childIndex) in formData.sec6.alterExecutorDetails">
                                                        <div class="mb-4">
                                                            <div>
                                                                <h2 x-text="getNumFormat(childIndex+1)+' Alternate Executor'" class="text-2xl text-blue-500"></h2>
                                                            </div>
                                                            <div>
                                                                <label for=""><span class="text-red-500">*</span> Full Name</label>
                                                                <input x-model="formData.sec6.alterExecutorDetails[childIndex].name" type="text">
                                                            </div>
                                                            <div>
                                                                <label for=""><span class="text-red-500">*</span> Relationship</label>
                                                                <input x-model="formData.sec6.alterExecutorDetails[childIndex].relation" type="text">
                                                            </div>
                                                            <div>
                                                                <label for=""><span class="text-red-500">*</span> Address</label>
                                                                <input x-model="formData.sec6.alterExecutorDetails[childIndex].address" type="text">
                                                            </div>
                                                            <button x-show="childIndex>0" @click="removeAlterExecutor(childIndex)" class="mt-2 bg-red-500 px-6 py-2 text-white hover:bg-red-600">
                                                                <span x-text="'DELETE ' + getNumFormat(childIndex+1) + ' ALTERNATE'"></span>
                                                            </button> 
                                                        </div>
                                                    </template>
                                                    <div x-show="formData.sec6.numAlterExecutor < 2">
                                                        <button @click="addAlterExecutor($event)" class="mt-2 bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                                <div x-show="activeForm === 'sec7'">
                                    <!-- Sub section intro of section 6 -->
                                    <div x-show="activeSubForm === 'intro'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Funeral and Burial Arrangements</h1>
                                        </div>
                                        <div>
                                            <p class="mb-2">
                                                I Hereby Direct that my body be prepared for burial in an appropriate manner and that my funeral expenses and any debts be paid out of my estate, along with the following:
                                            </p>
                                            <p class="mb-2">
                                                <span class="text-red-500">*</span> = required information
                                            </p>
                                        </div>
                                        <div>
                                            <div>
                                                <div class="row">
                                                    <div class="col-md-6 col-xl-4">
                                                        <div>
                                                            <label for="funeralOccur">That I be<span class="text-red-500">*</span></label>
                                                            <input type="text" name="funeralOccur" x-model="formData.sec7.funeralOccur" id="funeralOccur" class="mr-4" placeholder="Specify any specific details that you would like to occur at your funeral">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 col-xl-4">
                                                        <div>
                                                            <label for="funeralClothed">That be clothed in<span class="text-red-500">*</span></label>
                                                            <input type="text" name="funeralClothed" x-model="formData.sec7.funeralClothed" id="funeralClothed" class="mr-4" placeholder="Please specify color and type">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 col-xl-4">
                                                        <div>
                                                            <label for="firstName">That my remains be placed<span class="text-red-500">*</span></label>
                                                            <input type="text" name="funeralPlaced" x-model="formData.sec7.funeralPlaced" id="funeralPlaced" class="mr-4" placeholder="Please specify how and where you would like your remains to be placed">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 col-xl-12">
                                                        <div>
                                                            <label for="firstName">That the following songs be included in my funeral programme<span class="text-red-500">*</span></label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div>
                                                            <label for="funeralsongs1">songs 1<span class="text-red-500">*</span></label>
                                                            <input x-model="formData.sec7.funeralsongs1" type="text" id="funeralsongs1" placeholder="Please insert name of song">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div>
                                                            <label for="funeralsongs2">songs 2<span class="text-red-500">*</span></label>
                                                            <input x-model="formData.sec7.funeralsongs2" type="text" id="funeralsongs2" placeholder="Please insert name of song">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="activeForm === 'sec8'">
                                    <!-- Sub section intro of section 6 -->
                                    <div x-show="activeSubForm === 'intro'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Add Your Possession</h1>
                                        </div>
                                        <div>
                                            <div>
                                                <template x-for="(item,childIndex) in formData.sec8.positions">
                                                    <div class="mb-4">
                                                        <div>
                                                            <label for=""><span class="text-red-500">*</span> Select Possession</label>
                                                            <select x-model="formData.sec8.positions[childIndex].position">
                                                                <option>Select Possession</option>
                                                                <option value="Property">Property</option>
                                                                <option value="Shares and Stocks">Shares and Stocks</option>
                                                                <option value="Insurance">Insurance</option>
                                                                <option value="Bank Accounts">Bank Accounts</option>
                                                                <option value="Motor Vehicle">Motor Vehicle</option>
                                                                <option value="Unpaid Salary and Emoluments">Unpaid Salary and Emoluments</option>
                                                                <option value="National Housing Trust (NHT) Contributions">National Housing Trust (NHT) Contributions</option>
                                                                <option value="Jewellery">Jewellery</option>
                                                                <option value="Furniture">Furniture</option>
                                                                <option value="Paintings">Paintings</option>
                                                                <option value="Firearm">Firearm</option>
                                                                <option value="Residual Estate">Residual Estate</option>
                                                            </select>
                                                        </div> 

                                                         <div x-show="formData.sec8.positions[childIndex].position === 'Property'">
                                                            <!-- Property fields -->
                                                            <label for=""><span class="text-red-500">*</span> Address</label>
                                                            <input x-model="formData.sec8.positions[childIndex].address" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Parish</label>
                                                            <input x-model="formData.sec8.positions[childIndex].parish" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Registered at</label>
                                                            <input x-model="formData.sec8.positions[childIndex].registeredAt" type="text">
                                                        </div>

                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Shares and Stocks'">
                                                            <!-- Shares and Stocks fields -->
                                                            <label for=""><span class="text-red-500">*</span> Shares in</label>
                                                            <input x-model="formData.sec8.positions[childIndex].sharesIn" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Country</label>
                                                            <input x-model="formData.sec8.positions[childIndex].country" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Name of Investment Company or Stock Exchange</label>
                                                            <input x-model="formData.sec8.positions[childIndex].investmentCompany" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Account number</label>
                                                            <input x-model="formData.sec8.positions[childIndex].accountNumber" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Address</label>
                                                            <input x-model="formData.sec8.positions[childIndex].address" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Parish</label>
                                                            <input x-model="formData.sec8.positions[childIndex].parish" type="text">
                                                        </div>

                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Insurance'">
                                                            <!-- Insurance fields -->
                                                            <label for=""><span class="text-red-500">*</span> Proceeds of insurance policy numbered</label>
                                                            <input x-model="formData.sec8.positions[childIndex].policyNumber" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Name of Investment Company</label>
                                                            <input x-model="formData.sec8.positions[childIndex].investmentCompany" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Country</label>
                                                            <input x-model="formData.sec8.positions[childIndex].country" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Address</label>
                                                            <input x-model="formData.sec8.positions[childIndex].address" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Parish</label>
                                                            <input x-model="formData.sec8.positions[childIndex].parish" type="text">
                                                        </div>

                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Bank Accounts'">
                                                            <!-- Bank Accounts fields -->
                                                            <label for=""><span class="text-red-500">*</span> Accounts numbered</label>
                                                            <input x-model="formData.sec8.positions[childIndex].accountNumber" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Name of Financial Institution</label>
                                                            <input x-model="formData.sec8.positions[childIndex].financialInstitution" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Country</label>
                                                            <input x-model="formData.sec8.positions[childIndex].country" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Address</label>
                                                            <input x-model="formData.sec8.positions[childIndex].address" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Parish</label>
                                                            <input x-model="formData.sec8.positions[childIndex].parish" type="text">
                                                        </div>

                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Motor Vehicle'">
                                                            <!-- Motor Vehicle fields -->
                                                            <label for=""><span class="text-red-500">*</span> Color</label>
                                                            <input x-model="formData.sec8.positions[childIndex].color" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Make</label>
                                                            <input x-model="formData.sec8.positions[childIndex].make" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Model</label>
                                                            <input x-model="formData.sec8.positions[childIndex].model" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Licence number</label>
                                                            <input x-model="formData.sec8.positions[childIndex].licenceNumber" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Engine and chassis numbers</label>
                                                            <input x-model="formData.sec8.positions[childIndex].engineChassisNumbers" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Address</label>
                                                            <input x-model="formData.sec8.positions[childIndex].address" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Parish</label>
                                                            <input x-model="formData.sec8.positions[childIndex].parish" type="text">
                                                        </div>

                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Unpaid Salary and Emoluments'">
                                                            <!-- Unpaid Salary and Emoluments fields -->
                                                            <label for=""><span class="text-red-500">*</span> Name of Employer</label>
                                                            <input x-model="formData.sec8.positions[childIndex].employerName" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Address</label>
                                                            <input x-model="formData.sec8.positions[childIndex].address" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Parish</label>
                                                            <input x-model="formData.sec8.positions[childIndex].parish" type="text">
                                                        </div>

                                                        <div x-show="formData.sec8.positions[childIndex].position === 'National Housing Trust (NHT) Contributions'">
                                                            <!-- NHT Contributions fields -->
                                                            <label for=""><span class="text-red-500">*</span> Refund of National Housing Trust Contributions</label>
                                                            <input x-model="formData.sec8.positions[childIndex].refundDetails" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Address</label>
                                                            <input x-model="formData.sec8.positions[childIndex].address" type="text">

                                                            <label for=""><span class="text-red-500">*</span> Parish</label>
                                                            <input x-model="formData.sec8.positions[childIndex].parish" type="text">
                                                        </div>

                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Jewellery'">
                                                            <!-- Jewellery fields -->
                                                            <label for=""><span class="text-red-500">*</span> Description</label>
                                                            <textarea x-model="formData.sec8.positions[childIndex].description" rows="3"></textarea>
                                                        </div>

                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Furniture'">
                                                            <!-- Furniture fields -->
                                                            <label for=""><span class="text-red-500">*</span> Description</label>
                                                            <textarea x-model="formData.sec8.positions[childIndex].description" rows="3"></textarea>
                                                        </div>
                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Paintings'">
                                                            <!-- Paintings fields -->
                                                            <label for=""><span class="text-red-500">*</span> Description</label>
                                                            <textarea x-model="formData.sec8.positions[childIndex].description" rows="3"></textarea>
                                                        </div>
                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Firearm'">
                                                            <!-- Firearm fields -->
                                                            <label for=""><span class="text-red-500">*</span> Description</label>
                                                            <textarea x-model="formData.sec8.positions[childIndex].description" rows="3"></textarea>
                                                        </div>
                                                        <div x-show="formData.sec8.positions[childIndex].position === 'Residual Estate'">
                                                        <!-- Residual Estate fields -->
                                                        <label for=""><span class="text-red-500">*</span> Description</label>
                                                        <textarea x-model="formData.sec8.positions[childIndex].description" rows="3"></textarea>
                                                    </div>


                                                        <button @click="removePosition(childIndex)" class="mt-2 bg-red-500 px-6 py-2 text-white hover:bg-red-600">
                                                            <span x-text="'DELETE ' + getNumFormat(childIndex+1) + ' Possession'"></span>
                                                        </button> 
                                                    </div>
                                                </template>
                                                <div>
                                                    <button x-show="formData.sec6.numExecutor < 5" @click="addPosition($event)" class="mt-2 bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="activeForm === 'sec9'">
                                    <!-- Sub section intro of section 7 -->
                                    <div x-show="activeSubForm === 'intro'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Distribute Your Possessions</h1>
                                            <p class="italic font-bold">You are now ready to specify how you wish your possessions to be distributed.</p>
                                            <h2 class="mt-8 italic text-2xl text-blue-500">Remember:</h2>
                                        </div>
                                        <div>
                                            <ul class="list-disc pl-8">
                                                <li>
                                                    <p>To reduce the likelihood of your Will being contested in a court of law, be as complete and unambiguous in your answers as possible.</p>
                                                </li>
                                                <li>
                                                    <p>While answering the questions, if you need general assistance on the section, just read the Common Questions which appear on every page. If you don't see the questions, simply click on the big <span class="font-bold text-4xl">?</span> near the top of the page.</p>                                                    
                                                </li>
                                                <li>
                                                   <p>Specific help for parts of a page that may be unclear is available by tapping (or moving your mouse over) the small <span class="font-bold">?</span> symbol which appears next to some questions.</p> 
                                                </li>
                                                <li>
                                                    <p>You can come back at any time to revise your answers and keep your Will up to date, free of charge.</p>
                                                </li>
                                            </ul>
                                            <h2 class="mt-8 text-2xl text-blue-500">Click on the "NEXT" button below to continue.</h2>
                                        </div>
                                    </div>
                                    <!-- Sub section Bequests to Charities of section 7 -->
                                    <!-- <div x-show="activeSubForm === 'charities'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Bequests to Charities</h1>                                            
                                        </div>
                                        <div>
                                            <div class="mt-4">
                                                <p class="mb-2">
                                                Before we get started with distributing your possessions, have you given any thought to whether you would like to leave something to charity?
                                                </p>
                                                <p class="mb-2">
                                                Many people like to leave a gift to charity in their Will because they care about important causes. Do you want to make a gift to charity in your Will to support causes that have been important in your life? If you wish, you can use this page to leave a fixed sum, a specific item, or a percentage of your estate to one or more specific charities.
                                                </p>                                            
                                            </div>
                                            <div class="flex flex-col">
                                                <div>
                                                    <input type="radio" name="charitableDonation" x-model="formData.sec9.charitableDonation" id="charitableDonation1" value="1" class="mr-4"><label for="charitableDonation1">I do not want to specify a charitable donation in my Will.</label>
                                                </div>
                                                <div>
                                                    <input type="radio" name="charitableDonation" x-model="formData.sec9.charitableDonation" id="charitableDonation2" value="2" class="mr-4"><label for="charitableDonation2">I would like to include a charitable donation in my Will.</label>
                                                </div>
                                                <div>
                                                    <input type="radio" name="charitableDonation" x-model="formData.sec9.charitableDonation" id="charitableDonation3" value="3" class="mr-4"><label for="charitableDonation3">Undecided. Let me come back to this later.</label>
                                                </div>                                                                                                            
                                            </div>
                                            <div x-show="formData.sec9.charitableDonation==2">
                                                <div>
                                                    <template x-for="(item,childIndex) in formData.sec9.bequestDetails">
                                                        <div class="mt-10 flex">
                                                            <div class="w-2/12 p-4 border-2">
                                                                <p x-text="childIndex+1"></p>
                                                            </div>
                                                            <div class="w-10/12 p-4 border-2 border-l-0">
                                                                <p><span class="text-red-500">*</span> = required information</p>
                                                                <label :for="'bequestType'+childIndex">I would like to give</label>
                                                                <select x-model="formData.sec9.bequestDetails[childIndex].type" :id="'bequestType'+childIndex">                                                            
                                                                    <option value="1">Specific amount of money</option>
                                                                    <option value="2">Percentage of my estate</option>
                                                                    <option value="3">Specific asset (e.g. my car)</option>                                                            
                                                                </select>
                                                                <div x-show="formData.sec9.bequestDetails[childIndex].type==1">
                                                                    <label for=""><span class="text-red-500">*</span> Enter the amount</label>
                                                                    <input x-model="formData.sec9.bequestDetails[childIndex].amount" type="text">
                                                                </div>
                                                                <div x-show="formData.sec9.bequestDetails[childIndex].type==2">
                                                                    <label for=""><span class="text-red-500">*</span> Enter the percentage</label>
                                                                    <input x-model="formData.sec9.bequestDetails[childIndex].percentage" type="text">
                                                                </div>
                                                                <div x-show="formData.sec9.bequestDetails[childIndex].type==3">
                                                                    <label for=""><span class="text-red-500">*</span> Description</label>
                                                                    <textarea x-model="formData.sec9.bequestDetails[childIndex].asset" class="h-1/5 p-2"></textarea>                                                                    
                                                                </div>
                                                                <div>
                                                                    <label for=""><span class="text-red-500">*</span> To the charity</label>
                                                                    <input x-model="formData.sec9.bequestDetails[childIndex].charityName" type="text">
                                                                </div>
                                                                <button x-show="childIndex>0" @click="removeBequest(childIndex)" class="mt-2 bg-red-500 px-6 py-2 text-white hover:bg-red-600">
                                                                    DELETE
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <div x-show="formData.sec9.numBequests < 5">
                                                        <button @click="addBequest($event)" class="mt-2 bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <!-- Sub section Trusts for Pets of section 7 -->
                                  <!--   <div x-show="activeSubForm === 'petTrusts'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Trusts for Pets</h1>                                            
                                        </div>
                                        <div class="mt-4">
                                            <p class="mb-2">
                                            If you have pets, you may wish to create a Trust to be used for their care after you have passed away.
                                            </p>                                                                                    
                                        </div>
                                        <div class="flex flex-col">
                                            <div>
                                                <input type="radio" name="petTrust" x-model="formData.sec9.petTrust" id="petTrust1" value="1" class="mr-4"><label for="petTrust1">I do not want to create a Trust for the care of a pet.</label>
                                            </div>
                                            <div>
                                                <input type="radio" name="petTrust" x-model="formData.sec9.petTrust" id="petTrust2" value="2" class="mr-4"><label for="petTrust2">I would like to create a Trust for the care of a pet.</label>
                                            </div>                                            
                                        </div>
                                        <div x-show="formData.sec9.petTrust==2">
                                            <div>
                                                <template x-for="(item,childIndex) in formData.sec9.petDetails">
                                                    <div class="mt-10 flex">
                                                        <div class="w-2/12 p-4 border-2">
                                                            <p x-text="childIndex+1"></p>
                                                        </div>
                                                        <div class="w-10/12 p-4 border-2 border-l-0">
                                                            <p><span class="text-red-500">*</span> = required information</p>                                                            
                                                            <div class="mb-2">
                                                                <label for=""><span class="text-red-500">*</span> Name of Pet</label>
                                                                <input x-model="formData.sec9.petDetails[childIndex].name" type="text">
                                                                <p class="text-xs">Enter the name that is used to uniquely identify the pet, so that there is no confusion over which pet you are referring to.</p>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for=""><span class="text-red-500">*</span> Type of Pet</label>
                                                                <input x-model="formData.sec9.petDetails[childIndex].type" type="text">
                                                                <p class="text-xs">Be as specific as you can. For example, "Chocolate Labrador Retriever".</p>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for=""><span class="text-red-500">*</span> Amount in the Trust</label>
                                                                <input x-model="formData.sec9.petDetails[childIndex].amount" type="text">
                                                                <p class="text-xs">Enter the exact amount of money, including the currency. For example, "$1,000 United States dollars".</p>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for=""><span class="text-red-500">*</span> Caretaker</label>
                                                                <input x-model="formData.sec9.petDetails[childIndex].caretaker" type="text">
                                                                <p class="text-xs">Uniquely identify the person that you wish to care for this pet.</p>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label for=""><span class="text-red-500">*</span> Alternate Caretaker</label>
                                                                <input x-model="formData.sec9.petDetails[childIndex].alterCaretaker" type="text">
                                                                <p class="text-xs">This person would take on the role if your first choice was unable or unwilling to serve.</p>
                                                            </div>
                                                            <button x-show="childIndex>0" @click="removePet(childIndex)" class="mt-2 bg-red-500 px-6 py-2 text-white hover:bg-red-600">
                                                                DELETE
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div x-show="formData.sec9.numPets < 5">
                                                    <button @click="addPet($event)" class="mt-2 bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <!-- Sub section Distribute My Possessions of section 7 -->
                                   <!--  <div x-show="activeSubForm === 'possessionsDist'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Distribute My Possessions</h1>                                            
                                        </div>
                                        <div class="mt-2 flex flex-col">
                                      
                                            <div>
                                                <input type="radio" name="possessionDist" x-model="formData.sec9.possessionDist" id="possessionDist2" value="2" class="mr-4"><label for="possessionDist2">I would like to leave specific items to specific beneficiaries, and leave the rest to multiple beneficiaries.</label>
                                            </div>
                                                                                    
                                        </div>
                                    </div> -->
                                    <!-- Sub section Divide Possessions Between Multiple Beneficiaries of section 7 -->
                                    <div x-show="activeSubForm === 'divideEqual'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Divide Possessions Between Multiple Beneficiaries</h1>                                            
                                        </div>
                                        <div class="mt-4">
                                            <p x-show="formData.sec9.possessionDist==1" class="mb-2">
                                                I would like my possessions to be divided between my multiple beneficiaries in the following way:
                                            </p>
                                            <p x-show="formData.sec9.possessionDist==2" class="mb-2">
                                                I would like the rest of my possessions to be divided between my multiple beneficiaries in the following way:
                                            </p>
                                            <p><span class="text-red-500">*</span> = required information</p>
                                            <div class="mt-2 flex flex-col">
                                                <div>
                                                    <input type="radio" name="shareExp" x-model="formData.sec9.shareExp" id="shareExp1" value="1" class="mr-4"><label for="shareExp1">Express shares below as fractions (total must equal 1)</label>
                                                </div>
                                                <div>
                                                    <input type="radio" name="shareExp" x-model="formData.sec9.shareExp" id="shareExp2" value="2" class="mr-4"><label for="shareExp2">Express shares below as percentages (total must equal 100)</label>
                                                </div>                                                                                     
                                            </div>

                                            <div class="mt-6">
                                                <template x-for="(item,childIndex) in formData.sec4.beneficiaryDetails">
                                                    <div class="mt-10 flex">
                                                        <div class="w-2/12 p-4 border-2">
                                                            <p x-text="childIndex+1"></p>
                                                        </div>
                                                        <div class="w-6/12 p-4 border-2 border-l-0">
                                                            <p><span class="text-red-500">*</span> = required information</p>
                                                            <div>
                                                                <label for=""><span class="text-red-500">*</span> Full Name</label>
                                                                <input x-model="formData.sec4.beneficiaryDetails[childIndex].name" type="text">
                                                            </div>
                                                            <div>
                                                                <p class="form-label mt-2"><span class="text-red-500">*</span> Gender/Type:</p>
                                                                <div class="flex flex-col">
                                                                    <div>
                                                                        <input type="radio" :name="'genderBeneficiary2'+childIndex" id="genderBeneficiary11" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="1" class="mr-4"><label for="genderBeneficiary11">Male</label>
                                                                    </div>
                                                                    <div>
                                                                        <input type="radio" :name="'genderBeneficiary2'+childIndex" id="genderBeneficiary22" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="2" class="mr-4"><label for="genderBeneficiary22">Female</label>
                                                                    </div>
                                                                    <div>
                                                                        <input type="radio" :name="'genderBeneficiary2'+childIndex" id="genderBeneficiary33" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="3" class="mr-4"><label for="genderBeneficiary33">Neutral</label>
                                                                    </div>
                                                                    <div>
                                                                        <input type="radio" :name="'genderBeneficiary2'+childIndex" id="genderBeneficiary44" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="4" class="mr-4"><label for="genderBeneficiary44">Charity/Org</label>
                                                                    </div>
                                                                    <div>
                                                                        <input type="radio" :name="'genderBeneficiary2'+childIndex" id="genderBeneficiary55" x-model="formData.sec4.beneficiaryDetails[childIndex].gender" value="5" class="mr-4"><label for="genderBeneficiary55">Group</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-2">                                                                
                                                                <div class="mt-2">
                                                                    <label for=""><span class="text-red-500">*</span> Relationship</label>
                                                                    <input x-model="formData.sec4.beneficiaryDetails[childIndex].relation" type="text">
                                                                </div>
                                                                <div class="mt-2">
                                                                    <label for=""><span class="text-red-500">*</span> Address</label>
                                                                    <input x-model="formData.sec4.beneficiaryDetails[childIndex].address" type="text">
                                                                </div>
                                                            </div>
                                                            <button x-show="childIndex >= 1" @click="removeBeneficiary(childIndex)" class="bg-red-500 px-6 py-2 text-white hover:bg-red-600">Remove</button>
                                                        </div>
                                                        <div class="w-4/12 p-4 border-2 border-l-0">
                                                            <div x-show="formData.sec9.shareExp==1">
                                                                <div>
                                                                    Beneficiary's Share, expressed as a fraction (e.g. "1/3")
                                                                </div>
                                                                <div>
                                                                    <div class="mt-4">
                                                                        <label for=""><span class="text-red-500">*</span> Share</label>
                                                                        <input x-model="formData.sec4.beneficiaryDetails[childIndex].eqShare" type="text">                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div x-show="formData.sec9.shareExp==2">
                                                                <div>
                                                                    Beneficiary's Share, expressed as a percentage (e.g. "20.5")
                                                                </div>
                                                                <div>
                                                                    <div class="mt-4">
                                                                        <label for=""><span class="text-red-500">*</span> Share</label>
                                                                        <input x-model="formData.sec4.beneficiaryDetails[childIndex].eqShare" type="text">
                                                                        <p class="text-xs">Percent</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div class="mt-2">
                                                    <button x-show="formData.sec4.numBeneficiaries < 5" @click="addBeneficiary($event)" class="bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add Beneficiary</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Sub section Make Bequests of section 7 -->
                                    <div x-show="activeSubForm === 'makeBequests'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Make Bequests</h1>                                            
                                        </div>
                                        <div class="mt-4">
                                            <p class="mb-2">
                                            I would like to leave the following specific items to specific beneficiaries. Any of my possessions not specifically described here will go to my multiple main beneficiaries.
                                            </p>
                                        </div>
                                        <div class="p-4 border-2">
                                            <template x-for="(item,childIndex) in formData.sec9.specificsDetails">
                                                <div class="mt-10 flex">
                                                    <div class="w-2/12 p-4 border-2">
                                                        <p x-text="childIndex+1"></p>
                                                    </div>
                                                    <div class="w-10/12 p-4 border-2 border-l-0">
                                                        <p class="text-2xl mb-2">Add Bequest</p>

                                                        <div>      
                                                            <div>
                                                               
                                                                <div x-show="formData.sec9.specificsDetails[childIndex].type==1">
                                                                    <div class="mt-2">
                                                                        <label for=""><span class="text-red-500">*</span> To the beneficiary:</label>
                                                                        <select x-model="formData.sec9.specificsDetails[childIndex].giftBenefIndex">
                                                                            <template x-for="(beneficiary, benefIndex) in formData.sec3.childDetails" :key="benefIndex">

                                                                                <option :value="beneficiary.name" :selected="beneficiary.name == formData.sec9.specificsDetails[childIndex].giftBenefIndex" x-text="beneficiary.name"></option>
                                                                            </template>
                                                                            <!-- grand child opt -->
                                                                            <template x-for="(beneficiary, benefIndex) in formData.sec3.grandChildDetails" :key="benefIndex">

                                                                                <option :value="beneficiary.name" :selected="beneficiary.name == formData.sec9.grandChildDetails[childIndex].name" x-text="beneficiary.name"></option>
                                                                            </template>
                                                                            <!-- grand child opt -->
                                                                            <!-- beneficiary opt -->
                                                                            <template x-for="(beneficiary, benefIndex) in formData.sec4.beneficiaryDetails" :key="benefIndex">

                                                                                <option :value="beneficiary.name" :selected="beneficiary.name == formData.sec9.beneficiaryDetails[childIndex].name" x-text="beneficiary.name"></option>
                                                                            </template>
                                                                            <!-- beneficiary opt -->
                                                                        </select>
                                                                    </div>


                                                                    <div class="mt-2">
                                                                        <label for=""><span class="text-red-500">*</span> Add Position:</label>
                                                                        <select class="positionSelect" x-model="formData.sec9.specificsDetails[childIndex].positions">
                                                                            <template x-for="(position, posIndex) in formData.sec8.positions" :key="posIndex">
                                                                                <option :value="position.position" :selected="formData.sec9.specificsDetails[childIndex].positions === position.position" x-text="position.position + ' (' + (posIndex + 1) + 'st Position)'"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div x-show="formData.sec9.specificsDetails[childIndex].type==2">
                                                                    <p class="italic text-red-600 font-semibold">Provide a very specific and detailed description. Also, you must include the full names and addresses of all beneficiaries you include in your description.</p>
                                                                    <div class="mt-2">
                                                                        <label for=""><span class="text-red-500">*</span> Description:</label>
                                                                        <textarea class="h-1/5 p-2" x-model="formData.sec9.specificsDetails[childIndex].description"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                        
                                                        <button @click="removeSpecific(childIndex)" class="bg-red-500 mt-2 px-6 py-2 text-white hover:bg-red-600">Delete</button>
                                                    </div>                                                    
                                                </div>
                                            </template>
                                            <div x-show="formData.sec9.numSpecifics < 10">
                                                <button @click="addSpecific($event)" class="mt-2 bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Sub section Make Provisions For Multiple Beneficiaries of section 7 -->
                                    <div x-show="activeSubForm === 'multiBenefProvisions'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Make Provisions For Multiple Beneficiaries</h1>                                            
                                        </div>
                                        <template x-for="(item,childIndex) in formData.sec4.beneficiaryDetails">
                                            <div class="border-2 mt-4 p-4">
                                                <div>
                                                    <p>If my <span x-text="item.relation"></span>, <span x-text="item.name"></span>, does not survive me by thirty (30) days, then:</p>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="flex flex-col">
                                                        <!-- 'multiBenefProvisions' => [['radio'=1,'alterBenefIndex'=>-1,'shareDesc'=>'']] -->
                                                        <div class="mb-2">
                                                            <input type="radio" :name="'multiBen2'+childIndex" :id="'multiBen11'+childIndex" x-model="formData.sec9.multiBenefProvisions[childIndex].radio" value="1" class="mr-4"><label :for="'multiBen11'+childIndex">Divide his share equally between his own surviving children</label>
                                                        </div>
                                                        <div class="mb-2">
                                                            <input type="radio" :name="'multiBen2'+childIndex" :id="'multiBen22'+childIndex" x-model="formData.sec9.multiBenefProvisions[childIndex].radio" value="2" class="mr-4"><label :for="'multiBen22'+childIndex">Divide his share equally between my other surviving beneficiaries</label>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'multiBen2'+childIndex" :id="'multiBen33'+childIndex" x-model="formData.sec9.multiBenefProvisions[childIndex].radio" value="3" class="mr-4"><label :for="'multiBen33'+childIndex">Leave his share to the following alternate  beneficiary:</label>
                                                        </div>
                                                        <div class="mb-2" x-init="updateState()" class="mt-2 w-6/12">                                                                                                            
                                                            <label for=""><span class="text-red-500">*</span> Beneficiary:</label>
                                                            <select x-model="formData.sec9.multiBenefProvisions[childIndex].alterBenefIndex" >
                                                                <template x-for="(benf, benefIndex) in getAlterBenefs(childIndex)">
                                                                    <option :value="benf.value" :selected="benf.value == formData.sec9.multiBenefProvisions[childIndex].alterBenefIndex" x-text="benf.name"></option>
                                                                </template>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <input type="radio" :name="'multiBen2'+childIndex" :id="'multiBen44'+childIndex" x-model="formData.sec9.multiBenefProvisions[childIndex].radio" value="4" class="mr-4"><label :for="'multiBen44'+childIndex">Distribute his share in the following way:</label>
                                                        </div>
                                                        <div class="mb-2"><p class="italic text-red-600 font-semibold">Provide a very specific and detailed description. Also, you must include the full names and addresses of all beneficiaries you include in your description.</p></div>
                                                        <div class="mb-2">
                                                            <textarea class="h-1/5 p-2" x-model="formData.sec9.multiBenefProvisions[childIndex].shareDesc"></textarea>
                                                        </div>
                                                        <div class="mb-2">
                                                            <input type="radio" :name="'multiBen2'+childIndex" :id="'multiBen55'+childIndex" x-model="formData.sec9.multiBenefProvisions[childIndex].radio" value="5" class="mr-4"><label :for="'multiBen55'+childIndex">Undecided</label>
                                                        </div>
                                                    </div>                                                    
                                                </div>
                                            </div>
                                        </template>                                        

                                    </div>

                                    
                                    <!-- Sub section Name Alternate Beneficiary Intro of section 7 -->
                                    <div x-show="activeSubForm === 'alterBenefIntro'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Name Alternate Beneficiary</h1>                                            
                                        </div>
                                        <div x-show="formData.sec9.possessionDist==2" class="mt-4">
                                            <p class="mb-2">You have identified that your multiple main beneficiaries should receive all of your possessions, with the exception of the specific bequests you described earlier.</p>
                                            <p class="mb-2">In the event that none of your multiple main beneficiaries survive you by thirty (30) days, you now have an opportunity to specify how you would like to distribute everything that they would have received.</p>
                                        </div>
                                        <div x-show="formData.sec9.possessionDist==4" class="mt-4">
                                            <p class="mb-2">You have identified your main beneficiary (<span x-text="getBenefNameByIndex(formData.sec9.specificThingBenefIndex)"></span>) to receive all of your possessions, with the exception of the specific bequests you just described.</p>
                                            <p class="mb-2">In the event that your main beneficiary (<span x-text="getBenefNameByIndex(formData.sec9.specificThingBenefIndex)"></span>) does not survive you by thirty (30) days, you now have an opportunity to specify how you would like to distribute everything that would have gone to your main beneficiary (<span x-text="getBenefNameByIndex(formData.sec9.specificThingBenefIndex)"></span>).</p>
                                        </div>
                                    </div>


                                    <!-- Sub section Name Alternate Beneficiary of section 7 -->
                                    <div x-show="activeSubForm === 'alterBenef'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Name Alternate Beneficiary</h1>                                            
                                        </div>
                                        <div x-show="[1,2].includes(+formData.sec9.possessionDist)" class="mt-4">
                                            <p class="mb-2">If none of my multiple main beneficiaries survive me by thirty (30) days, then:</p>
                                        </div>
                                        <div x-show="[3,4].includes(+formData.sec9.possessionDist)" class="mt-4">
                                            <p class="mb-2">If my main beneficiary (<span x-text="getBenefNameByIndex(formData.sec9.specificThingBenefIndex)"></span>) does not survive me by thirty (30) days, then:</p>
                                        </div>
                                        <div class="mt-2">
                                        <div class="flex flex-col">
                                            <!-- 'alterBenefProvisions'=> [['radio'=>1,'everythingAlterBenefIndex'=>-1,'restAllBenefIndex'=>-1]], -->                                            
                                            <div x-show="[1,2].includes(+formData.sec9.possessionDist)" class="mb-2">
                                                <input type="radio" name="alterBen2" id="alterBen11" x-model="formData.sec9.alterBenefProvisions.radio" value="1" class="mr-4"><label for="alterBen11">For each of my multiple main beneficiaries, divide their individual share equally between their own children</label>
                                            </div>
                                            <div class="mb-2">
                                                <input type="radio" name="alterBen2" id="alterBen22" x-model="formData.sec9.alterBenefProvisions.radio" value="2" class="mr-4"><label for="alterBen22">Leave everything that my multiple main beneficiaries would have received to the following alternate beneficiary:</label>
                                                <div x-init="updateState()" class="w-6/12">                                                                                                            
                                                    <label for=""><span class="text-red-500">*</span> Beneficiary:</label>
                                                    <select x-model="formData.sec9.alterBenefProvisions.everythingAlterBenefIndex" >
                                                        <template x-for="(benf, benefIndex) in formData.sec4.beneficiaryDetails">
                                                            <option :value="benefIndex" :selected="benefIndex == formData.sec9.alterBenefProvisions.everythingAlterBenefIndex" x-text="benf.name"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <input type="radio" name="alterBen2" id="alterBen33" x-model="formData.sec9.alterBenefProvisions.radio" value="3" class="mr-4"><label for="alterBen33">I would like to leave specific items that my multiple main beneficiaries would have received to other specific beneficiaries, and leave the rest to the following alternate  beneficiary:</label>
                                                <div x-init="updateState()" class="w-6/12">                                                                                                            
                                                    <label for=""><span class="text-red-500">*</span> Beneficiary:</label>
                                                    <select x-model="formData.sec9.alterBenefProvisions.restAllBenefIndex" >
                                                        <template x-for="(benf, benefIndex) in formData.sec4.beneficiaryDetails">
                                                            <option :value="benefIndex" :selected="benefIndex == formData.sec9.alterBenefProvisions.restAllBenefIndex" x-text="benf.name"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                            <div>
                                                <input type="radio" name="alterBen2" id="alterBen44" x-model="formData.sec9.alterBenefProvisions.radio" value="4" class="mr-4"><label for="alterBen44">I would like to leave specific items that my multiple main beneficiaries would have received to other specific beneficiaries, and let me describe how to leave the rest</label>
                                            </div>
                                            <div>
                                                <input type="radio" name="alterBen2" id="alterBen55" x-model="formData.sec9.alterBenefProvisions.radio" value="5" class="mr-4"><label for="alterBen55">None of the above.  Let me describe how I would like to distribute everything that my multiple main beneficiaries would have received</label>
                                            </div>
                                            <div>
                                                <input type="radio" name="alterBen2" id="alterBen66" x-model="formData.sec9.alterBenefProvisions.radio" value="6" class="mr-4"><label for="alterBen66">Undecided</label>
                                            </div>                                            
                                        </div>                                            
                                        </div>
                                    </div>                                    

                                    <!-- Sub section Make An Alternative Plan of section 7 -->
                                    <div x-show="activeSubForm === 'makeAlterPlan'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Make An Alternative Plan</h1>                                            
                                        </div>
                                        <div class="mt-2">
                                            <p >You have decided to make an alternative plan, to come into effect if your multiple main beneficiaries do not survive you.</p>
                                        </div>
                                        <div class="mt-2">
                                            <p x-show="['3'].includes(formData.sec9.alterBenefProvisions.radio)">In particular, you have indicated that if your multiple main beneficiaries do not survive you, then you would like all of your possessions to go to <strong x-text="formData.sec4.beneficiaryDetails[formData.sec9.alterBenefProvisions.restAllBenefIndex].name"></strong>, with the exception of some specific items.</p>
                                            <p x-show="['4'].includes(formData.sec9.alterBenefProvisions.radio)">You will be able to distribute all of your possessions in a very specific way, but this will only be applicable if your multiple main beneficiaries do not survive you by thirty (30) days.</p>
                                        </div>
                                        <div class="mt-2">
                                            <p x-show="['3'].includes(formData.sec9.alterBenefProvisions.radio)">You now have an opportunity to specify those items and their corresponding beneficiaries. Take care when specifying these items to be as specific as possible. Also, keep in mind that this will only be applicable if your multiple main beneficiaries do not survive you by thirty (30) days.</p>
                                            <p x-show="['4'].includes(formData.sec9.alterBenefProvisions.radio)">Take care when creating this plan to be as specific as possible. At the end you will name the beneficiary who will inherit the remainder of your possessions (everything other than those items specified), if your multiple main beneficiaries do not survive you by thirty (30) days.</p>
                                        </div>
                                    </div>
                                    <!-- Sub section Make Bequests (Alternative Plan) of section 7 -->
                                    <div x-show="activeSubForm === 'makeAlterPlanBequests'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Make Bequests (Alternative Plan)</h1>                                            
                                        </div>
                                        <div class="mt-4">
                                            <p class="mb-2">In the event that my multiple main beneficiaries do not survive me by thirty (30) days, I would like to leave all of my possessions to <strong x-text="formData.sec4.beneficiaryDetails[formData.sec9.alterBenefProvisions.restAllBenefIndex].name"></strong>, with the exception of the following bequests:</p>
                                        </div>
                                        <!-- 'alterSpecificsDetails' => [['type'=>1,'gift'=>'','description'=>'','giftBenefIndex'=>-1,'alterGiftBenefIndex'=>-1]], -->
                                        <div class="p-4 border-2">
                                            <template x-for="(item,childIndex) in formData.sec9.alterSpecificsDetails">
                                                <div class="mt-10 flex">
                                                    <div class="w-2/12 p-4 border-2">
                                                        <p x-text="childIndex+1"></p>
                                                    </div>
                                                    <div class="w-10/12 p-4 border-2 border-l-0">
                                                        <p class="text-2xl mb-2">Add Bequest</p>
                                                        <p class="mb-2"><span class="text-red-500">*</span> = required information</p>
                                                        <p class="my-2">STEP 1 - Choose whether this bequest is a:</p>                                                        
                                                        <div>                                                            
                                                            <div class="flex flex-col">
                                                                <div>
                                                                    <input type="radio" :name="'specTypeAlter2'+childIndex" id="specTypeAlter11" x-model="formData.sec9.alterSpecificsDetails[childIndex].type" value="1" class="mr-4"><label for="specTypeAlter11">Simple gift (one item to one person)</label>
                                                                </div>
                                                                <div>
                                                                    <input type="radio" :name="'specTypeAlter2'+childIndex" id="specTypeAlter22" x-model="formData.sec9.alterSpecificsDetails[childIndex].type" value="2" class="mr-4"><label for="specTypeAlter22">Detailed description</label>
                                                                </div>                                                                
                                                            </div>
                                                            <div>
                                                                <p class="my-2">STEP 2 - Describe the bequest:</p>
                                                                <div x-show="formData.sec9.alterSpecificsDetails[childIndex].type==1">
                                                                    <div class="mt-2">
                                                                        <label for=""><span class="text-red-500">*</span> I would like to give:</label>
                                                                        <textarea class="h-1/5 p-2" x-model="formData.sec9.alterSpecificsDetails[childIndex].gift"></textarea>
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <label for=""><span class="text-red-500">*</span> To the beneficiary:</label>
                                                                        <select x-model="formData.sec9.alterSpecificsDetails[childIndex].giftBenefIndex" >
                                                                            <template x-for="(benf, benefIndex) in beneficiaryNames">
                                                                                <option :value="benf.value" :disabled="benf.value==-1" :selected="benf.value == formData.sec9.alterSpecificsDetails[childIndex].giftBenefIndex" x-text="benf.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        <label for="">Alternate  beneficiary:</label>
                                                                        <select x-model="formData.sec9.alterSpecificsDetails[childIndex].alterGiftBenefIndex">
                                                                            <template x-for="(benf, benefIndex) in beneficiaryNames">
                                                                                <option :value="benf.value" :selected="benf.value == formData.sec9.alterSpecificsDetails[childIndex].alterGiftBenefIndex" x-text="benf.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div x-show="formData.sec9.alterSpecificsDetails[childIndex].type==2">
                                                                    <p class="italic text-red-600 font-semibold">Provide a very specific and detailed description. Also, you must include the full names and addresses of all beneficiaries you include in your description.</p>
                                                                    <div class="mt-2">
                                                                        <label for=""><span class="text-red-500">*</span> Description:</label>
                                                                        <textarea class="h-1/5 p-2" x-model="formData.sec9.alterSpecificsDetails[childIndex].description"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>                                                        
                                                        <button x-show="formData.sec9.numAlterSpecifics > 1" @click="removeAlterSpecific(childIndex)" class="bg-red-500 mt-2 px-6 py-2 text-white hover:bg-red-600">Delete</button>
                                                    </div>                                                    
                                                </div>
                                            </template>
                                            <div x-show="formData.sec9.numAlterSpecifics < 10">
                                                <button @click="addAlterSpecific($event)" class="mt-2 bg-green-500 px-6 py-2 text-white hover:bg-green-600">Add</button>
                                            </div>
                                        </div>
                                        
                                    </div>

                                    <!-- Sub section Describe How To Distribute (Alternative Plan) of section 7 -->
                                    <div x-show="activeSubForm === 'describeAlterPlan'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Describe How To Distribute (Alternative Plan)</h1>                                            
                                        </div>
                                        <div class="mt-4">
                                            <p class="mb-2">In case your primary beneficiary cannot inherit your estate, this page allows you to describe the distribution of your estate. Everything that is left over, or cannot be delivered to a chosen beneficiary, is called the "Residual Estate" and your plans for this are described on the next page.</p>
                                            <p class="mb-2">If my multiple main beneficiaries do not survive me by thirty (30) days and my alternative plan comes into effect, I would like the following description to represent how I would like to distribute everything that my multiple main beneficiaries would have received:</p>
                                            <p class="italic text-red-600 font-semibold">Provide a very specific and detailed description. Also, you must include the full names and addresses of any beneficiaries you include in your description.</p>
                                            <div class="mb-2">                                            
                                                <textarea class="h-40 p-2" x-model="formData.sec9.describeAlterDesc"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Sub section Describe Specific Bequests (if any) of section 7 -->
                                    <div x-show="activeSubForm === 'descSpecificBequests'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Describe Specific Bequests (if any)</h1>                                            
                                        </div>
                                        <div class="mt-4">
                                            <p class="mb-2">If my multiple main beneficiaries do not survive me by thirty (30) days and my alternative plan comes into effect, I would like the following description to represent how I would like to distribute all of my possessions, with the exception of those items previously described.  This includes those items left to any beneficiaries who do not survive me by thirty (30) days, and for which there is no alternate  beneficiary who survives me by thirty (30) days.</p>
                                            <p class="mb-2">I would like to distribute specific bequests in the following way (OPTIONAL):</p>
                                            <p class="italic text-red-600 font-semibold">Provide a very specific and detailed description. Also, you must include the full names and addresses of any beneficiaries you include in your description.</p>
                                        </div>
                                        <div class="mt-2">
                                            <label for="">I would like to give:</label>
                                            <textarea class="h-40 p-2" x-model="formData.sec9.descSpecificBequest"></textarea>
                                        </div>
                                    </div>



                                    <!-- Sub section Residual Beneficiary (Alternative Plan) of section 7 -->
                                    <div x-show="activeSubForm === 'residualAlterPlan'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">Residual Beneficiary (Alternative Plan)</h1>                                            
                                        </div>
                                        <div class="mt-4">
                                            <p class="mb-2">If my multiple main beneficiaries do not survive me by thirty (30) days and my alternative plan comes into effect, I would like the following description to represent how I would like to distribute all of my possessions, with the exception of those items previously described.  This includes those items left to any beneficiaries who do not survive me by thirty (30) days, and for which there is no alternate  beneficiary who survives me by thirty (30) days.</p>
                                        </div>
                                        <div>
                                            <p class="italic text-red-600 font-semibold">Provide a very specific and detailed description. Also, you must include the full names and addresses of any beneficiaries you include in your description.</p>
                                        </div>
                                        <!-- 'residualAlterDetail' => ['residualDesc','residualBenefIndex'], -->
                                        <div class="mb-2">                                            
                                            <textarea class="h-40 p-2" x-model="formData.sec9.residualAlterDetail.residualDesc"></textarea>
                                        </div>
                                        <div>
                                            <p>If any of the beneficiaries identified above do not survive me by thirty (30) days, then I would like their share to go to the following alternate residual  beneficiary <span class="italic font-bold">(OPTIONAL):</span></p>
                                            <div x-init="updateState()" class="w-6/12">
                                                <label for="">Beneficiary:</label>
                                                <select x-model="formData.sec9.residualAlterDetail.residualBenefIndex" >
                                                    <template x-for="(benf, benefIndex) in formData.sec4.beneficiaryDetails">
                                                        <option :value="benefIndex" :selected="benefIndex == formData.sec9.residualAlterDetail.residualBenefIndex" x-text="benf.name"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    

                                    <!-- Sub section "Second Level" Alternate of section 7 -->
                                    <div x-show="activeSubForm === 'secondLevelAlterBenef'">
                                        <div>
                                            <h1 class="text-4xl text-blue-500">"Second Level" Alternate</h1>                                            
                                        </div>
                                        <div class="mt-2">
                                            <p x-show="formData.sec9.alterBenefProvisions.radio==2">In the event that neither my multiple main beneficiaries nor my alternate beneficiary (<span x-text="formData.sec4.beneficiaryDetails[formData.sec9.alterBenefProvisions.everythingAlterBenefIndex].name"></span>) survive me by thirty (30) days, then: </p>
                                        </div>
                                        <div class="mt-2">
                                            <div class="flex flex-col">
                                                <!-- 'secondLevelAlter' => ['radio'=>1,'alterBenefIndex'=>0,'everythingDesc'=>''], -->
                                                <div class="mb-2">
                                                    <input type="radio" name="secondLevelAlterBen2" id="secondLevelAlterBen11" x-model="formData.sec9.secondLevelAlter.radio" value="1" class="mr-4"><label for="secondLevelAlterBen11">I would like everything that they would have received to go to the following "second level" alternate  beneficiary:</label>
                                                    <div>
                                                        <select x-model="formData.sec9.secondLevelAlter.alterBenefIndex" >
                                                            <template x-for="(benf, benefIndex) in formData.sec4.beneficiaryDetails">
                                                                <option :value="benefIndex" :selected="benefIndex == formData.sec9.secondLevelAlter.alterBenefIndex" x-text="benf.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <input type="radio" name="secondLevelAlterBen2" id="secondLevelAlterBen22" x-model="formData.sec9.secondLevelAlter.radio" value="2" class="mr-4"><label for="secondLevelAlterBen22">Distribute everything that they would have received in the following way:</label>
                                                    <div>
                                                        <p class="italic text-red-600 font-semibold">Provide a very specific and detailed description. Also, you must include the full names and addresses of any beneficiaries you include in your description.</p>
                                                        <textarea class="h-1/5 p-2" x-model="formData.sec9.secondLevelAlter.everythingDesc"></textarea>
                                                    </div>
                                                </div>
                                                <div class="mb-2">
                                                    <input type="radio" name="secondLevelAlterBen2" id="secondLevelAlterBen33" x-model="formData.sec9.secondLevelAlter.radio" value="3" class="mr-4"><label for="secondLevelAlterBen33">None of the above. I do not want to specify a "second level" alternate  beneficiary.</label>                                                    
                                                </div>
                                                <div class="mb-2">
                                                    <input type="radio" name="secondLevelAlterBen2" id="secondLevelAlterBen44" x-model="formData.sec9.secondLevelAlter.radio" value="4" class="mr-4"><label for="secondLevelAlterBen44">Undecided</label>                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub section Trusts for Young Beneficiaries of section 7 -->                                    
                                
                                <div x-show="activeForm === 'sec10'">
                                    <div class="mb-2">
                                        <h1 class="text-4xl text-blue-500">Forgive Debts</h1>                                            
                                    </div>
                                    <div class="mb-2">
                                        <p class="mb-2">Is there anything specific owed to you that you wish to forgive or cancel at the time of your death?</p>
                                        <p class="mb-2">If you have debts owed to you that you do not wish to forgive, they become part of your estate. After you complete your Will, you should include these in the "Personal Details & Assets" form located on the MyWill™ Main Menu.</p>
                                    </div>
                                    <div class="mb-2">
                                        <!-- 'sec10' => ['forgive','forgiveDetails'], -->
                                        <div class="flex flex-col">                                                            
                                        <div class="mb-2">
                                            <input type="radio" name="forgive1" id="forgive11" x-model="formData.sec10.forgive" value="1" class="mr-4"><label for="forgive11">No</label>                                                                
                                        </div>
                                        <div class="mb-2">                                                                
                                            <input type="radio" name="forgive1" id="forgive12" x-model="formData.sec10.forgive" value="2" class="mr-4"><label for="forgive12">Yes, and the details are as follows:</label>
                                        </div>
                                        <div x-show="formData.sec10.forgive==2">
                                            <label for="forgiveDetails">Description</label>
                                            <textarea class="h-60 p-2 w-6/12 block" id="forgiveDetails" x-model="formData.sec10.forgiveDetails" placeholder="Enter detailed description"></textarea>
                                        </div>
                                        <div class="mb-2">                                                                
                                            <input type="radio" name="forgive1" id="forgive13" x-model="formData.sec10.forgive" value="3" class="mr-4"><label for="forgive13">Undecided</label>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                                <div x-show="activeForm === 'sec11'">
                                    <div class="mb-2">
                                        <h1 class="text-4xl text-blue-500">Attachments</h1>                                            
                                    </div>
                                    <div class="mb-2">
                                        <p class="mb-2">Check this box if you plan to store additional information with your Will, such as a document or letter providing additional instructions or other information.</p>                                        
                                    </div>
                                    <div class="mb-2 bg-slate-50 p-8 shadow">

                                        <div class="mb-2">
                                            <p class=" text-blue-500"><b>View the Will</b></p>                
                                        </div>
                                        
                                        
                                        <?php
                                        echo do_shortcode('[membership level="3" ]
                                                <p>Once you click the download button, your membership will be expired and you no longer will be able to edit this will.</p>
                                            [/membership]');
                                            
                                            echo '<div class="mb-2 bg-slate-50 p-8 shadow">
                                                <button type="submit" class="mt-0 bg-green-500 px-6 py-2 text-white hover:bg-green-600" id="download-will-one-time" data-user-id="'.$user_id.'">View And Download Pdf</button>';
                                                
                                        ?>

                                            <?php
                                            echo do_shortcode('[membership level="2" ]
                                                    <a href="'.home_url('/sign-view-will').'" target="_blank" class="mt-0 bg-green-500 px-6 py-2 text-white hover:bg-green-600 sign-pdf_view premium">View And Sign Pdf</a>
                                                [/membership]');
                                               
                                            ?>
                                            
                                        </div>
                                        <br> 
                                        <br>
                                        <?php
                                        $premium_content = '';
                                        $premium_content .= '<div class="mb-2 premium">
                                            <p class="text-blue-500"><b>Do you want to send zoom link to the executors and sign in front of them</b></p>
                                            <p>If yes then click on this button <a class="btn-primary btn mt-0 bg-green-500 px-6 py-2 text-white hover:#2073d9" target="_blank" href="https://zoom.us/">zoom</a> and paste the link on the below box</p>              
                                        </div>
                                        <div class="mb-2 premium">
                                            <p class="text-blue-500"><b>Send Meet Link</b></p>                
                                        </div>
                                        <div class="mb-2 bg-slate-50 p-8 shadow premium">
                                            <div style="display:flex;">
                                                <input type="text" id="meet_link" placeholder="Enter Meet Link" x-model="formData.sec11.meet_link">
                                                <button @click="sendMeetLink($event)" class="mt-0 bg-green-500 px-6 py-2 text-white hover:bg-green-600">Send</button>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="premium">
                                            <input class="mr-4" :checked="formData.sec11.attachement == \'true\'" x-model="formData.sec11.attachement" type="checkbox" id="attachements">
                                            <label for="attachements">I am going to attach an instruction to my Will</label>
                                        </div>
                                        <div class="premium">
                                            <p>If you check this box, a clause will be inserted into your Will that references the existence of the document.</p>
                                        </div>';
                                        echo do_shortcode('[membership level="2"]
                                            '.$premium_content.'
                                        [/membership]');
                                        
                                        ?> 
                                        <br>
                                        <div x-show="formData.sec11.attachement" class="premium">
                                            <div class="mb-2 bg-slate-50 p-8 shadow">
                                                <input type="text" class="hidden" id="sign-url" x-model="formData.sec11.attachement_url">
                                                <label for="sign-url">Upload Signed PDF</label>
                                                <form id="signForm" class="form" method="post" action="" enctype="multipart/form-data">                 
                                                    <div>
                                                        <div class="shrink-0">
                                                            <?php
                                                            $src = $will_data['sec11']['attachement_url'] == '/wp-content/uploads/2024/04/sign-placeholder.png' ? :$will_data['sec11']['attachement_url'];
                                                            $file_extension = pathinfo($src, PATHINFO_EXTENSION);
                                                            $is_pdf = $file_extension === 'pdf';
                                                            $file_name = basename($src); // Get the file name with extension
                                                            ?>

                                                            <?php if ($is_pdf){ ?>
                                                                <a href="<?= htmlspecialchars($src, ENT_QUOTES, 'UTF-8'); ?>" class="pdf-link">
                                                                    <i class="fas fa-file-pdf"></i> <?= htmlspecialchars($file_name, ENT_QUOTES, 'UTF-8'); ?>
                                                                </a>
                                                            <?php } ?> 
                                                        </div>
                                                        <label class="block">
                                                            <span class="sr-only">Choose signature file</span>
                                                            <input id="signature-file" name="sign_file" type="file" accept="image/*" class="block w-full text-sm text-slate-500
                                                            file:mr-4 file:py-2 file:px-4
                                                            file:rounded-full file:border-0
                                                            file:text-sm file:font-semibold
                                                            file:bg-violet-50 file:text-blue-500
                                                            hover:file:bg-violet-100
                                                            cursor-pointer
                                                            "/>                                                    
                                                        </label>
                                                        <?php wp_nonce_field( 'signuploadnonce', 'signnonce' );?>
                                                    </div>                                            
                                                </form>                                        
                                            </div>

                                            <div class="mb-2 bg-slate-50 p-8 shadow">
                                                <input type="text" class="hidden" id="video_url" x-model="formData.sec11.video_url">
                                                <label for="video_url">Upload Recorded Video</label>
                                                <form id="videoForm" class="form" method="post" action="" enctype="multipart/form-data">                                            
                                                    <div>
                                                        <div class="shrink-0">
                                                            <?php
                                                            $src = $will_data['sec11']['video_url'] == '/wp-content/uploads/2024/04/sign-placeholder.png' ? :$will_data['sec11']['video_url'];
                                                            ?>

                                                             
                                                            <video id="uploaded-video" width="200" controls style="<?php echo (!empty($src))? '' :'display:none;'; ?>">
                                                                <source id="video-source" src="<?php echo $src; ?>" type="video/mp4">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                             
                                                        </div>
                                                        <label class="block">
                                                            <span class="sr-only">Choose Video file</span>
                                                            <input id="video-file" name="video_file" type="file" class="block w-full text-sm text-slate-500
                                                            file:mr-4 file:py-2 file:px-4
                                                            file:rounded-full file:border-0
                                                            file:text-sm file:font-semibold
                                                            file:bg-violet-50 file:text-blue-500
                                                            hover:file:bg-violet-100
                                                            cursor-pointer
                                                            " accept="video/*"/>                                                    
                                                        </label>
                                                        <?php wp_nonce_field( 'videouploadnonce', 'videononce' );?>
                                                    </div>                                            
                                                </form>                                        
                                            </div>
                                        </div>
                                          
                                     
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <!-- code section for sending approval -->
               
                   
               
                <?php 
                
                
                ?>                                              
                <!-- code section for sending approval -->
                

                <div class="flex justify-center gap-2 mt-4 action-section" style="position:relative;">
                    <button class="uppercase bg-gray-300 px-4 py-2 rounded backPage-btn" @click="backPage">Back</button>
                    <?php
                    echo do_shortcode('[membership level="2"]
                        <button :class="{\'bg-gray-300\': allPages.indexOf(activeForm) !== 10, \'bg-green-500\': allPages.indexOf(activeForm) == 10}" class="uppercase bg-gray-300 px-4 py-2 text-white rounded save-btn" @click="save" x-show="allPages.indexOf(activeForm) == 10" >Finish</button>
                    [/membership]');

                    ?>
                    

                    <button class="uppercase bg-gray-300 px-4 py-2 rounded save-btn" @click="save" x-show="allPages.indexOf(activeForm) !== 10">Save/Exit</button>
                    <button :class="{'bg-green-500': allPages.indexOf(activeForm) === 10, 'bg-gray-300': allPages.indexOf(activeForm) !== 10}" class="uppercase bg-gray-300 px-4 py-2 rounded nextPage-btn" @click="nextPage" x-show="allPages.indexOf(activeForm) !== 10">Save/Next</button>

                </div>
            </div>

        </div>
    </div>   
 
    <script> 
        populateCountries("country");

        const ajaxurl = "<?= admin_url('admin-ajax.php')?>"
        var canCreate = true
        function updateNumViews() {
            url = "<?= admin_url('admin-ajax.php'); ?>"
            jQuery.ajax({
                type : "post",                        
                url : url,
                data : {action: "updateNumViews"},
                success: function(response) {
                    if(response.success) {
                        canCreate = true
                    }
                    else if(response.success==false) {
                        canCreate = false
                        alert("Cannot create or modify will more than 5 times.");                         
                    }
                }
            })
        }
        document.addEventListener('alpine:init', () => {
            Alpine.data('data', () => ({
                child: 0,
                activeForm: 'home',
                progressValue:1,
                activeSubForm: 'intro',
                checkSubSec: ['sec3','sec6','sec9'],
                allPages: [
                    'sec1',
                    'sec2',
                    'sec3',
                    'sec4',
                    'sec5',
                    'sec6',
                    'sec7',
                    'sec8',
                    'sec9',
                    'sec10',
                    'sec11',
                ],
                allSubPages: {
                    'sec3': ['intro', 'partner', 'children', 'grandChildren', 'deceased'],
                    'sec6': ['intro'],
                    'sec9': ['intro','makeBequests','divideEqual','multiBenefProvisions','alterBenefIntro','alterBenef','makeAlterPlan','makeAlterPlanBequests','describeAlterPlan','descSpecificBequests','residualAlterPlan','secondLevelAlterBenef'],
                },
                mainForm:false,
                selectedOpt: 1,
                beneficiaryNames: [],
                formData : willsFormData,

                qna: {
                    home:{},createMod:{},
                    sec1: {
                        'What if I have a question that isn\'t answered anywhere on the page?': 'No problem. Simply click on the \"CONTACT US\" link that appears in the menu at the top of each page. We\'d be happy to answer any questions that you have.\nBe sure to save your work by clicking the \"SAVE/EXIT\" button and creating an account, so that you can come back later to continue from where you left off.',
                        'How do I get started?': 'It\'s easy! Just click the \"NEXT\" button at the bottom of the page.',                        
                    },
                    sec2: {
                        'Question 1': 'Answer 1',
                        'Question 2': 'Answer 2',
                        'Question 3': 'Answer 3',
                        'Question 4': 'Answer 4',
                        'Question 5': 'Answer 5',
                    },
                    sec3: {
                        'Question 1': 'Answer 1',
                        'Question 2': 'Answer 2',
                        'Question 3': 'Answer 3',
                        'Question 4': 'Answer 4',
                        'Question 5': 'Answer 5',
                    },
                    sec4: {
                        'Question 1': 'Answer 1',
                        'Question 2': 'Answer 2',
                        'Question 3': 'Answer 3',
                        'Question 4': 'Answer 4',
                        'Question 5': 'Answer 5',
                    },
                    sec5: {
                        'Question 1': 'Answer 1',
                        'Question 2': 'Answer 2',
                        'Question 3': 'Answer 3',
                        'Question 4': 'Answer 4',
                        'Question 5': 'Answer 5',
                    },
                    sec6: {
                        'Question 1': 'Answer 1',
                        'Question 2': 'Answer 2',
                        'Question 3': 'Answer 3',
                        'Question 4': 'Answer 4',
                        'Question 5': 'Answer 5',
                    },
                    sec7: {
                        'That I be': 'Answer 1',
                        'That be clothed in': 'Answer 2',
                        'That my remains be placed': 'Answer 3',
                        'That the following songs be included in my funeral programme': 'Answer 4',
                    },
                    sec8: {
                        'Question 1': 'Answer 1',
                        'Question 2': 'Answer 2',
                        'Question 3': 'Answer 3',
                        'Question 4': 'Answer 4',
                        'Question 5': 'Answer 5',
                    },
                    sec9: {
                        'Question 1': 'Answer 1',
                        'Question 2': 'Answer 2',
                        'Question 3': 'Answer 3',
                        'Question 4': 'Answer 4',
                        'Question 5': 'Answer 5',
                    },
                    sec10: {
                        'Question 1': 'Answer 1',
                        'Question 2': 'Answer 2',
                        'Question 3': 'Answer 3',
                        'Question 4': 'Answer 4',
                        'Question 5': 'Answer 5',
                    },
                    sec11: {
                        'Question 1': 'Answer 1',
                        'Question 2': 'Answer 2',
                        'Question 3': 'Answer 3',
                        'Question 4': 'Answer 4',
                        'Question 5': 'Answer 5',
                    },
                },

                sectionSelOption: [
                    'Section 1: Introduction',
                    'Section 2: Personal Details',
                    'Section 3: Family Status',
                    'Section 4: Other Beneficiaries',
                    'Section 5: Guardians for Minor Children',
                    'Section 6: Executor',
                    'Section 7: Funeral and Burial Arrangements',
                    'Section 8: Add Your Possession',
                    'Section 9: Distribute your Possessions',
                    'Section 10: Forgive Debts',
                    'Section 11: Next Step',
                ],

                validateRules: {
                    sec2: {
                        prefix: {
                            max: 20,
                            min: 2,
                            required: true,
                        },
                        suffix: {
                            max: 20,
                            required: false,
                        },
                        firstName: {
                            max: 20,
                            min: 2,
                            required: true,
                        },
                        middleName: {
                            max: 20,
                            required: false,
                        },
                        lastName: {
                            max: 20,
                            min: 2,
                            required: true,
                        },
                        email: {
                            max: 50,
                            min: 2,
                            email: true,
                            required: true,
                        },
                        gender: {
                            required: true,
                        },
                        country: {
                            required: true,
                        },
                        address1: {
                            required: true,
                        },
                        parish: {
                            required: true,
                        },
                        occupation: {
                            required: true,
                        },
                    },
                    sec3: {
                        intro: {
                            status: {
                                required: true,
                            },
                            children: {
                                required: true,
                            },
                            grandChildren: {
                                required: true,
                            },
                        },

                        partner: {
                            fullName: {
                                required: true,
                                max: 50,
                                min: 2,
                            },
                            relation: {
                                required: true,
                            },
                            partnerGender: {
                                required: true,
                            }
                        },
                        children: {

                        },
                        grandChildren: {

                        },
                        deceased: {
                            
                        },
                    },
                    sec6: {
                        intro: {

                        },
                    },
                    sec7: {
                    },
                    sec8: {
                    },
                    sec9: {
                        intro: {

                        },
                        divideEqual:{

                        },
                        makeBequests:{

                        },
                        multiBenefProvisions: {

                        },
                        alterBenef:{

                        },
                        youngBenef:{

                        },
                        secondLevelAlterBenef:{

                        }
                    }
                },
                getExpiryAges(){
                    let ages = []
                    for(let index=0;index<17;index++){
                        if(index==0) ages.push('(Select Age)')
                        else ages.push(index+18)
                    }
                    return ages
                },
                addChild(e){
                    this.formData.sec3.numChildren++
                    this.formData.sec3.childDetails.push({'name':'','relation':'','dob':'','email':'','occupation':'','address':'','parish':''})
                },
                removeChild(index) {
                    this.formData.sec3.childDetails.splice(index, 1);
                    this.formData.sec3.numChildren--
                },
                addGrandChild(e){
                    this.formData.sec3.numGrandChildren++
                    this.formData.sec3.grandChildDetails.push({'name':'','relation':'','dob':'','email':'','occupation':'','address':'','parish':''})
                },
                removeGrandChild(index) {
                    this.formData.sec3.grandChildDetails.splice(index, 1);
                    this.formData.sec3.numGrandChildren--
                },
                addDeceased(e){
                    this.formData.sec3.numDeceased++
                    this.formData.sec3.deceasedDetails.push({'name':'','gender':'','relation':''})
                },
                removeDeceased(index) {
                    this.formData.sec3.deceasedDetails.splice(index, 1);
                    this.formData.sec3.numDeceased--
                },
                addBeneficiary(e){
                    this.formData.sec4.numBeneficiary++
                    this.formData.sec4.beneficiaryDetails.push({'gender':'','name':'','relation':'','address':''})
                },
                removeBeneficiary(index) {
                    this.formData.sec4.beneficiaryDetails.splice(index, 1);
                    this.formData.sec4.numBeneficiary--
                },
                addExecutor(e){
                    this.formData.sec6.numExecutor++
                    this.formData.sec6.executorDetails.push({'name':'','relation':'','address':''})
                },
                removeExecutor(index) {
                    this.formData.sec6.executorDetails.splice(index, 1);
                    this.formData.sec6.numExecutor--
                },
                addPosition(e){
                    this.formData.sec8.numExecutor++
                    this.formData.sec8.positions.push({position: 'Property'})
                },
                removePosition(index) {
                    this.formData.sec8.positions.splice(index, 1);
                    this.formData.sec8.numExecutor--
                },
                addAlterExecutor(e){
                    this.formData.sec6.numAlterExecutor++
                    this.formData.sec6.alterExecutorDetails.push({'name':'','relation':'','address':''})
                },
                removeAlterExecutor(index) {
                    this.formData.sec6.alterExecutorDetails.splice(index, 1);
                    this.formData.sec6.numAlterExecutor--
                },
                addBequest(e){
                    this.formData.sec9.numBequests++
                    this.formData.sec9.bequestDetails.push({"type":1,"amount":"","percentage":"","asset":"","charityName":""})
                },
                removeBequest(index) {
                    this.formData.sec9.bequestDetails.splice(index, 1);
                    this.formData.sec9.numBequests--
                },                
                addPet(e){
                    this.formData.sec9.numPets++
                    this.formData.sec9.petDetails.push({"name":"","type":"","amount":"","caretaker":"","alterCaretaker":""})
                },
                removePet(index) {
                    this.formData.sec9.petDetails.splice(index, 1);
                    this.formData.sec9.numPets--
                },
                addSpecific(e){
                    this.formData.sec9.numSpecifics++
                    this.formData.sec9.specificsDetails.push({"type":1,"giftBenefIndex":0,"positions":0})
                },
                removeSpecific(index) {
                    this.formData.sec9.specificsDetails.splice(index, 1);
                    this.formData.sec9.numSpecifics--
                },
                addAlterSpecific(e){
                    this.formData.sec9.numAlterSpecifics++
                    this.formData.sec9.alterSpecificsDetails.push({"type":1,"gift":"","description":"","giftBenefIndex":0,"alterGiftBenefIndex":0})
                },
                removeAlterSpecific(index) {
                    this.formData.sec9.alterSpecificsDetails.splice(index, 1);
                    this.formData.sec9.numAlterSpecifics--
                },
                getNumFormat(number){
                    switch (number % 10) {
                        case 1:
                            return number + 'st';
                        case 2:
                            return number + 'nd';
                        case 3:
                            return number + 'rd';
                        default:
                            return number + 'th';
                    }
                },
                getBenefNameByIndex(index){
                    try{
                        return this.formData.sec4.beneficiaryDetails[index].name
                    }catch (err){
                        return 'None Selected'
                    }
                },
                getNumExecutors(){
                    return this.formData.sec6.executorDetails.length
                },
                getExecutorNames(){
                    let names = ''
                    if(this.formData.sec6.executorDetails.length === 1) return this.formData.sec6.executorDetails[0].name
                    for(let i=0;i<this.formData.sec6.executorDetails.length;i++){
                        if(i!==this.formData.sec6.executorDetails.length-1)
                        names += this.formData.sec6.executorDetails[i].name+', '
                        else if(i===this.formData.sec6.executorDetails.length-1)
                        names += 'and ' + this.formData.sec6.executorDetails[i].name
                        else
                        names += this.formData.sec6.executorDetails[i].name
                    }
                    return names
                },
                getAlterBenefs(discardIndex){
                    let benefs = this.formData.sec4.beneficiaryDetails
                    let alterBenefs = []
                    benefs.forEach((item,index)=>{
                        if(index!==discardIndex) alterBenefs.push({'value':index,'name':item.name})
                    })
                    alterBenefs.unshift({'value':-1,'name':'[make selection]'})
                    return alterBenefs
                },
                isYoungInfoRequrired(){
                    let temp = [1,2,3,4].includes(+(this.formData.sec9.possessionDist))
                    let temp2 = [1].includes(+(this.formData.sec9.alterBenefProvisions.radio))
                    if(temp && temp2)
                        return true
                    return false
                },
                getYoungBenfificiaries(){
                    let benefsDetails = this.formData.sec4.beneficiaryDetails
                    let length = this.formData.sec9.youngBenefs.length
                    
                    if(length<benefsDetails.length){
                        let youngBenefs = this.formData.sec9.youngBenefs
                        for(let i=0; i<benefsDetails.length-length;i++){
                            youngBenefs.push({"trust":1,"expiryAge":-1,"shareType":1,"fraction":"","ageGranted":-1,"fractionRemainder":"","atThisAge":-1})
                        }
                        this.formData.sec9.youngBenefs = youngBenefs
                        return youngBenefs
                    }else {
                        
                        return this.formData.sec9.youngBenefs
                    }
                },

                sendMeetLink(e){
                    var meet_link = document.getElementById('meet_link').value
                    var executor_date = this.formData.sec6.executorDetails
                    var validExecutors = [];

                    executor_date.forEach(executor => {
                        if (executor.hasOwnProperty('email')) {
                            if (!this.isValidEmail(executor.email)) {
                                alert('Email is invalid:', executor.email);
                                return false;
                            }else{
                                validExecutors.push({
                                    name: executor.name,
                                    email: executor.email
                                });
                            }
                        }
                    });
                
                    var data = new FormData()
                    data.append('meet_link',meet_link)
                    data.append('executor_data',JSON.stringify(validExecutors, null, 2))
                    data.append('action','send_meet_link')
                    $ = jQuery
                    $.ajax({
                        type: "POST",
                        data: data,
                        dataType: "json",
                        url: ajaxurl,
                        cache: false,
                        processData: false,
                        contentType: false,
                        success: function(response) {                                   
                            if(response.success){
                                alert(response.data.msg);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown){                    
                            alert('Something went wrong!')
                        }
                    });
                     
                },


                sendWill(e){
                    var email = document.getElementById('send-will').value
                    var will_name = this.formData.sec2.firstName
                    if(this.isValidEmail(email)){
                        var data = new FormData()
                        data.append('email',email)
                        data.append('will_name',will_name)
                        data.append('action','send_will_email')
                        $ = jQuery
                        $.ajax({
                            type: "POST",
                            data: data,
                            dataType: "json",
                            url: ajaxurl,
                            cache: false,
                            processData: false,
                            contentType: false,
                            success: function(response) {                                                        
                                if(response.success);
                                    console.log('Please check Inbox')
                            },
                            error: function(jqXHR, textStatus, errorThrown){                    
                                alert('Something went wrong!')
                            }
                        });
                    }else {
                        alert('Enter a valid email address!')
                    }
                },

                validateError: {},
                
                validate(inputName) {
                    let rules;
                    if (this.checkSubSec.includes(this.activeForm)) {
                        rules = this.validateRules[this.activeForm][this.activeSubForm][inputName];
                        // 

                    } else {
                        rules = this.validateRules[this.activeForm][inputName];
                    }
                    // 
                    const value = this.formData[this.activeForm][inputName].trim();

                    this.validateError[inputName] = '';

                    if (rules.required && !value) {
                        this.validateError[inputName] = 'This field is required.';
                        return;
                    }
                    if (rules.email && !this.isValidEmail(value)) {
                        this.validateError[inputName] = 'Invalid email address.';
                        return;
                    }
                    if (rules.max && value.length > rules.max) {
                        this.validateError[inputName] = `Maximum ${rules.max} characters allowed.`;
                        return;
                    }

                    if (rules.min && value.length < rules.min) {
                        this.validateError[inputName] = `Minimum ${rules.min} characters required.`;
                        return;
                    }
                },

                validateOnSubmit() {
                    if (this.checkSubSec.includes(this.activeForm)) {
                        for (const inputName in this.validateRules[this.activeForm][this.activeSubForm]) {                        
                            this.validate(inputName);
                        }
                    } else {
                        for (const inputName in this.validateRules[this.activeForm]) {
                            this.validate(inputName);
                        }
                    }
                    for (const inputName in this.validateError) {
                        if (this.validateError[inputName]) {
                            return false;
                        }
                    }
                    return true;
                },

                isValidEmail(email) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(email);
                },

                async openPage(pageName) {
                    switch (pageName) {
                        case 'createMod':
                            // await updateNumViews()
                            // console.log(canCreate)
                            if(canCreate){
                                this.activeForm = 'createMod';
                            } else {
                                this.activeForm = 'home';
                            }
                            break;                        
                        case 'view':
                            window.location = 'view-will'                     
                        case 'delete':
                            jQuery.post('<?= admin_url('admin-ajax.php') ?>',{
                                action:'delete_form_data',
                            },
                            function(response){
                                window.location = 'will-testament'   
                            })
                        default:
                            this.activeForm = 'home';                            
                            break;
                    }
                    if(pageName.includes('sec')){
                        this.activeForm = pageName;
                        this.mainForm = true;
                        this.updateState()
                    }
                },                
                selectChanged(e) {
                    if (this.validateOnSubmit()) {                        
                        let page = e.target.value;
                        this.activeForm = page
                        if(this.checkSubSec.includes(page)) this.activeSubForm = 'intro'
                        this.updateState()
                    }else{
                        this.selectedOpt = this.activeForm
                    }
                },

                backPage() {
                    let page = this.allPages.indexOf(this.activeForm);

                    if (page > 0) {
                        if(this.checkSubSec.includes(this.activeForm)){
                            let activeSubIndex = this.allSubPages[this.activeForm].indexOf(this.activeSubForm)
                            if(activeSubIndex>0){
                                let subPage = this.validPrevSubPage(activeSubIndex)                                
                                if(subPage) {
                                    this.activeSubForm = subPage
                                }
                                else{
                                    let prevPage = page - 1;
                                    this.activeForm = this.allPages[prevPage];    
                                }
                            }else{
                                let prevPage = page - 1;
                                this.activeForm = this.allPages[prevPage];
                            }                            
                        }else{
                            let prevPage = page - 1;
                            this.activeForm = this.allPages[prevPage];
                        }
                    }
                    this.updateState()
                },
                validPrevSubPage(activeSubIndex){
                    if(activeSubIndex>0){                                    
                        let prevSubForm = this.allSubPages[this.activeForm][activeSubIndex-1]                                               
                        if(this.selectedFields[this.activeForm][prevSubForm] || this.selectedFields[this.activeForm][prevSubForm]==undefined){
                            return prevSubForm
                        }
                        else {
                            return this.validPrevSubPage(activeSubIndex-1)
                        }
                    }
                    else{
                        return false
                    }
                },
                nextPage() {
                    if (this.validateOnSubmit()) {
                        let page = this.allPages.indexOf(this.activeForm);


                        if (page >= 0 && page < this.allPages.length - 1) {

                            let activeSubIndex;
                            if (this.allSubPages[this.activeForm] && page == 2) {

                                let activeSubIndex = this.allSubPages[this.activeForm].indexOf(this.activeSubForm);

                                if(activeSubIndex>0){
                                    let subPage = this.validPrevSubPage(activeSubIndex);
                                    if (subPage == 'partner') {
                                        if (this.checkEmails(this.formData.sec3)) {
                                            alert('Warning: Email fields are the same Exit.');
                                        } else {
                                            this.submit(page);
                                        }
                                    }else{
                                        this.submit(page);
                                    }
                                }else{
                                    this.submit(page);
                                }
                            }else if(page == 5){
 

                                if (this.checkEmails(this.formData.sec6)) {
                                    alert('Warning: Email fields are the same Exit.');
                                } else {
                                    this.submit(page);
                                }
                            }else{
                                this.submit(page);
                            }
                        }
                    }
                    this.updateState()
                },
                checkEmails(secData) {
                    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    
                    for (let section of Object.values(secData)) {
                        if (Array.isArray(section)) {
                            let emails = section.map(child => {
                                if (child.email && typeof child.email === 'string') {
                                    return child.email.trim() !== "" ? child.email.trim() : null;
                                }
                                return null;
                            });
                            emails = emails.filter(email => email !== null);
                            let uniqueEmails = [...new Set(emails)];
                            if (emails.length !== uniqueEmails.length) {
                                return true;
                            }
                            let allValid = emails.every(email => emailRegex.test(email));
                            if (!allValid) {
                                return true;
                            }
                        }
                    }
                    return false;
                },
                selectedFields: {
                    sec3: {
                        partner: false,
                        children: false,
                        grandChildren: false,
                        deceased: true
                    },
                    sec6: {
                        alterExecutor: true,
                    },
                    sec9: {
                        divideEqual: false,
                        makeBequests: false,
                        multiBenefProvisions: false,
                        alterBenefIntro: false,
                        alterBenef: false,                        
                        secondLevelAlterBenef: false,
                        makeAlterPlan: false,
                        makeAlterPlanBequests: false,
                        describeAlterPlan: false,
                        descSpecificBequests: false,
                        residualAlterPlan: false,
                    },

                },                
                submit(page) {
                    if (this.checkSubSec.includes(this.activeForm)) {
                        if (this.activeForm === "sec3") {
                            if (this.activeSubForm === 'intro') {
                                this.selectedFields[this.activeForm].partner = [2, 3, 4, 5, 7].includes(+(this.formData[this.activeForm].status));
                                this.selectedFields[this.activeForm].children = this.formData[this.activeForm].children === '1';
                                this.selectedFields[this.activeForm].grandChildren = this.formData[this.activeForm].grandChildren === '1';                                
                            }                             
                        }
                        if(this.checkSubSec.includes(this.activeForm)){
                            if(this.activeForm === "sec9") {
                                this.selectedFields[this.activeForm].makeBequests = [1,2,4,5].includes(+(this.formData[this.activeForm].possessionDist))

                                if(this.activeSubForm == 'possessionsDist'){
                                    // this.selectedFields[this.activeForm].divideEqual = [1,2].includes(+(this.formData[this.activeForm].possessionDist))
                                    this.selectedFields[this.activeForm].makeBequests = [1,2,4,5].includes(+(this.formData[this.activeForm].possessionDist))
                                    this.selectedFields[this.activeForm].multiBenefProvisions = [1,2].includes(+(this.formData[this.activeForm].possessionDist))
                                    this.selectedFields[this.activeForm].alterBenefIntro = [2,4].includes(+(this.formData[this.activeForm].possessionDist))
                                    this.selectedFields[this.activeForm].alterBenef = [1,2,3,4].includes(+(this.formData[this.activeForm].possessionDist))
                                    this.selectedFields[this.activeForm].residualAlterPlan = [1,2,3,4,5,6].includes(+(this.formData[this.activeForm].possessionDist))
                                    this.selectedFields[this.activeForm].descSpecificBequests = [6].includes(+(this.formData[this.activeForm].possessionDist))
                                    // descSpecificBequests
                                    this.selectedFields[this.activeForm].secondLevelAlterBenef = [1,2,3,4].includes(+(this.formData[this.activeForm].possessionDist))
                                }
                                if(this.activeSubForm == 'alterBenef'){                                    
                                    this.selectedFields[this.activeForm].secondLevelAlterBenef = [2,3].includes(+(this.formData[this.activeForm].alterBenefProvisions.radio))
                                    this.selectedFields[this.activeForm].makeAlterPlan = [3,4].includes(+(this.formData[this.activeForm].alterBenefProvisions.radio))
                                    this.selectedFields[this.activeForm].makeAlterPlanBequests = [3,4].includes(+(this.formData[this.activeForm].alterBenefProvisions.radio))
                                    this.selectedFields[this.activeForm].residualAlterPlan = [4,5].includes(+(this.formData[this.activeForm].alterBenefProvisions.radio))
                                    this.selectedFields[this.activeForm].describeAlterPlan = [5].includes(+(this.formData[this.activeForm].alterBenefProvisions.radio))
                                }
                                if(this.selectedFields[this.activeForm].divideEqual){  //Update section 4 field
                                    this.formData.sec4.otherBeneficiaries = 2
                                }                                
                            }
                        }
                        let index = this.allSubPages[this.activeForm].indexOf(this.activeSubForm);
                        if(index<this.allSubPages[this.activeForm].length-1){
                            let nextSub = this.validNextSubPage(index)
                            if(nextSub){
                                this.activeSubForm = nextSub
                            }
                            else{
                                this._nextPage(page)
                            }
                        } else {
                            this._nextPage(page)
                        }
                    } else {
                        this._nextPage(page)
                    }
                },
                validNextSubPage(activeSubIndex){
                    if(activeSubIndex < this.allSubPages[this.activeForm].length-1){ 
                        let activeSubForm = this.allSubPages[this.activeForm][activeSubIndex]
                        let nextSubForm = this.allSubPages[this.activeForm][activeSubIndex+1]                        
                        if(this.selectedFields[this.activeForm][nextSubForm]){
                            return nextSubForm
                        }
                        else {
                            return this.validNextSubPage(activeSubIndex+1)
                        }
                    }
                    else{
                        return false
                    }                    
                },
                _nextPage(page) {
                    let nextPage = page + 1;
                    this.activeForm = this.allPages[nextPage];
                    if (this.checkSubSec.includes(this.activeForm)) {
                        this.activeSubForm = this.allSubPages[this.activeForm][0];
                    }
                    this.updateState()
                },

                async save() {
                    if (this.validateOnSubmit()) {

                        let page = this.allPages.indexOf(this.activeForm);


                        

                        if (page == 2) {
                            let activeSubIndex;
                            if (this.allSubPages[this.activeForm] && page == 2) {
                                let activeSubIndex = this.allSubPages[this.activeForm].indexOf(this.activeSubForm);
                                if(activeSubIndex>0){
                                    let subPage = this.validPrevSubPage(activeSubIndex);
                                     
                                    if (subPage == 'partner' || subPage == 'children') {
                                        if (this.checkEmails(this.formData.sec3)) {
                                            alert('Warning: Email fields are the same Exit.');
                                            return false;
                                        }
                                    }
                                }
                            }
                        }else if(page == 5){
                            if (this.checkEmails(this.formData.sec6)) {
                                alert('Warning: Email fields are the same Exit.');
                                return false;
                            }
                        }

                        if (page == 10) {

                            data = JSON.parse(JSON.stringify(this.formData))
                            jQuery.post('<?= admin_url('admin-ajax.php') ?>',{
                                action:'update_form_data',
                                sec:'last',
                                data:data
                            },
                            function(response){
                                let successMsg = document.createElement('div');
                                successMsg.textContent = 'Save Successfully!';
                                successMsg.classList.add('save-success-msg');
                                successMsg.style.position = 'absolute';
                                successMsg.style.bottom = '-30px';
                                successMsg.style.zIndex = '99';
                                successMsg.style.color = '#61CE70';
                                document.querySelector('.action-section').appendChild(successMsg);
                                 setTimeout(() => {
                                    // location.reload();
                                }, 5000);
                            });
                        }else{
                            data = JSON.parse(JSON.stringify(this.formData))
                            jQuery.post('<?= admin_url('admin-ajax.php') ?>',{
                                action:'update_form_data',
                                data:data
                            },
                            function(response){
                                console.log(response);
                                alert('Save Successfully!');  
                            });
                        }
                    }
                },
                isMainForm(){                    
                    return this.allPages.includes(this.activeForm)
                },
                updateState(){
                    // Update Progress Bar
                    setTimeout(()=>{
                        try {
                            if (this.isMainForm() && this.activeForm!='sec12') {
                                this.progressValue = this.activeForm.split("sec")[1]                                               
                            }
                        }
                        catch (err) {
                            console.log('Progress bar cannot be updated');
                        }                        
                    },0)
                    this.selectedOpt = this.activeForm // Update Select Section
                    // Check if it is a main form
                    if(this.isMainForm()) {
                        this.mainForm = true
                    }else{
                        this.mainForm = false
                    }

                    // Check if WIll holder is having any living minor child/grandchild for section 5
                    if(this.activeForm=='sec5'){                        
                        if(this.hasMinorChild()) {                            
                        }
                        if(this.formData.sec5.guardianDetails.length < this.minorChildren.length) this.extendGuardianFields(this.formData.sec5.guardianDetails.length)
                        if(this.formData.sec5.guardianDetails.length < this.minorgrChildren.length) this.extendgrGuardianFields(this.formData.sec5.guardianDetails.length)
                    } else if(this.activeForm=='sec9' && this.activeSubForm=='possessionsDist'){
                        this.beneficiaryNames = this.formData.sec4.beneficiaryDetails.map((item,index)=>{
                            return {'name':item.name,'value':index}
                        })
                        this.beneficiaryNames.unshift({'name':'None','value':-1})
                        
                    }
                    if(this.activeForm=='sec9' && this.activeSubForm=='multiBenefProvisions'){
                        this.extendMultiBenefProvisions()
                    }
                },
                extendGuardianFields(lengthDetails){
                    if(lengthDetails<this.minorChildren.length){
                        let dif = this.minorChildren.length - lengthDetails
                        for(let i=0;i<dif;i++)
                        this.formData.sec5.guardianDetails.push({'childName':'','name':'','reason':'','email':'','occupation':'','address':'','parish':'','acceptTerms':''})                    
                    }
                },
                extendgrGuardianFields(lengthDetails){
                    if(lengthDetails<this.minorgrChildren.length){
                        let dif = this.minorgrChildren.length - lengthDetails
                        for(let i=0;i<dif;i++)
                        this.formData.sec5.guardianDetails.push({'childName':'','name':'','reason':'','email':'','occupation':'','address':'','parish':'','acceptTerms':''})                    
                    }
                },
                extendMultiBenefProvisions(){
                    let lengthBenef = this.formData.sec4.beneficiaryDetails.length
                    let dif = lengthBenef - this.formData.sec9.multiBenefProvisions.length
                    if(lengthBenef>dif){
                        for(let i=0;i<dif;i++){
                            this.formData.sec9.multiBenefProvisions.push({"radio":1,"alterBenefIndex":-1,"shareDesc":""})
                        }
                    }
                },
                hasMinorChild(){
                    let hasMinor = false;
                    let children = this.formData.sec3.children;
                    
                    if(children==1){ 
                        let allChild = this.formData.sec3.childDetails
                        let dobs =allChild.map((detail)=>detail.dob)
                        let minorChildNames = []                        
                        for(let i=0;i<dobs.length;i++){                            
                            dob = new Date(dobs[i])
                            var month_diff = Date.now() - dob.getTime();
                            var age_dt = new Date(month_diff);
                            var year = age_dt.getUTCFullYear(); 
                            var age = Math.abs(year - 1970);
                            if(age<18) {        
                                minorChildNames.push(this.formData.sec3.childDetails[i].name)                                                        
                            }
                        }
                        if(minorChildNames.length>0) {
                            this.minorChildren = minorChildNames

                            return true;
                        }
                    }  
                    
                },
                hasMinorgrChild() {

                    let hasgrMinor = false;
                    let grandChildren = this.formData.sec3.grandChildren;
                    
                    if(grandChildren==1){ 
                        let allChild = this.formData.sec3.grandChildDetails
                        let dobs =allChild.map((detail)=>detail.dob)
                        let minorgrandChildNames = []                        
                        for(let i=0;i<dobs.length;i++){                            
                            dob = new Date(dobs[i])
                            var month_diff = Date.now() - dob.getTime();
                            var age_dt = new Date(month_diff);
                            var year = age_dt.getUTCFullYear(); 
                            var age = Math.abs(year - 1970);
                            if(age<18) {        
                                minorgrandChildNames.push(this.formData.sec3.grandChildDetails[i].name)                                                        
                            }
                        }

                        if(minorgrandChildNames.length>0) {
                            this.minorgrandChildren = minorgrandChildNames
                            return true;
                        }
                    }  
 
                }, 
                minorgrandChildren: [],
                minorChildren: [], 
                partner: {},
                children: [],
                grandChildren: [],

                saveThenNextPage() {
                    this.save();
                    this.nextPage();
                }
            }))
        })
        jQuery(document).ready(function($) {
            

            var signForm = $('#signForm');
            var signUploadInput = $('#signature-file');

            // Ensure ajaxurl is defined
            if (typeof ajaxurl === 'undefined') {
                var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            }

            $(signUploadInput).change(function(e) {
                e.preventDefault();
                const file = signUploadInput[0].files[0];
                const validTypes = ['application/pdf'];
                
                    if (file && validTypes.includes(file.type)) {
                        const signFormData = new FormData(signForm[0]);
                        signFormData.append('action', 'sign_frontend_ajax_upload');
                        signFormData.append('signnonce', $('#signnonce').val()); // Append the nonce

                        $.ajax({
                            type: "POST",
                            data: signFormData,
                            dataType: "json",
                            url: ajaxurl,
                            cache: false,
                            processData: false,
                            contentType: false,
                            enctype: 'multipart/form-data',
                            success: function(response) {
                                if (response.success) {
                                    $('#sign-url').val(response.data.url);
                                    document.querySelector('#sign-url').dispatchEvent(new Event('input'));
                                    document.querySelector('#placeholder-img').src = response.data.url;
                                    alert('Sign Upload Successful');
                                } 
                                // else {
                                //     alert('Upload failed: ' + (response.data.message || 'Unknown error'));
                                // }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                // console.error('Error details:', textStatus, errorThrown, jqXHR.responseText);
                                alert('Something went wrong! no success' + textStatus + ': ' + errorThrown);
                            }
                        });
                    } else {
                        alert('Please upload a valid pdf file');
                    }
            });
            var videoForm = $('#videoForm');
            $('#video-file').change(function(e) {
                e.preventDefault();
                var videoFormData = new FormData(videoForm[0]);
                videoFormData.append('action', 'video_frontend_ajax_upload');
                
                $.ajax({
                    type: "POST",
                    data: videoFormData,
                    dataType: "json",
                    url: ajaxurl,
                    cache: false,
                    processData: false,
                    contentType: false,
                    enctype: 'multipart/form-data',
                    success: function(response) {
                        if (response.success) {
                            $('#video_url').val(response.data.url);
                            document.querySelector('#video_url').dispatchEvent(new Event('input'));
                            document.querySelector('#uploaded-video').src = response.data.url;
                        } else {
                            alert('Upload failed: ' + (response.data.message || 'Unknown error'));
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error details:', textStatus, errorThrown, jqXHR.responseText);
                        alert('Something went wrong! ' + textStatus + ': ' + errorThrown);
                    }
                });
            });
        });
 
   </script>
    
<?php
} else {
    ?>      
        <section >

            <div class="container p-0 try-buy-service-wrapper mt-4 border border-2 rounded shadow p-4">
                <div>
                    <h3>Last Will and Testament</h3>
                    <p>Create a perfect, lawyer-approved legal Will from the comfort of your home.</p>
                    <div class="try-buy-btns bg-green-600 text-white rounded border border-green-600  hover:bg-green-500">
                        <?php 
                        echo do_shortcode('[membership]
                            <a class="btn btn-dark extend-plan px-3" href="/will-testament/">Start Creating Will</a>
                        [/membership]');
                        echo do_shortcode('[membership level="0"]
                             <a class="btn btn-dark extend-plan px-3" href="/membership-levels/">Join to create a will</a>
                        [/membership]');
                        ?>
                    
                     
                    </div>
                </div>                
            </div>
        </section>
            <?php
}
get_footer();
?>