<?php 

//converts integers between 999,999,999 and -999,999,999 to strings
function convert_words_to_nums($words){
        
    $result = ""; //to store result
    $max_string_length = 1000;
    $temp_num = 0;
    $final_num = 0;
    $max_num = 999999999;
    $negative = false; //flag
    
    $number_dictionary = array(
        'negative' => '-',
        'zero' => 0, 'one' => 1, 'two' => 2, 'three' => 3,'four' => 4, 'five' => 5,
        'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10,
        'eleven' => 11, 'twelve' => 12, 'thirteen' => 13, 'fourteen' =>14,
        'fifteen' =>15, 'sixteen' => 16, 'seventeen' =>17, 'eighteen' =>18,
        'nineteen' => 19,'twenty' => 20, 'thirty' => 30, 'forty' => 40, 
        'fifty' => 50, 'sixty' => 60, 'seventy' => 70, 'eighty' =>80,
        'ninety' => 90, 'hundred' => 100, 'thousand' => 1000, 'million' => 1000000
    );
    
    $ones = array(
        'negative' => '-',
        'zero' => 0, 'one' => 1, 'two' => 2, 'three' => 3,'four' => 4, 'five' => 5,
        'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10,
        'eleven' => 11, 'twelve' => 12, 'thirteen' => 13, 'fourteen' =>14,
        'fifteen' =>15, 'sixteen' => 16, 'seventeen' =>17, 'eighteen' =>18,
        'nineteen' => 19
    );
    
    $tens = array(
        'twenty' => 20, 'thirty' => 30, 'forty' => 40, 
        'fifty' => 50, 'sixty' => 60, 'seventy' => 70, 'eighty' =>80,
        'ninety' => 90
    );
    
    //Conditions for user input
    if(strlen($words) > $max_string_length){
        return "<p>Sorry your input is too long!</p>";
    }
    elseif(preg_match("/[0-9]+/",$words)){
        return $result .="<p>Please enter only words for optimal translation :)</p>";
    }
    else {
        $result .= "<p>You entered: <div id=\"words\">".$words."</div>
            <br/><br/></p>";
    }
    
    //$words = strtr($words, $number_dictionary); 
        //strtr() converts "done" to "d1", so we use string_translate() instead
    $words = string_translate($words, $number_dictionary);
    $words = preg_split("/(\,)|(\.)+/i", $words, -1, 
                            PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY); 
        
    $result .= "<p>The translation is: ";
    
    //loop through entire string
    foreach($words as $val){
        $temp_num = 0;
        $final_num = 0;
        $negative = false;
        
        //preg_split by space
        $val = preg_split("/\s+/i",$val, -1, PREG_SPLIT_NO_EMPTY); 
        
        //loop through each word
        foreach($val as $key => $translate){
            
            //check if negative or not numeric
            if($translate == '-' && $negative == false){
                $result .= $translate; 
                $negative = true; //add only one negative per number
                continue;
            }
            elseif(!is_numeric($translate)){
                //echo "not numeric..skipping to next number  <br/>";
                if($final_num == 0 && $temp_num != 0){
                    //output word
                    $result .= $temp_num." ";
                    $temp_num = 0;
                }
                elseif($final_num != 0){
                    //previous number was stored, so output the number first
                    $result .= bcadd($final_num, $temp_num)." ";
                    $temp_num = 0;
                    $final_num = 0;
                }
                $result .= $translate." ";
                continue;
            }
            
            //case: $translate < 100 ?
            if(bccomp($translate, 100) == -1 ){
                $temp_num = bcadd($temp_num,  $translate); 
                
                //check some invalid input conditions
                if( isset($val[$key + 1]) ){
                    if(in_array($translate, $ones) && (in_array($val[$key + 1], $ones)
                            || in_array($val[$key+1],$tens) ) ){
                        //two ones repeat
                        //invalid user syntax, so let us start new number 
                        $result .= bcadd($final_num, $temp_num)." ";
                        $temp_num = 0;
                        $final_num = 0;
                    }
                    elseif(in_array($translate, $tens) &&  in_array($val[$key + 1], $tens) ){
                        //two tens repeat
                        //invalid user syntax, so let us start new number
                        $result .= bcadd($final_num, $temp_num)." ";
                        $temp_num = 0;
                        $final_num = 0;
                    }
                }
                continue;
            }
            
            //case: $translate == 100 ?
            if(bccomp($translate, 100) == 0 ){
                $temp_num = bcmul($temp_num, $translate);
                
                //check if two multiples are repeated
                if( isset($val[$key + 1]) ){
                    if( $translate == $val[$key + 1]  ){
                        //invalid user syntax 
                        $result .= bcadd($final_num, $temp_num)." [invalid input] ";
                        $temp_num = 0;
                        $final_num = 0;
                    }
                }
                continue;
            }
            
            //case: greater than 100 (e.g. 1000, or million)
            $temp_num = bcmul($temp_num, $translate);
            
            //check if OVERFLOW
            if(bcadd($final_num,$temp_num) > $max_num){
                return "<p>Overflow, check your input (put some commas)
                    !! Max number is 999,999,999</p>";
                /*$final_num = $temp_num;
                $result .= "[overflow]:".$final_num." ";
                $final_num = 0;*/
            }
            else{
                //nothing wrong, so add final_num
                $final_num = bcadd($final_num,$temp_num);
            }

            $temp_num = 0; //restart process
            
            //check if two multiples are repeated
            if( isset($val[$key + 1]) ){
                if( $translate == $val[$key + 1]  ){
                    //invalid user syntax, so let us start new number 
                    $result .= bcadd($final_num, $temp_num)." [invalid input] ";
                    $temp_num = 0;
                    $final_num = 0;
                }
            }

        }//end foreach
        
        //add number to result
        if(is_numeric($translate)) {
            $final_num = bcadd($final_num, $temp_num);
            $result .= $final_num;
        }
        
    }//we looped through whole string
    
    $result .= "</p>";
    return $result;
}




// translate each word to its corresponding number
function string_translate($string, $number_dictionary){
    
    $string = strtolower($string);
    
    //for comparison
    //print_r(strtr($string, $number_dictionary)); 

    $string = preg_split("/(\,)|(\?)|(\!)|(\.)|(\s)+/",$string,-1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    
    foreach($string as $key => $val){
        if(array_key_exists($val,$number_dictionary) == true){
            $string[$key] = $number_dictionary[$val];
        }
    }
    $string = implode($string);
    //print_r($string);
    
    return $string;
}

?>