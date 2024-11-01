<?php
/**
 * Twitch API Class
 *
 * @since       2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SWTI_Twitch_API' ) ) {

	class SWTI_Twitch_API extends Streamweasels_Admin {

		private $api_url = 'https://api.twitch.tv/helix/';
		private $token_url = 'https://id.twitch.tv/oauth2/token';
		private $game_url = 'https://api.twitch.tv/helix/games?name=';
		private $team_url = 'https://api.twitch.tv/helix/teams?name=';
		private $channel_url = 'https://api.twitch.tv/helix/users?login=';
		private $client_id;
		private $client_secret;
		private $auth_token;
		private $game;
		private $token;
		private $nonceCheck;
		private $debug = false;

		public function __construct() {
			$options = get_option('swti_options');
			$this->client_id = (!empty($options['swti_client_id'])) ? $options['swti_client_id'] : '';
			$this->client_secret = (!empty($options['swti_client_secret'])) ? $options['swti_client_secret'] : '';
			$this->auth_token = (!empty($options['swti_api_access_token'])) ? $options['swti_api_access_token'] : '';
			$this->game = (!empty($options['swti_game'])) ? $options['swti_game'] : '';
			$this->token = (!empty($options['swti_api_access_token'])) ? $options['swti_api_access_token'] : '';
			$this->nonceCheck = (!empty($options['swti_nonce_check'])) ? $options['swti_nonce_check'] : false;
		}

		public function enable_debug_mode() {
			$this->debug = true;
		}

		public function get_game_id($game) {

			$gameId = get_transient( 'swti_game_'.$game );

			if ( $gameId !== false ) {
				$this->swti_twitch_debug_field('Game ID found in cache - '.$gameId);
				return esc_attr($gameId);
			}			

			$headers = [
				'Content-Type' => 'application/json',
				'Client-id' => $this->client_id,
				'Authorization' => 'Bearer '.$this->token				
			];

			$response = wp_remote_get( $this->game_url.$game, [
				'headers' => $headers,
				'timeout' => 15
			]);			

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$result = wp_remote_retrieve_body( $response );
			$result = json_decode( $result, true );	
			$gameId = $result['data'][0]['id'];

			if ( $result === false || !isset( $result['data'][0] ) ) {
				$this->swti_twitch_debug_field('Game ID not found for - '.$game);
				$this->swti_twitch_debug_field($result);
				return false;
			}

			set_transient( 'swti_game_'.$game, sanitize_text_field($gameId), 86400 );

			return esc_attr($gameId);
		}


		public function get_team_channels($team) {

			$teamChannels = get_transient( 'swti_team_'.$team );

			if ( $teamChannels !== false ) {
				$teamChannelsCount = count(explode(',',$teamChannels));
				$this->swti_twitch_debug_field('Team found in cache - '.$teamChannelsCount. ' members');
				return esc_attr($teamChannels);
			}

			$headers = [
				'Content-Type' => 'application/json',
				'Client-id' => $this->client_id,
				'Authorization' => 'Bearer '.$this->token				
			];

			$response = wp_remote_get( $this->team_url.$team, [
				'headers' => $headers,
				'timeout' => 15
			]);			

			if ( is_wp_error( $response ) ) {
				$this->swti_twitch_debug_field($response);
				return false;
			}

			$result = wp_remote_retrieve_body( $response );
			$result = json_decode( $result, true );	

			if ( $result === false || !isset( $result['data'][0]['users'] ) ) {
				return false;
			}

			$users = $result['data'][0]['users'];
			$userList = '';
			foreach ($users as $user) {
				$userList .= $user['user_login'].',';
			}
			$userList = substr($userList, 0, -1);

			set_transient( 'swti_team_'.$team, sanitize_text_field($userList), 86400 );

			return esc_attr($userList);
		}

		public function get_channel_id($channel) {

			$channelId = get_transient( 'swti_channel_'.$channel );

			if ( $channelId !== false ) {
				return esc_attr($channelId);
			}

			$headers = [
				'Content-Type' => 'application/json',
				'Client-id' => $this->client_id,
				'Authorization' => 'Bearer '.$this->token				
			];

			$response = wp_remote_get( $this->channel_url.$channel, [
				'headers' => $headers,
				'timeout' => 15
			]);			

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$result = wp_remote_retrieve_body( $response );
			$result = json_decode( $result, true );	

			if ( $result === false || !isset( $result['data'][0]['id'] ) ) {
				$this->debug_log($result);
				return false;
			}

			$channelId = $result['data'][0]['id'];
			set_transient( 'swti_channel_'.$channel, sanitize_text_field($channelId), 86400 );

			return esc_attr($channelId);
		}	
		
		public function get_channel_ids($channels) {
			$channelIds = [];
		
			foreach ($channels as $channel) {
				$channelId = get_transient('swti_channel_' . $channel);
		
				if ($channelId === false) {
					$headers = [
						'Content-Type' => 'application/json',
						'Client-id' => $this->client_id,
						'Authorization' => 'Bearer ' . $this->token
					];
		
					$response = wp_remote_get($this->channel_url . $channel, [
						'headers' => $headers,
						'timeout' => 15
					]);
		
					if (is_wp_error($response)) {
						continue; // Skip this channel if there's an error
					}
		
					$result = wp_remote_retrieve_body($response);
					$result = json_decode($result, true);
		
					if ($result === false || !isset($result['data'][0]['id'])) {
						$this->debug_log($result);
						continue; // Skip this channel if ID retrieval fails
					}
		
					$channelId = sanitize_text_field($result['data'][0]['id']);
					set_transient('swti_channel_' . $channel, sanitize_text_field($channelId), 86400);
				}
		
				$channelIds[] = $channelId;
			}
		
			return implode(',', $channelIds);
		}		

		public function refresh_token() {
            delete_transient( 'swti_twitch_token' );
            delete_transient( 'swti_twitch_token_expires' );
		}

		public function get_token($clientId="",$clientSecret="") {
			$token = get_transient( 'swti_twitch_token' );
            $expires = get_transient( 'swti_twitch_token_expires' );
			$clientIdVar = ($clientId !== '' ? $clientId : $this->client_id);
			$clientSecretVar = ($clientSecret !== '' ? $clientSecret : $this->client_secret);

			if ( $token !== false ) {
				return array($token, $expires);
			}

			$args = [
				'client_id' => $clientIdVar,
				'client_secret' => $clientSecretVar,
				'grant_type' => 'client_credentials'
			];

			$headers = [
				'Content-Type' => 'application/json'
			];

			$response = wp_remote_post( $this->token_url, [
				'headers' => $headers,
				'body'    => wp_json_encode( $args ),
				'timeout' => 15
			]);

			if ( is_wp_error( $response ) ) {
				$this->swti_twitch_debug_field($response);
				return array('error');
			}

			$result = wp_remote_retrieve_body( $response );
			$result = json_decode( $result, true );

			if ( $result === false || !isset( $result['access_token'] ) ) {
				delete_transient( 'swti_twitch_auth_token' );
				delete_transient( 'swti_twitch_auth_token_expires' );
				$this->swti_twitch_debug_field($result);
				return array('error', $result['message']);
			}
			
			$token = $result['access_token'];
            $expires = $result['expires_in'];
			$today = time();
			$todayPlusExpires = $today + $expires;
			$expiresDate = date('F j, Y', $todayPlusExpires);

			set_transient( 'swti_twitch_token', sanitize_text_field($token), $result['expires_in'] - 30 );
            set_transient( 'swti_twitch_token_expires', sanitize_text_field($expiresDate), $result['expires_in'] - 30 );

			return array(esc_attr($token), esc_attr($expiresDate));
		}

		public function swti_fetch_streams(WP_REST_Request $request) {

			$nonce = $request->get_header('X-WP-Nonce');
			
			if (!$this->nonceCheck) {
				if (!wp_verify_nonce($nonce, 'wp_rest')) {
					$this->swti_twitch_debug_field('Nonce verification failed - Fetch Streams. Nonce: ' . ($nonce ? $nonce : 'empty'));
					return new WP_REST_Response('Nonce verification failed', 403);
				}
			}
	
			$authToken = $this->auth_token;
			$clientId = $this->client_id;
			$baseUrl = "https://api.twitch.tv/helix/streams";
			
			$queryParams = [];
			$game = $request->get_param('game_id');
			if (!empty($game)) {
				$queryParams['game_id'] = $game;
				$queryParams['first'] = '100'; // Default value for 'first' if 'game' is present
			}
	
			$pagination = $request->get_param('after');
			if (!empty($pagination)) {
				$queryParams['after'] = $pagination;
			}
	
			$channels = $request->get_param('user_login');
			if (!empty($channels)) {
				$userLoginArray = explode(',', $channels);
				foreach ($userLoginArray as $login) {
					$queryParams['user_login'][] = $login; // Append each login to 'user_login' array
				}
			}
	
			$language = $request->get_param('language');
			if (!empty($language)) {
				$queryParams['language'] = strtolower($language);
			}
			$queryString = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
			$url = $baseUrl . '?' . $queryString;
	
			$response = wp_remote_get($url, array(
				'headers' => array(
					'Client-ID' => $clientId,
					'Authorization' => 'Bearer ' . $authToken,
				),
			));
	
			$errorResponse = $this->swti_handle_twitch_errors($response, $url, 'Fetch Streams');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}
	
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
		}

		public function swti_fetch_video(WP_REST_Request $request) {

			if (!$this->nonceCheck) {
				if (!wp_verify_nonce($nonce, 'wp_rest')) {
					$this->swti_twitch_debug_field('Nonce verification failed - Fetch Video. Nonce: ' . ($nonce ? $nonce : 'empty'));
					return new WP_REST_Response('Nonce verification failed', 403);
				}
			}		
	
			$authToken = $this->auth_token;
			$clientId = $this->client_id;
			$baseUrl = "https://api.twitch.tv/helix/";
			$params = $request->get_params();
			
			if (!empty($params['clip_type'])) {
				switch ($params['clip_type']) {
					case 'clips':
						$endpoint = 'clips';
						break;
					case 'videos':
						$endpoint = 'videos';
						break;
					default:
						$endpoint = 'clips'; // Default to clips if not specified or recognized
				}
			}
	
			unset($params['clip_type']);
			$queryString = http_build_query($params);
			$url = $baseUrl . $endpoint . '?' . $queryString;
	
			$response = wp_remote_get($url, array(
				'headers' => array(
					'Client-ID' => $clientId,
					'Authorization' => 'Bearer ' . $authToken,
				),
			));
	
			$errorResponse = $this->swti_handle_twitch_errors($response, $url, 'Fetch Video');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}
	
			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
		}	
		
		function swti_fetch_users(WP_REST_Request $request) {

			if (!$this->nonceCheck) {
				if (!wp_verify_nonce($nonce, 'wp_rest')) {
					$this->swti_twitch_debug_field('Nonce verification failed - Fetch Users. Nonce: ' . ($nonce ? $nonce : 'empty'));
					return new WP_REST_Response('Nonce verification failed', 403);
				}
			}		
	
			$authToken = $this->auth_token;
			$clientId = $this->client_id;
			$baseUrl = "https://api.twitch.tv/helix/users/";
			$queryParams = [];
	
			$channels = $request->get_param('login');
			if (!empty($channels)) {
				$userLoginArray = explode(',', $channels);
				foreach ($userLoginArray as $login) {
					$queryParams['login'][] = $login; // Append each login to 'login' array
				}
			}
	
			$queryString = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
			$url = $baseUrl . '?' . $queryString;
	
			$response = wp_remote_get($url, array(
				'headers' => array(
					'Client-ID' => $clientId,
					'Authorization' => 'Bearer ' . $authToken,
				),
			));
	
			$errorResponse = $this->swti_handle_twitch_errors($response, $url, 'Fetch Users');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}

			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
		}		

		function swti_fetch_games(WP_REST_Request $request) {

			if (!$this->nonceCheck) {
				if (!wp_verify_nonce($nonce, 'wp_rest')) {
					$this->swti_twitch_debug_field('Nonce verification failed - Fetch Games. Nonce: ' . ($nonce ? $nonce : 'empty'));
					return new WP_REST_Response('Nonce verification failed', 403);
				}
			}		
	
			$authToken = $this->auth_token;
			$clientId = $this->client_id;
			$baseUrl = "https://api.twitch.tv/helix/games/";
			$queryParams = [];
	
			$gameIDs = $request->get_param('id');
			if (!empty($gameIDs)) {
				$userLoginArray = explode(',', $gameIDs);
				foreach ($userLoginArray as $login) {
					$queryParams['id'][] = $login; // Append each login to 'user_login' array
				}
			}
	
			$queryString = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
			$url = $baseUrl . '?' . $queryString;
	
			$response = wp_remote_get($url, array(
				'headers' => array(
					'Client-ID' => $clientId,
					'Authorization' => 'Bearer ' . $authToken,
				),
			));
	
			$errorResponse = $this->swti_handle_twitch_errors($response, $url, 'Fetch Games');
			if ($errorResponse instanceof WP_REST_Response) {
				return $errorResponse;
			}

			$body = wp_remote_retrieve_body($response);
			$data = json_decode($body);
	
			return new WP_REST_Response($data, 200);
		}

		private function swti_handle_twitch_errors($response, $url, $context = 'Fetch Streams') {
			// Check for errors in the response
			if (is_wp_error($response)) {
				$this->swti_twitch_debug_field('WP Error received on the following URL: ' . $url);
				return new WP_REST_Response($response->get_error_message(), 500);
			}
	
			$response_code = wp_remote_retrieve_response_code($response);
			if ($response_code != 200) {
				$this->swti_twitch_debug_field($context . ' returned status code: ' . $response_code);
				$this->swti_twitch_debug_field($context . ' request URL: ' . $url);
				$body = wp_remote_retrieve_body($response);
				$data = json_decode($body, true);
				$errorMessage = $data['message'] ?? 'No message received...';
				$this->swti_twitch_debug_field($context . ' returned error message: ' . $errorMessage);
				return new WP_REST_Response("Error in " . $context . ': ' . $response_code . " - " . $errorMessage, $response_code);
			}

		}		

		private function debug_log( $data ) {
			if ( !$this->debug ) {
				return;
			}
			$this->swti_twitch_debug_log( $data );
		}
	}
}