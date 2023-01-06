<?php

    // Print the response as plain text so that the gateway can read it
    header('Content-type: text/plain');

    /*
    * For africastalking 
    * When working from a live server change the GET[] method 
    * to POST[] (that is how africastalking do their stuff) 
    */ 
    $phone_number = $_POST['phoneNumber'];
    $session_id = $_POST['sessionId'];
    $service_code = $_POST['serviceCode'];
    $ussd_string = $_POST['text'];

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

    //create tables
	// $sql0 = "CREATE DATABASE users";
    // $sql1 = "CREATE TABLE user ( id INT(10) AUTO_INCREMENT PRIMARY KEY, first_name VARCHAR(20) NOT NULL, 
    //     last_name VARCHAR(20) NOT NULL, phone_number VARCHAR(30) NOT NULL, 
    //     email VARCHAR(30) NOT NULL, district VARCHAR(30) NOT NULL, village VARCHAR (30) NOT NULL)";
    
    $phoneNumber = $phone_number;

    // Check if the user is already in database 
    $sql7 = "SELECT * FROM user WHERE phone_number LIKE '%".$phoneNumber."%' LIMIT 1";
	$userQuery=$conn->query($sql7);
	$userAvailable=$userQuery->fetch_assoc();

    // if user is not available register
    if (!$userAvailable) {

        /*
        * $level=0 means the user hasnt replied. We use levels to track the number of user replies
        * show the home/first menu
        */
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
                $query_1 = "INSERT INTO user (first_name, last_name, phone_number, email, district, village) VALUES (?, ?, ?, ?, ?, ?)";
                $sql = $conn->prepare($query_1);

                // bind parameters
                $sql->bind_param("ssssss", $firstName, $lastName, $phoneNumber, $email, $district, $village);

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
        $ussd_string = "WELCOME TO ULIMI WATHU\n 1. Crop production \n2. Animal production\n";
        ussd_proceed($ussd_string);
    }

    function display_registration_menu() {
        $ussd_string = "WELCOME TO ULIMI WATHU \nREGISTRATION MENU \n 1. Register \n 2. Exit \n";
        ussd_proceed($ussd_string);
    }

    function crop_production($ussd_string_exploded) {

        if (count($ussd_string_exploded) == 1 ) {
            $ussd_string = "Please Select: \n1. Maize production \n2. Groundnuts production\n";
            ussd_proceed($ussd_string);	  
        }

        else if (count($ussd_string_exploded) == 2) {
            $option = $ussd_string_exploded[1];

            if ($option == "1" || $option == "2") {
                $ussd_string = "Please Select:\n1. Husbandry practice \n2. Pests and Diseases Control\n";
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
            else if ($option1 == "1" && $option2 == "1" && $option3 == "2") {
                $ussd_string = "Please Select:\n";
                $ussd_string .= "1. Diseases and Control\n";
                $ussd_string .= "2. Pests and Control";
                ussd_proceed($ussd_string);
            }
            
            /* 
            *handle groundnuts level 3
            *
            *
            */
            //groundnuts practices
            else if ($option1 == "1" && $option2 == "2" && $option3 == "1") {
                $ussd_string = "Land preparation must be done early enough (July or August).\n";
                $ussd_string .= "To allow decomposition of residues before the growing/rainy season. \nPress n to proceed...";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2") {
                $ussd_string = "Please Select:\n";
                $ussd_string .= "1. Diseases and Control\n";
                $ussd_string .= "2. Pests and Control \nPress n to proceed...";
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

            /* 
            *handle maize level 4
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n") {
                $ussd_string = "Plant one seed per station, spaced at 25 - 30cm.\n";
                $ussd_string .= "Seed need around 25kg/ha or 10kg/acre.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "1") {
                $ussd_string = "1. Maize streak virus\n";
                $ussd_string .= "2. Leaf spots \n";
                $ussd_string .= "3. Maize Lethal Necrosis.\nPress n for diseases control.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "2") {
                $ussd_string = "Please select pest for control:\n";
                $ussd_string .= "1. Pink stem borer,\n";
                $ussd_string .= "2. Fall Armyworm\n";
                $ussd_string .= "3. Shoot fly";
                ussd_proceed($ussd_string);
            }

            /* 
            *handle groundnuts level 4
            *
            *
            */
            else if ($option1 == "1" && $option2 == "2" && $option3 == "1" && $option4 == "n" || $option4 == "N") {
                $ussd_string = "Plant with the first effective rains (approximately 25-30 mm).\n";
                $ussd_string .= "Make a groove 5-6 cm deep on the middle of the ridge.\n";
                $ussd_string .= "Drop a single seed every 15 cm (75cm X 15cm X 1).\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "1") {
                $ussd_string = "1. Groundnut Rosette\n";
                $ussd_string .= "2. Early and Late leaf spots\n";
                $ussd_string .= "3. Rust\nPress n for disease control.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "2") {
                $ussd_string = "Please select pest for control:\n";
                $ussd_string .= "1. Termites\n";
                $ussd_string .= "2. Aphids \n";
                $ussd_string .= "3. Hilda spp";
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

            /* 
            *handle maize level 5
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n" && $option5 == "n" ) {
                $ussd_string = "Apply 100 kg basal fertilizer preferably 23:21:0 + 4s at 5gms / plant just after emegency.\n";
                $ussd_string .= "Keep the field weed free all the time.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "1" && $option5 == "n") {
                $ussd_string = "1.Early plant the entire field and pull up infected seedlings\n";
                $ussd_string .= "2.Early planting using resistant varieties and treated with Thiran and Benomyl\n";
                $ussd_string .= "3.Uprooting infected plants\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "2" && $option5 == "1") {
                $ussd_string = "1. Early planting, rotation of crops.\n";
                $ussd_string .= "2. Deeply ploughing the soil.\n";
                $ussd_string .= "3. Always weeding the plantation.\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "2" && $option5 == "2") {
                $ussd_string = "1. Collecting fall armyworms and killing them,\n";
                $ussd_string .= "2. Using pesticides like Cypermethrin 4%+profenofos 40% at 1-2 ml of pesticide in1 l of water,\n";
                $ussd_string .= "3. check whether there is no Fall armyworm outbreak.\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "1" && $option3 == "2" && $option4 == "2" && $option5 == "3") {
                $ussd_string = "1. Regular surveillance of the plantation\n";
                $ussd_string .= "2. Lambda-Cyhalothrin 50g/l, at 1-2 ml in 1l of water.\n";
                $ussd_string .= "3. Crop rotation of cereals with legumes or tubers\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
            }

            /* 
            * handle groundnuts level 5
            *
            */
            else if ($option1 == "1" && $option2 == "2" && $option3 == "1" && $option4 == "n" && $option5 == "n") {
                $ussd_string = "Farmers should apply single superphosphate before or at sowing at a rate of 100 kg/ha.\n";
                $ussd_string .= "Top dressing with Gypsum at a rate of 200 kg/ha when 30% of the plants have flowered.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "1" && $option5 == "n" ) {
                $ussd_string = "1. Early sowing and crop rotation with a cereal.\n";
                $ussd_string .= "2. Removing and destroying all volunteer crops.\n";
               $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "2" && $option5 == "1") {
                $ussd_string = "1. Deep ploughing. \n";
                $ussd_string .= "2. Applying pesticides such as Dursban,\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "2" && $option5 == "2") {
                $ussd_string = "1. Sowing early.\n";
                $ussd_string .= "2. Removing volunteer crops.\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
            }

            else if ($option1 == "1" && $option2 == "2" && $option3 == "2" && $option4 == "2" && $option5 == "3") {
                $ussd_string = "1. Keeping the groundnut field and area around the field free of weeds.\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
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

            /* 
            *handle maize level 6
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n" && $option5 == "n" && $option6 == "n") {
                $ussd_string = "Apply top dressing fertlizer like urea, when plants are 30cm high or 21 days after emergency.\n";
                $ussd_string .= "Press n to proceed.";
                ussd_proceed($ussd_string);
            }

            /* 
            *handle groundnuts level 6
            *
            *
            */
            else if ($option1 == "1" && $option2 == "2" && $option3 == "1" && $option4 == "n" && $option5 == "n" && $option6 == "n") {
                $ussd_string = "Harvest groundnut timely to avoid bleaching, dis-colouration of nuts \n";
                $ussd_string .= "Maturity can be checked by lifting a few pods and examining the inside of the shell.\nPress n to proceed.";
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

            /* 
            *handle maize level 7
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n" && $option5 == "n" && $option6 == "n" && $option7 == "n") {
                $ussd_string = "Harvest when the crop is at 18% moisture content and leave it to dry in granaries.\n";
                $ussd_string .= "Press n to proceed.";
                ussd_proceed($ussd_string);
            }

            /* 
            *handle groundnuts level 7
            *
            *
            */
            else if ($option1 == "1" && $option2 == "2" && $option3 == "1" && $option4 == "n" && $option5 == "n" && $option6 == "n" && $option7 == "n") {
                $ussd_string = "Remove groundnut from straws using hands or groundnut strippers.\n";
                $ussd_string .= "Store the groundnuts in pods and in well ventilated containers.\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
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

            /* 
            *handle maize level 8
            *
            *
            */
            if ($option1 == "1" && $option2 == "1" && $option3 == "1" && $option4 == "n" && $option5 == "n" && $option6 == "n" && $option7 == "n"&& $option8 == "n") {
                $ussd_string = "Shell and bag when the crop is at 12.5% moisture content. \nRemember to treat with pesticide to avoid weavil.\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }  
    }


    function animal_production($ussd_string_exploded) {

        /* 
        *handle chicken level 1
        *
        *
        */
        if (count($ussd_string_exploded) == 1) {
            $ussd_string = "Please Select: \n1. Chicken production";
            ussd_proceed($ussd_string);
        }

        /* 
        *handle chicken level 2
        *
        *
        */
        else if (count($ussd_string_exploded) == 2) {
            $option = $ussd_string_exploded[1];

            if ($option == "1") {
                $ussd_string = "Please Select:\n1. Layers \n2. Broilers";
                ussd_proceed($ussd_string);
            }

            else if ($option == "2") {
                $ussd_string = "Please Select:\n1. Sheep Feeds \n1. Pests and Diseases Control\n";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid response";
            } 
        }

        /* 
        *handle chicken level 3
        *
        *
        */
        else if(count($ussd_string_exploded) == 3) {
            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];

            /* 
            *handle maize level 3
            *
            *
            */
            if($option1 == "2" && $option2 == "1" && $option3 == "1") {
                $ussd_string = "Please Select: \n1. Layers Feeds \n2. Broilers Feeds";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }

        /* 
        *handle chicken level 4
        *
        *
        */
        else if (count($ussd_string_exploded) == 4) {
            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            $option4 = $ussd_string_exploded[3];

            if ($option1 == "2" && $option2 == "1" && $option3 == "1" && $option4 == "1") {
                $ussd_string = "Chicken mash:Given at day old and above for internal organs development.\n";
                $ussd_string .= "Each bird should consume approximately 1.1kg of feed during the period.\nPress n to proceed";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }

        /* 
        *handle chicken level 5
        *
        *
        */
        else if (count($ussd_string_exploded) == 5) {
            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            $option4 = $ussd_string_exploded[3];
            $option5 = $ussd_string_exploded[4];

            if ($option1 == "2" && $option2 == "1" && $option3 == "1" && $option4 == "1" && $option5 == "n" ) {
                $ussd_string = "Growers Mash: Given from 7 weeks to 18 weeks old";
                $ussd_string .= "Each bird should feed 4.9kg during this period.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }
 
        /* 
        *handle chicken level 6
        *
        *
        */
        else if (count($ussd_string_exploded) == 6) {

            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            $option4 = $ussd_string_exploded[3];
            $option5 = $ussd_string_exploded[4];
            $option6 = $ussd_string_exploded[5];

            if ($option1 == "2" && $option2 == "1" && $option3 == "1" && $option4 == "1" && $option5 == "n" && $option6 == "n") {
                $ussd_string = "Layers Mash: Given from 18 weeks up to Laying period.\n";
                $ussd_string .= "Each bird should feed 115 - 125 grams of feed.\nPress n to proceed.";
                ussd_proceed($ussd_string);
            }

            else {
                echo "Invalid input";
            }
        }

        /* 
        *handle chicken level 7
        *
        *
        */
        else if (count($ussd_string_exploded) == 7) {
            $option1 = $ussd_string_exploded[0];
            $option2 = $ussd_string_exploded[1];
            $option3 = $ussd_string_exploded[2];
            $option4 = $ussd_string_exploded[3];
            $option5 = $ussd_string_exploded[4];
            $option6 = $ussd_string_exploded[5];
            $option7 = $ussd_string_exploded[6];

            if ($option1 == "2" && $option2 == "1" && $option3 == "1" && $option4 == "1" && $option5 == "n" && $option6 == "n" && $option7 == "n") {
                $ussd_string = "Layers Concrete: Given to Chicken from 18 to the end of laying .\n";
                $ussd_string .= "Mix 2 parts Layers Concentrate to 3 parts maize by mass.\n";
                $ussd_string .= "*Thank You for using Ulimi Wathu*";
                ussd_stop($ussd_string);
            }
       
            else {
                echo "Invalid input";
            }
        }
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
