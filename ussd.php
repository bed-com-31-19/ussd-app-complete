<?php
    // Print the response as plain text so that the gateway can read it
    header('Content-type: text/plain');

    /*
    * For africastalking 
    * When working from a live server change the GET[] method 
    * to POST[] (that is how africastalking do their stuff) 
    */ 
    $phone = $_GET['phoneNumber'];
    $session_id = $_GET['sessionId'];
    $service_code = $_GET['serviceCode'];
    $ussd_string = $_GET['text'];


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
                crop_production($ussd_string_explode);
                break;
            
            case 2:
                animal_production($ussd_string_explode);
                break;  
               
            default:
                echo "Invalid response";    
                break;

        }
    }

    function display_menu() {
        $ussd_string = "WELCOME TO ULIMI WATHU \n \n 1. Crop production \n 2. Animal production \n";
        ussd_proceed($ussd_string);
    }

    function crop_production($ussd_string_exploded) {

        if (count($ussd_string_exploded) == 1 ) {
            $ussd_string = "Please Select: \n \n 1. Maize production \n 2. Groundnuts production \n";
            ussd_proceed($ussd_string);	  
        }

        else if (count($ussd_string_exploded) == 2) {
            $option = $ussd_string_exploded[1];

            if($option == "1") {
                $ussd_string = "Please Select Maize Variety: \n \n1. SC 719(Njobvu) \n2. SC 627 (Mkango)\n";
                ussd_proceed($ussd_string); 
            }
            else if ($option == "2") {
                $ussd_string = "Please Select Maize Variety: \n \n1. Chalimbana\n";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }        
        }

        else if (count($ussd_string_exploded) == 3) {
            $option = $ussd_string_exploded[2];
            if ($option == "1") {
                $ussd_string = "Please Select: \n \n1. Husbandry practices \n2. Pests and dieseases \n";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            } 
           
        }

        else if(count($ussd_string_exploded) == 4 && $ussd_string_exploded[2] == 1) {
            $option = $ussd_string_exploded[3];

            if($option == "1") {
                $ussd_string = "Clear the land as early as possible before November for summer crop.\n";
                $ussd_string .= "Make ridges or rows at 60 - 75cm.\nEnter N for next";
                ussd_proceed($ussd_string);
            }

            else if ($option == "2") {
                echo "Paste and diseases menu";
            }

            else {
                echo "Invalid input";
            }
        }
        else if (count($ussd_string_exploded) == 5 && $ussd_string_exploded[2] == 1) {
            $option = $ussd_string_exploded[4];

            if($option == "N" || $option == "n") {
                $ussd_string = "Plant one seed per station, spaced at 25 - 30cm.\n";
                $ussd_string .= "Seed need around 25kg/ha or 10kg/acre.\n \nEnter N for next";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }

        }
        else if (count($ussd_string_exploded) == 6 && $ussd_string_exploded[2] == 1) {
            $option = $ussd_string_exploded[5];

            if($option == "N" || $option == "n") {
                $ussd_string = "Apply 100 kg basal fertilizer preferably 23:21:0 + 4s at 5gms / plant just after emegency.\n";
                $ussd_string .= "Keep the field weed free all the time.\n \nEnter N for next";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }
        else if (count($ussd_string_exploded) == 7 && $ussd_string_exploded[2] == 1) {
            $option = $ussd_string_exploded[6];

            if($option == "N" || $option == "n") {
                $ussd_string = "Apply top dressing fertlizer like urea, when plants are 30cm high or 21 days after emergency.\n";
                $ussd_string .= "\nEnter N for next";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }

        }
        else if (count($ussd_string_exploded) == 8 && $ussd_string_exploded[2] == 1) {
            $option = $ussd_string_exploded[7];

            if($option == "N" || $option == "n") {
                $ussd_string = "Harvest when the crop is at 18% moisture content and leave it to dry in granaries.\n";
                $ussd_string .= "\nEnter N for next";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }

        }
        else if (count($ussd_string_exploded) == 9 && $ussd_string_exploded[2] == 1) {
            $option = $ussd_string_exploded[8];

            if($option == "N" || $option == "n") {
                $ussd_string = "Shell and bag when the crop is at 12.5% moisture content. Remember to treat with pesticide to avoid weavil.\n";
                $ussd_string .= "\nThank you for using Ulimi wathu app";
                ussd_stop($ussd_string);
            }
            else {
                echo "Invalid input";
            }

        }

    }

    function animal_production($ussd_string_exploded) {
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