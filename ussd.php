<?php

    // Print the response as plain text so that the gateway can read it
    header('Content-type: text/plain');

    /*
    * For africastalking 
    * When working from a live server change the GET[] method 
    * to POST[] (that is how africastalking do their stuff) 
    */ 
    $phone_number = $_GET['phoneNumber'];
    $session_id = $_GET['sessionId'];
    $service_code = $_GET['serviceCode'];
    $ussd_string = $_GET['text'];

    // Database configuration
    $servername = "db";
    $username = "programmer";
    $password = "Cyberman@2999";
    $dbname = "users";

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


    // Create database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check db connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }
    
    $phoneNumber = $phone_number;

    // Check if the user is already in database 
    $sql7 = "SELECT * FROM user WHERE phone_number LIKE '%".$phoneNumber."%' LIMIT 1";
	$userQuery=$conn->query($sql7);
	$userAvailable=$userQuery->fetch_assoc();

    // if user is not available register
    if (!$userAvailable) {

        if ( $level == 0) {
            display_registration_menu();  
        }

        else if ($level > 0) {

            if (count($ussd_string_explode) == 1) {
                $ussd_string = "Enter your first name:\n";
                ussd_proceed($ussd_string);
            }

            else if (count($ussd_string_explode) == 2) {
                $ussd_string = "Enter your last name:\n";
                ussd_proceed($ussd_string);
            }

            else if (count($ussd_string_explode) == 3) {
                $ussd_string = "Enter your email:\n";
                ussd_proceed($ussd_string);
            }

            else if (count($ussd_string_explode) == 4) {
                $ussd_string = "Enter your district:\n";
                ussd_proceed($ussd_string);
            }

            else if (count($ussd_string_explode) == 5) {
                $ussd_string = "Enter your village:\n";
                ussd_proceed($ussd_string);
            }

            if (count($ussd_string_explode) == 6) {
                $query_1 = "INSERT INTO user (first_name, last_name, phone_number) VALUES (?, ?, ?)";
                $sql = $conn->prepare($query_1);

                // bind parameters
                $sql->bind_param("sss", $firstName, $lastName, $phoneNumber);

                $firstName = $ussd_string_explode[1];
                $lastName = $ussd_string_explode[2];
                $phoneNumber = $phone_number;
                $email = $ussd_string_explode[3];
                $district = $ussd_string_explode[4];
                $village =$ussd_string_explode[5];
                $sql->execute();

                $ussd_string = "Registered successfully";

                ussd_stop($ussd_string);
            }    
        }
    }


    

    


    // if user is in the db then save the menu
    else {

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
    }  

    function display_menu() {
        $ussd_string = "WELCOME TO ULIMI WATHU \n \n 1. Crop production \n 2. Animal production \n";
        ussd_proceed($ussd_string);
    }

    function display_registration_menu() {
        $ussd_string = "WELCOME TO ULIMI WATHU \nREGISTRATION MENU \n 1. Register \n 2. Exit \n";
        ussd_proceed($ussd_string);
    }

    function crop_production($ussd_string_exploded) {

        if (count($ussd_string_exploded) == 1 ) {
            $ussd_string = "Please Select: \n \n 1. Maize production \n 2. Groundnuts production \n";
            ussd_proceed($ussd_string);	  
        }

        else if (count($ussd_string_exploded) == 2) {
            $option = $ussd_string_exploded[1];

            if ($option == "1" || $option == "2") {
                $ussd_string = "Please Select: \n \n1. Husbandry practices \n2. Pests and dieseases \n";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid response";
            } 
        }

        else if(count($ussd_string_exploded) == 3) {
            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            print_r($ussd_string_exploded);

            /* 
            *handle maize level 3
            *
            *
            */
            if($option1 == "1" && $option2 == "1" && $option3 == "1") {
                $ussd_string = "Clear the land as early as possible before November for summer crop.\n";
                $ussd_string .= "Make ridges or rows at 60 - 75cm.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            //pest and disease control for maize or groundnuts
            else if ($option1 == "1" && $option2 == "2" || $option2 == "1" && $option3 == "2") {
                $ussd_string = "Please Select: \n \n1. Disease and Control\n";
                $ussd_string .= "2. Pest and Control.\n";
                ussd_proceed($ussd_string);
            }
            
            /* 
            *handle groundnuts level 3
            *
            *
            */
            //groundnuts practices
            else if ($option1 == "1" && $option2 == "2" && $option3 == "1") {
                $ussd_string = "groundnuts practices 1\n";
                $ussd_string .= "groundnuts practices 1.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }

        else if (count($ussd_string_exploded) == 4) {
            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            $option4 = $ussd_string_exploded[3];
            print_r($ussd_string_exploded);

            /* 
            *handle maize level 4
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n" || $option4 == "N") {
                $ussd_string = "Plant one seed per station, spaced at 25 - 30cm.\n";
                $ussd_string .= "Seed need around 25kg/ha or 10kg/acre.\n \nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "1") {
                $ussd_string = "Maize Desease 1\n";
                $ussd_string .= "Maize Desease 2.\n";
                $ussd_string .= "Maize Desease 3.\nPress n for desease control.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "2") {
                $ussd_string = "Please select for pest control:\n";
                $ussd_string .= "Pest 1\n";
                $ussd_string .= "Pest 2.\n";
                $ussd_string .= "Pest 3.";
                ussd_proceed($ussd_string);
            }

            /* 
            *handle groundnuts level 4
            *
            *
            */
            else if ($option1 == "1" && $option2 == "2" && $option3 == "1" && $option4 == "n" || $option4 == "N") {
                $ussd_string = "groundnuts practices 2\n";
                $ussd_string .= "groundnuts practices 2.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "1") {
                $ussd_string = "Desease 1\n";
                $ussd_string .= "Desease 2.\n";
                $ussd_string .= "Desease 3.\nPress n for desease control.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "2") {
                $ussd_string = "Please select for pest control:\n";
                $ussd_string .= "Pest 1\n";
                $ussd_string .= "Pest 2.\n";
                $ussd_string .= "Pest 3.";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }

        else if (count($ussd_string_exploded) == 5) {
            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            $option4 = $ussd_string_exploded[3];
            $option5 = $ussd_string_exploded[4];
            print_r($ussd_string_exploded);

            /* 
            *handle maize level 5
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n" || $option4 == "N" && $option5 == "n" || $option5 == "N") {
                $ussd_string = "Apply 100 kg basal fertilizer preferably 23:21:0 + 4s at 5gms / plant just after emegency.\n";
                $ussd_string .= "Keep the field weed free all the time.\n \nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "1" && $option5 == "n" || $option5 == "N") {
                $ussd_string = "Maize Desease Control page 1\n";
                $ussd_string .= "Maize Desease Contol page 1.\n";
                $ussd_string .= "Maize Desease Control page 1.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "2" && $option5 == "1") {
                $ussd_string = "Maize Pest 1 Control\n";
                $ussd_string .= "Maize Pest 1 Control.\n";
                $ussd_string .= "Maize Pest 1 Control.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "2" && $option5 == "2") {
                $ussd_string = "Maize Pest 2 Control\n";
                $ussd_string .= "Maize Pest 2 Control.\n";
                $ussd_string .= "Maize Pest 2 Control.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "2" && $option5 == "3") {
                $ussd_string = "Maize Pest 3 Control\n";
                $ussd_string .= "Maize Pest 3 Control.\n";
                $ussd_string .= "Maize Pest 3 Control.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            /* 
            * handle groundnuts level 5
            *
            */
            if ($option1 == "1" && $option2 == "2" && $option3 == "1" && $option4 == "n" && $option4 == "N" && $option5 == "n" || $option5 == "N") {
                $ussd_string = "groundnuts practices 3\n";
                $ussd_string .= "groundnuts practices 3.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "1" && $option5 == "n" || $option5 == "N") {
                $ussd_string = "Groundnuts Desease Control page 1\n";
                $ussd_string .= "Groundnuts Desease Contol page 1.\n";
                $ussd_string .= "Groundnuts Desease Control page 1.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "2" && $option5 == "1") {
                $ussd_string = "Groundnuts Pest 1 Control\n";
                $ussd_string .= "Groundnuts Pest 1 Control.\n";
                $ussd_string .= "Groundnuts Pest 1 Control.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "2" && $option5 == "2") {
                $ussd_string = "Groundnuts Pest 2 Control\n";
                $ussd_string .= "Groundnuts Pest 2 Control.\n";
                $ussd_string .= "Groundnuts Pest 2 Control.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "2" && $option5 == "3") {
                $ussd_string = "Groundnuts Pest 3 Control\n";
                $ussd_string .= "Groundnuts Pest 3 Control.\n";
                $ussd_string .= "Groundnuts Pest 3 Control.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }
 
        else if (count($ussd_string_exploded) == 6) {

            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            $option4 = $ussd_string_exploded[3];
            $option5 = $ussd_string_exploded[4];
            $option6 = $ussd_string_exploded[5];
            print_r($ussd_string_exploded);

            /* 
            *handle maize level 6
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n" || $option4 == "N" && $option5 == "n" || $option5 == "N" && $option6 == "n" || $option6 == "N") {
                $ussd_string = "Apply top dressing fertlizer like urea, when plants are 30cm high or 21 days after emergency.\n";
                $ussd_string .= "\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }

        else if (count($ussd_string_exploded) == 7) {
            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            $option4 = $ussd_string_exploded[3];
            $option5 = $ussd_string_exploded[4];
            $option6 = $ussd_string_exploded[5];
            $option7 = $ussd_string_exploded[6];
            print_r($ussd_string_exploded);

            /* 
            *handle maize level 7
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n" || $option4 == "N" && $option5 == "n" || $option5 == "N" && $option6 == "n" || $option6 == "N" && $option7 == "n" || $option7 == "N") {
                $ussd_string = "Harvest when the crop is at 18% moisture content and leave it to dry in granaries.\n";
                $ussd_string .= "\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }

        else if (count($ussd_string_exploded) == 8 ) {
            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            $option4 = $ussd_string_exploded[3];
            $option5 = $ussd_string_exploded[4];
            $option6 = $ussd_string_exploded[5];
            $option7 = $ussd_string_exploded[6];
            $option8 = $ussd_string_exploded[7];
            print_r($ussd_string_exploded);

            /* 
            *handle maize level 8
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n" || $option4 == "N" && $option5 == "n" || $option5 == "N" && $option6 == "n" || $option6 == "N" && $option7 == "n" || $option7 == "N" && $option8 == "n" || $option8 == "N") {
                $ussd_string = "Shell and bag when the crop is at 12.5% moisture content. \nRemember to treat with pesticide to avoid weavil.\n";
                $ussd_string .= "\nThank you for using Ulimi wathu app";
                ussd_proceed($ussd_string);
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