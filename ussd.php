<?php
    // Print the response as plain text so that the gateway can read it
    header('Content-type: text/plain');

    /*
    * For africastalking 
    * When working from a live server change the GET[] method 
    * to POST[] (that is how africastalking do their stuff) 
    */ 
    $phone = $_POST['phoneNumber'];
    $session_id = $_POST['sessionId'];
    $service_code = $_POST['serviceCode'];
    $ussd_string= $_POST['text'];


    /*
     * Split text input based on asteriks(*)
     * Africa's talking appends asteriks for after every menu level or input
     * One needs to split the response from Africa's Talking in order to determine
     * the menu level and input for each level
     */
    $level = 0;

    /*
     * str_replace replaces all #’s with *’s, which then is exploded with * as the
     * separator to give an array. Array index starts at 0, so we pass value at 0,
     * to check what value the user selected, $ussdString_explode
     * The count tells us what level the user is at i.e how many times the user has responded
     */
    if ($ussd_string != "") {
        $ussd_string= str_replace("#", "*", $ussd_string);  
        $ussd_string_explode = explode("*", $ussd_string);  
        $level = count($ussd_string_explode);
    }

    /*
     * $level=0 means the user hasnt replied. We use levels to track the number of user replies
     * show the home/first menu
     */
    if ($level == 0) {
        display_menu();

    }

    // $level>0 means the user has entered an option
    else if ($level > 0) {
        switch($ussd_string_explode[0]) {
            case 1:
                crop_production();
                break;
            
            case 2:
                animal_production();
                break;    

        }

    }







    function display_menu() {
        $ussd_string = "WELCOME TO ULIMI WATHU \n \n 1. Crop production \n 2. Animal production \n";
        ussd_proceed($ussd_string);

    }

    function crop_production() {
        $ussd_string = "CON Please Select: \n \n 1. Maize production \n 2. Groundnuts production \n";
        ussd_proceed($ussd_string);
    }

    function animal_production() {
        $ussd_string = "Please Select: \n \n 1. Chicken production \n 2. Sheep production \n";
        ussd_proceed($ussd_string);
    }

    /*
    * The ussd_proceed function appends CON to the USSD response the application gives.
    * This informs Africa's Talking USSD gateway and consecuently Safaricom's
    * USSD gateway that the USSD session is still in session or should still continue
    */
    function ussd_proceed($ussd_string){
        echo "CON $ussd_string";
    }

    /*
    * This ussd_stop function appends END to the USSD response the application gives.
    * This informs Africa's Talking USSD gateway and consecuently Safaricom's
    * USSD gateway that the USSD session should end.
    */
    function ussd_stop($ussd_string){
        echo "END $ussd_string";
    }
   
     

?>