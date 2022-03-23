//Assuming this is called from FM with custom params:
//http://website.com/?client_id=123&response=accept
<?php 
$client_id = $_GET[“client”];
$response = $_GET[“response”];

//init FM
require_once 'FileMaker.php';
$fm = new FileMaker($database = "", $hostspec = ", $username = "", $password = "");

$isError = FileMaker::isError ($fm);
if ($isError) {
	echo 'Problem logging on:' . $fm->message . '(' . $layouts->code . ')<br/>';
	exit;
}

function redirect($url) {
    ob_start();
    header('Location: '.$url);
    ob_end_flush();
    die();
}

$layout = "";
$script = "";
$parameter = $client_id ."|". $response;

$newPerformScript = $fm->newPerformScriptCommand($layout, $script, $parameter);
$result = $newPerformScript->execute(); 
if (FileMaker::isError($result)) {
    //error
    // echo 'Problem logging on:' . $result->getMessage() . '(' . $result->getCode() . ')<br/>';
    // exit;
    redirect("http://website.com/result.php?result=failure");
} else {
    redirect("http://website.com/result.php?result=success");
}


?>

//result.php
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HTML 5 Boilerplate</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <h1>Result</h1>
    <?php
    $result = htmlspecialchars($_GET[“result”]);
    if ($result == "success") {
        echo "<p>Thank you for your response!</p>";
    } else {
        echo "<p>There was a problem with your response. Please try again.</p>";
    }
    ?>
	<script src="index.js"></script>
  </body>
</html>

<?php

?>