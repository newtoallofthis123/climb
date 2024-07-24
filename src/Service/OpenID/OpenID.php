<?php

namespace ClimbUI\Service\OpenID;

require_once __DIR__ . '/../../../support/lib/vendor/autoload.php';

use Approach\nullstate;
use Approach\Service\Service;
use Approach\Service\connectivity as ServiceConnectivity;
use Exception;

class OpenID extends Service{

	public array $oidc = []; 

	// use ServiceConnectivity;

	/**
		Do things you want to happen WHEN a connection is formed. Before transfering any major payload.
		For example: Header stuff, metadata, stream context profiles... 
	*/
	public function connect($register_connection = true) :nullstate
	{
		$oidc = self::get_oidc_info();

		$this->connected = false;

		if (empty($oidc)) {
			throw new Exception('OIDC information is empty');
		}

		if ($register_connection) {
			$this->register_connection();
		}

		// Set headers
		if (isset($oidc['access_token'])) {
			// Set the Authorization header with the Bearer token
			header('Authorization: Bearer ' . $oidc['access_token']);
		} else {
			throw new Exception('Access token is missing');
		}

		// Configure stream context profiles
        $contextOptions = [
            'http' => [
                'method' => 'GET',
                'header' => [
					'Authorization: Bearer ' . ($oidc['access_token'] ?? ''),
                    'Content-Type: application/json',
                    'User-Agent: ClimbUI OpenID Client'
				]
            ]
        ];
		foreach ($contextOptions['http']['header'] as $key => $value) {
			header($key.': '.$value);
		}
        $context = stream_context_create($contextOptions);

        // Assign context to be used in subsequent requests
        $this->context = $context;

		$this->connected = true;
		return nullstate::defined;
	}

	public static function get_oidc_info(){

		// Look in HTTP header for Authorization: Bearer [this string]
		// which will be the JWT (JSON Web Token)
		$oidc = [];
		$oidc['client_id'] = getenv('clientID');
        $oidc['client_secret'] = getenv('clientSecret');
        $oidc['redirect_uri'] = getenv('redirectUri');

		if( isset($_SERVER['HTTP_AUTHORIZATION']) ){

			$header = $_SERVER['HTTP_AUTHORIZATION'];
			
			// Make sure 'Bearer' is in the authorization
			if( !str_contains($header, ': Bearer ')){
				throw new Exception(('Authorization header set but Bearer token not found!'));
			}

			$accessToken = substr(
				$header,
				(
					strpos( $header, ' ') 
					+ 7 //1					// '+ 1' makes sure we skips over 'Bearer' string
				)
			);
			$oidc['access_token'] = $accessToken;
		} else {
			throw new Exception('Authorization header not set');
		}

		/**
		 * GET Parameters:
		 * 	code: Obtained during the OAuth authorization process, this parameter represents the authorization code given by the provider (e.g., Google, Facebook, etc.). Applications usually trade this temporary code for long-lived access tokens.
		 *	error: Returned when something goes wrong during the OAuth flow. Common error types involve incorrect redirection URIs, invalid scopes, or disabled applications.
		 *	state: Sent alongside the initial authorization request, this value serves as protection against CSRF attacks. Upon receiving a response containing a state parameter, compare it with the original one stored earlier. Matching values confirm authenticity while mismatched ones indicate tampering attempts.

		 * POST Variables:
		 * 	grant_type: Specifies the desired OAuth grant type used in the request (e.g., 'authorization_code', 'refresh_token'). Helps determine appropriate responses from the provider.
		 *	redirect_uri: Used both during authorization requests and token exchanges, this URI indicates where the provider should send the user following completion of the OAuth flow. Ensure consistency between requested and actual redirect URIs throughout the entire process
		 *	client_id: Unique identifier assigned by the provider to represent your application. Double-check the ID against known trusted sources since it directly ties to your app's credentials.
		 *	client_secret: Confidential secret shared between your application and the provider. Protect this value carefully, limiting exposure even during development phases. Use environment variables or secure vault services instead of hardcoding secrets into source files.
		 */


		// Check for GET and POST variables, like found after redirect_url step
		if(	isset( $_GET['code'] )){
			$oidc['code'] = $_GET['code'];
		}
		if(	isset( $_GET['error'] )){
			$oidc['error'] = $_GET['error'];
		}
		if(	isset( $_GET['state'] )){
			$oidc['state'] = $_GET['state'];
		}

		if( isset( $_POST['grant_type'] ) ){
			$oidc['grant_type'] = $_POST['grant_type'];
		}
		if( isset( $_POST['redirect_uri'] ) ){
			$oidc['redirect_uri'] = $_POST['redirect_uri'];
		}
		if( isset( $_POST['client_id'] ) ){
			$oidc['client_id'] = $_POST['client_id'];
		}
		if( isset( $_POST['client_secret'] ) ){
			$oidc['client_secret'] = $_POST['client_secret'];
		}
		
		return $oidc;
	}


}