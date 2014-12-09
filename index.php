<?php 
/*
 * @author btaba
 * February 2, 2014
 * 
 */
 
header('Content-Type: text/html; charset=utf-8'); 
include 'php/fibonacci.php';
include 'php/number_translator.php';

$debug = false;
ini_set("max_execution_time",15); 

if(!$debug){
    error_reporting(0); // turn off error reporting
    register_shutdown_function('shutdown'); 
}

function shutdown() 
{ 
       $error = error_get_last();
       //print_r($error);
       
       if ($error['type'] === E_ERROR && preg_match("/^Maximum execution time/",$error['message'])
               && preg_match("/fibonacci\.php$/",$error['file']) ) {
           echo "<p>"."Try a smaller number. This computer is slow :("."</p>";
       }elseif(!is_null($error)){
           echo "<p>"."There was an error. Please contact admin."."</p>";
       }
       
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="css/default.css">
        <title>PHP Stuff</title>
    </head>
    <body>
        
        <h1>Strings to Numbers Translator</h1>
        <form method="post" action="index.php">
            Write some numbers in English separated by commas:<br/>
            <textarea rows="5" cols="80" name="num_translate"><?php 
                if(isset($_POST['num_translate'])){
                    echo htmlspecialchars($_POST['num_translate']);
                }
                else{
                    echo "six, negative seven hundred twenty nine, one million one hundred one";
                }?></textarea>
            <br/>*Enter "negative" for negative numbers.
            <br/><input id="go" type="submit" value="Translate!">
        </form>
        
        <div id="exercise">
            <?php
                if(isset($_POST['num_translate'])){
                    $words = htmlspecialchars($_POST['num_translate']);
                    echo "<br/>".convert_words_to_nums($words);
                }//end if
            ?>
        </div>
        
        
        <br/><br/>
        
        <h1> Fibonacci Nums </h1>
        <form method="post" action="index.php">
            Input numbers smaller than 5000:<br/>
            <textarea rows="4" cols="50" name="fib_seq"><?php 
                if(isset($_POST['fib_seq'])){
                    echo htmlspecialchars($_POST['fib_seq']);
                }else{echo "5, 7, 11";} 
            ?></textarea>
            <br/><input id="go" type="submit" value="GO!">
        </form>
        
        <br/>
        
        <div id ="exercise">
            <?php
                if(isset($_POST['fib_seq'])){

                    $numbers = $_POST['fib_seq'];
                    $pattern = "/^\s*(?:[0-9]*[\s|,]*){20}$/";
                    
                    if(preg_match($pattern, $numbers)){
                        echo "<p>Here are the Fibonacci numbers for the value(s) you provided:</p>";
                        $numbers = preg_split("/[\s\n,]+/", $numbers, -1, PREG_SPLIT_NO_EMPTY);
                        //print_r($numbers);
                        
                        foreach($numbers as $key=>$val)
                        {
                            if($val > 5000){
                                echo '<p>'.$val." is greater than 5000. 
                                    Sorry cannot compute :(".'</p>';
                            } 
                            else{
                                echo '<p>'.$val.' -> '.fibonacci($val).'<br/>'.'</p>';
                            }
                            
                        }
                    }
                    else{
                        echo "<p>Input Invalid! Please input up to 20 INTEGERS 
                            separated by whitespace or commas.</p>";
                    }
                    
                }//end if
            ?>
        </div>
        
    </body>
</html>
