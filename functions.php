<?php


define("MB", 1048576);

function filterRequest($requestname)
{
    return  htmlspecialchars(strip_tags($_POST[$requestname]));
}

function getAllData($table, $where = null, $values = null, $json = true)
{
    global $con;
    
    try {
        // Construct the SQL query based on the presence of a WHERE clause
        $sql = ($where === null) ? "SELECT * FROM $table" : "SELECT * FROM $table WHERE $where";
        
        // Prepare and execute the SQL query
        $stmt = $con->prepare($sql);
        $stmt->execute($values);
        
        // Fetch all results as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get the number of rows returned
        $count = count($data);
        
        // Prepare response
        $response = array();
        if ($count > 0) {
            $response['status'] = "success";
            $response['data'] = $data;
        } else {
            $response['status'] = "failure";
        }
        
        // Output as JSON if requested
        if ($json) {
            header('Content-Type: application/json');
            echo json_encode($response , JSON_BIGINT_AS_STRING);
        }
        
        // Return the result array for further use
        return $response;
    } catch (PDOException $e) {
        // Handle database error
        $errorResponse = array("status" => "error", "message" => $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode($errorResponse);
    }
}


function getData($table, $where = null, $values = null, $json = true)
{
    global $con;
    $data = array();
    $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    $stmt->execute($values);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    } else {
        return $count;
    }
}




function insertData($table, $data, $json = true)
{
    global $con;
    foreach ($data as $field => $v)
        $ins[] = ':' . $field;
    $ins = implode(',', $ins);
    $fields = implode(',', array_keys($data));
    $sql = "INSERT INTO $table ($fields) VALUES ($ins)";

    $stmt = $con->prepare($sql);
    foreach ($data as $f => $v) {
        $stmt->bindValue(':' . $f, $v);
    }
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}
 


/*function insertData($table, $data, $json = true)
{
    global $con;
    
    try {
        // Prepare the SQL statement
        $fields = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";

        // Prepare and execute the SQL statement using prepared statements
        $stmt = $con->prepare($sql);
        foreach ($data as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }
        $stmt->execute();

        // Get the number of affected rows
        $rowCount = $stmt->rowCount();

        // Prepare response data
        $response = array();
        if ($json) {
            if ($rowCount > 0) {
                $response['status'] = "success";
            } else {
                $response['status'] = "failure";
            }
            // Encode response as JSON and return as a string
            return json_encode($response);
        }

        // Return the number of affected rows
        return $rowCount;
    } catch (PDOException $e) {
        // Handle database errors
        // Log or handle the error as needed
        // Return an error response if required
        $response = array("status" => "error", "message" => $e->getMessage());
        if ($json) {
            // Encode error response as JSON and return as a string
            return json_encode($response);
        }
        return 0; // Return 0 if an error occurred
    }
}*/






function updateData($table, $data, $where, $json = true)
{
    global $con;
    $cols = array();
    $vals = array();

    foreach ($data as $key => $val) {
        $vals[] = "$val";
        $cols[] = "`$key` =  ? ";
    }
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";

    $stmt = $con->prepare($sql);
    $stmt->execute($vals);
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}

function deleteData($table, $where, $json = true)
{
    global $con;
    $stmt = $con->prepare("DELETE FROM $table WHERE $where");
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}

function imageUpload($dir, $imageRequest)
{
    global $msgError;
    if (isset($_FILES[$imageRequest])) {
        $imagename  = rand(1000, 10000) . $_FILES[$imageRequest]['name'];
        $imagetmp   = $_FILES[$imageRequest]['tmp_name'];
        $imagesize  = $_FILES[$imageRequest]['size'];
        $allowExt   = array("jpg", "png", "gif", "mp3", "pdf" , "svg" , "SVG");
        $strToArray = explode(".", $imagename);
        $ext        = end($strToArray);
        $ext        = strtolower($ext);

        if (!empty($imagename) && !in_array($ext, $allowExt)) {
            $msgError = "EXT";
        }
        if ($imagesize > 2 * MB) {
            $msgError = "size";
        }
        if (empty($msgError)) {
            move_uploaded_file($imagetmp,  $dir . "/" . $imagename);
            return $imagename;
        } else {
            return "fail";
        }
    }else {
        return 'empty' ; 
    }
}




function deleteFile($dir, $imagename)
{
    if (file_exists($dir . "/" . $imagename)) {
        unlink($dir . "/" . $imagename);
    }
}

function checkAuthenticate()
{
    if (isset($_SERVER['PHP_AUTH_USER'])  && isset($_SERVER['PHP_AUTH_PW'])) {
        if ($_SERVER['PHP_AUTH_USER'] != "wael" ||  $_SERVER['PHP_AUTH_PW'] != "wael12345") {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Page Not Found';
            exit;
        }
    } else {
        exit;
    }

    // End 
}


function   printFailure($message = "none") 
{
    echo     json_encode(array("status" => "failure" , "message" => $message));
}
function   printSuccess($message = "none") 
{
    echo     json_encode(array("status" => "success" , "message" => $message));
}
function result($count){
    if ($count == 0){
        printSuccess();
    }else{
        printFailure();
    }    
}

function sendEmail($to, $title, $body)
{
    $header = "From: osamahesham101@gmail.com " . "\r\n" .
              "CC: osamahesham101@gmail.com";
    if (mail($to, $title, $body, $header)) {
        echo json_encode(array("status" => "success", "message" => "Email sent successfully"));
    } else {
        echo json_encode(array("status" => "failure", "message" => "Failed to send email"));
    }
}
function sendGCM($title, $message, $topic, $pageid, $pagename) {
   
    $url = 'https://fcm.googleapis.com/fcm/send';

    
    $payload = [
        'to' => '/topics/' . $topic,
        'priority' => 'high',
        'content_available' => true,
        'notification' => [
            'body' => $message,
            'title' => $title,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'sound' => 'default'
        ],
        'data' => [
            'pageid' => $pageid,
            'pagename' => $pagename
        ]
    ];

  
    $fields = json_encode($payload);

    $headers = [
        'Authorization: key=' . "",
        'Content-Type: application/json'
    ];

 
    $ch = curl_init();


    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $fields
    ]);


    $result = curl_exec($ch);


    curl_close($ch);

   
    return $result;
}




function insertNotify($title, $body, $userid, $topic, $pageid, $pagename)
{
    global $con;
    $stmt  = $con->prepare("INSERT INTO `notification`(  `notification_title`, `notification_body`, `notification_userid`) VALUES (? , ? , ?)");
    $stmt->execute(array($title, $body, $userid));
    sendGCM($title,  $body, $topic, $pageid, $pagename);
    $count = $stmt->rowCount();
    return $count;
}


//where addcart fu