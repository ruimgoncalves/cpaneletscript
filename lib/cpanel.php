<?php

namespace CPanel;

/**
 * UAPI for CPanel
 */
class CPanel
{
  private $host;
  private $username;
  private $password;
  private $response;

  function __construct($data)
  {
    $this->host = $data['host'];
    $this->username = $data['username'];
    $this->password = $data['password'];
  }

  function query($module, $func, $funcparams = []){
    // Create new curl handle
		$ch = curl_init();

    $headers = [
      "Authorization: Basic " . base64_encode($this->username . ":" . $this->password)
    ];

    $params = array_merge([
      'cpanel_jsonapi_apiversion' => 3,
      'cpanel_jsonapi_module' => $module,
      'cpanel_jsonapi_func' => $func
    ], $funcparams);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $this->host . '/json-api/cpanel');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies); // Save cookies to
    curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_TIMEOUT, 100020);
    curl_setopt($ch, CURLOPT_BUFFERSIZE, 131072);
		// Execute the curl handle and fetch info then close streams.
    try {
      $this->response = curl_exec($ch);
  		//$h = curl_getinfo($ch);
      return json_decode($this->response)->result;
    } catch (Exception $e) {
      return false;
    } finally {
      curl_close($ch);
    }


  }

}
