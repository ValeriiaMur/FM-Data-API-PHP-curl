require_once 'DATA_API.php';
$token = get_token();

if (empty($token) or (strpos($token, "Err") !== false)) {
  //display error
  echo $token . '<br/>';
  //exit;
}

//get all records fm data api
$method = "POST";
$path = "/layouts/" . $layoutName . "/_find/";
$arr = [];
$query_array = array(
  $findFieldName => $findFieldValue,
  "includeforweb" => "yes"
);
array_push($arr, $query_array);
$body1 = array('query' => $arr);

$arr = [];
$sort_array1 = array(
  "fieldName" => "crnumber",
  "sortOrder" => "ascend"
);
$sort_array2 = array(
  "fieldName" => "version",
  "sortOrder" => "ascend"
);
array_push($arr, $sort_array1);
array_push($arr, $sort_array2);
$body2 = array('sort' => $arr);
$body3 = array('limit' => 1000);

$json_body = json_encode(array_merge($body1, $body2, $body3));
//api call returns 0 if OK or err text
$json_response = api_call($token, $method, $json_body, $path);
$res = $json_response['messages'][0]['code'];
if ($res == "0") {
  $data = $json_response['response']['data'];
  //var_dump($data);
  $total = $json_response['response']['dataInfo']['returnedCount'];
} else {
  echo "<p>Error: " . $res . "</p>";
  exit;
}
delete_token($token);