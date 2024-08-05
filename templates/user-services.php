<?php 
/* Template Name: User Services  */ 

//Terminate if the Wills Helper plugin is not activated
if(function_exists('is_plugin_active')){
    if (!is_plugin_active( 'wills-helper/wills-helper.php')) {    
        echo 'Wills Helper Plugin is not active, activate it first.';
        exit;
    }
}
wp_enqueue_style('bootstrap');
wp_enqueue_script('bootstrap');

get_header();
if(is_user_logged_in()){
    $user_id = get_current_user_id(); 
    $userdata = get_userdata($user_id);
    $user_meta = get_user_meta( $user_id);
    $date_display_format = "F d, Y";    
    
    $createdDate = date(EXP_DATE_FORMAT,strtotime($userdata->user_registered)); 
    $dateObject =  DateTime::createFromFormat(EXP_DATE_FORMAT, $createdDate);

    $creationDate = $dateObject->format($date_display_format);
    if ($user_meta['acc_expires_on']) {
        $dateObject = DateTime::createFromFormat(EXP_DATE_FORMAT, $user_meta['acc_expires_on'][0]);
    }
    $expiryDate = $dateObject->format($date_display_format);

    $isMemberExpired = strtotime($expiryDate) < time();  
    
  
?>
<div class="services-wrapper py-5">
    <div class="inner-section">
        <div class="welcome-section">
            <p class="mb-1">Welcome, <?= $user_meta['first_name'][0]; ?></p>
            <a class="btn btn-info logout-btn" href="<?= site_url('logout')?>">Logout</a>
        </div>    
        <div class="membership-section mb-4">
            <p class="mb-1">Member since: <span><?= $creationDate ?></span></p>
            <?php
            if(!$isMemberExpired){ ?> <p class="mb-1">Membership Expires: <span><?= $expiryDate ?></span></p>
            <?php
            }else{ ?> <p class="mb-1">Membership Expired: <span><?= $expiryDate ?></span></p> <?php } ?>
        </div>
        
        <div class="extend-memberships-btns d-flex flex-column gap-2 flex-md-row">
                                
        </div>
        <?php 
        // if(!$isMemberExpired){ 
            ?>
            <div class="container p-0 try-buy-service-wrapper mt-4 border border-2 rounded shadow p-4">
                <div>
                    <h3>Last Will and Testament</h3>
                    <p>Create a perfect, lawyer-approved legal Will from the comfort of your home.</p>
                    <div class="try-buy-btns">
                        <?php 
                        echo do_shortcode('[membership]
                            <a class="btn btn-dark extend-plan px-3" href="/will-testament/">Start Creating Will</a>
                        [/membership]');
                        echo do_shortcode('[membership level="0"]
                             <a class="btn btn-dark extend-plan px-3" href="/membership-levels/">Join to create a will</a>
                        [/membership]');
                        ?>
                    
                    <a class="btn btn-dark extend-plan px-3 d-none" href="#">BUY ($39.95)</a>
                    </div>
                </div>                
            </div>
        <?php //} else {?>
            <!-- <h2>There is no active membership.</h2> -->
        <?php// } ?>
    </div>
</div>
<?php
}else{
    echo '
            <div class="container p-0 try-buy-service-wrapper mt-4 border border-2 rounded shadow p-4">
                <div>
                    <h2>Login to Continue</h2> 
                    <div class="try-buy-btns"> 
                    <a class="btn btn-dark extend-plan px-3" href="/membership-account/">Login</a> 
                    </div>
                </div>                
            </div>
            ';

}
get_footer();
?>
