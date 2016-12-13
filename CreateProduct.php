<?php
  function CreateCurlRequest($URI, $ProductArray)
  {
    // Create curl object
    $ch = curl_init();
    curl_setopt_array($ch, array (CURLOPT_URL => $URI,
                                  CURLOPT_RETURNTRANSFER => true,
                                  CURLOPT_HEADER => true,
                                  CURLOPT_CUSTOMREQUEST => "POST",
                                  CURLOPT_POSTFIELDS => $ProductArray,
                                  CURLOPT_SSL_VERIFYPEER => false,
                                  CURLOPT_HTTPHEADER => array("Content-Type: application/json; charset=utf-8")));

    return $ch;
  }
  
  function ImageToBase64($FileName)
  {
    if (!isset($_FILES[$FileName]) || $_FILES[$FileName] == null)
      return '';
    $File = $_FILES[$FileName];


    // Handy image to base64 from question asker: http://stackoverflow.com/q/19335477
    // modified to fix bugs and avoid needless processing
    $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
    $file_name = $File['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

    $file_size = $File['size'];
    $file_tmp = $File['tmp_name'];
    
    //.    Extension not allowed                      File too big (>2MB)
    if(in_array($file_ext,$allowed_ext) === false || $file_size > 2097152)
      return '';

    $type = pathinfo($file_tmp, PATHINFO_EXTENSION);
    $data = file_get_contents($file_tmp);
    return base64_encode($data);
  }
  
  
  function PostProduct($Title, $ImageB64, $Variant, $Description, $Price, $Type, $StockKeepingUnit = null)  
  {
    if ($StockKeepingUnit == null)
      $StockKeepingUnit = uniqid();
    
    $products_array = array("product" => array("title" => $Title,
                                               "body_html" => $Description,
                                               "vendor" => "URG",
                                               "product_type" => $Type,
                                               "variants" => array(array("option1" => $Variant,
                                                                         "price" => $Price,
                                                                         "sku" => $StockKeepingUnit)),
                                               "images" => array(array("attachment" => $ImageB64))));
    
    $url = "https://7bd03f2b1bb33dd5140c0617f5bc9aba:8cf5138ac9b70fd47ddb28459f91f365@urgexamplestore.myshopify.com/admin/products.json";
    $ch = CreateCurlRequest($url, json_encode($products_array));
    
    // Execute and grab result + header
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    curl_close($ch);

    $Result = json_decode($body, true);
  }
    
  $errorMSG = '';
  $title = '';
  $variant = '';
  $description = '';
  $price = '';
  $type = '';
  $sku = '';
  
  // Check required fields
  if (isset($_POST['t']) && $_POST['t'] != ''
   && isset($_POST['v']) && $_POST['v'] != ''
   && isset($_POST['d']) && $_POST['d'] != ''
   && isset($_POST['p']) && $_POST['p'] != ''
   && isset($_POST['ty']) && $_POST['ty'] != '')
  {
    // Assuming user is trusted and shopify does any security checks required
    if (isset($_POST['sku']) && $_POST['sku'] == '')
      $sku = null;
    else
      $sku = $_POST['sku'];
    
    //            Title            Image.            Variant    Description    Price        Type      Stock Keeping Unit
    PostProduct($_POST['t'], ImageToBase64('img'), $_POST['v'], $_POST['d'], $_POST['p'], $_POST['ty'], $sku);
    
    // Stop the rest of the webpage from displaying
    die("OK");
  }
  else // Error
    die("Please fill out all required fields. * = Required");
