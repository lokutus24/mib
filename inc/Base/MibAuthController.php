<?php

/**
 * Mib
 */
namespace Inc\Base;

use Inc\Base\MibBaseController;

class MibAuthController extends MibBaseController
{

	public function loginToMib()
	{

		$args = array(
			'body' => json_encode(['email' => $this->mibOptions['mib-login-email'], 'password' => $this->mibOptions['mib-login-password']]),
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'cookies' => array()
		);

		$response = wp_remote_post($this->loginUrl, $args);


		if (is_wp_error($response)) {

			$response_message = json_decode($response->get_error_message());
			$this->mibOptions['token'] = '';
			$this->mibOptions['expiry'] = '';
			update_option('mib_options', $this->mibOptions);

		} else {

			$response_message = json_decode(wp_remote_retrieve_body($response));

			if (isset($response_message->token)) {

				$now = new \DateTime();
				$now->modify('+1 hour');

				$this->mibOptions['token'] = $response_message->token;
				$this->mibOptions['expiry'] = $now->format('Y-m-d H:i:s');
				update_option('mib_options', $this->mibOptions);
			} else {
				echo "Sikertelen Mib bejelentkezés";
				error_log('Sikertelen Mib bejelentkezés');
				//die();
			}
		}

		return $response_message;
	}

	public function getOptionDatas()
	{
		return $this->mibOptions;
	}

	public function checkExpireToken($expiryDate)
	{
		$expired = false;
		$now = new \DateTime();
		$expiry = \DateTime::createFromFormat('Y-m-d H:i:s', $expiryDate);

		if (!$expiry) {
			return true; // vagy hamis dátum esetén kezelhetjük úgy, mintha lejárt volna
		}

		$diff = $now->diff($expiry);

		if ($diff->invert == 1 || $diff->days > 0 || $diff->h > 0 || ($diff->h == 0 && $diff->i >= 30)) {
			$expired = true;
		}

		return $expired;
	}

	public function getApartments()
	{
		$useFallback = false;
		$all_data = [];
		$per_page = 50;
		$page = 1;

		echo 'sdsds';
		if (!empty($this->mibOptions['mib-login-email']) && !empty($this->mibOptions['mib-login-password']) && !empty($this->mibOptions['token'])) {

			$headers = ['Authorization' => "Bearer {$this->mibOptions['token']}"];

			do {
				$url = add_query_arg([
					'page' => $page,
					'perPage' => $per_page,
				], $this->filterApartmentsUrl);

				$response = wp_remote_get($url, ['headers' => $headers]);

				if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
					error_log('MIB getApartments Primary API failed. Switching to fallback.');
					$useFallback = true;
					$all_data = []; // Reset data
					break;
				}

				$body = wp_remote_retrieve_body($response);
				$json = json_decode($body);

				$items = (is_object($json) && isset($json->data) && is_array($json->data)) ? $json->data : [];

				$all_data = array_merge($all_data, $items);
				$count_on_page = is_array($items) ? count($items) : 0;

				$page++;

			} while ($count_on_page === $per_page);
		} else {
			$useFallback = true;
		}

		if ($useFallback) {

			$fallbackUrl = $this->mibOptions['mib-api-url'] ?? '';
			$fallbackKey = $this->mibOptions['mib-api-key'] ?? '';

			if (!empty($fallbackUrl) && !empty($fallbackKey)) {
				$page = 1;
				// Reset data just in case
				$all_data = [];
				$headers = ['X-API-KEY' => $fallbackKey];

				do {
					$url = add_query_arg([
						'page' => $page,
						'perPage' => $per_page,
					], $fallbackUrl);

					error_log("MIB getApartments Fallback URL: " . $url);

					$response = wp_remote_get($url, ['headers' => $headers, 'sslverify' => false]);

					if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
						error_log('MIB getApartments Fallback API failed: ' . (is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_response_code($response)));
						break;
					}

					$body = wp_remote_retrieve_body($response);
					error_log("MIB getApartments Fallback Body Length: " . strlen($body));
					$json = json_decode($body);

					if (is_array($json)) {
						$items = $json;
					} elseif (is_object($json) && isset($json->data) && is_array($json->data)) {
						$items = $json->data;
					} else {
						$items = [];
					}

					$all_data = array_merge($all_data, $items);
					$count_on_page = is_array($items) ? count($items) : 0;

					$page++;

				} while ($count_on_page === $per_page);
			}
		}

		return $all_data;
	}

	public function getApartmentsForFrontEnd($per_page = 50, $page = 1, $arg = [])
	{
		// error_log('MIB getApartmentsForFrontEnd called');
		$useFallback = false;
		$items = [];
		$total = 0;

		if (!empty($this->mibOptions['mib-login-email']) && !empty($this->mibOptions['mib-login-password']) && !empty($this->mibOptions['token'])) {
			$headers = ['Authorization' => "Bearer {$this->mibOptions['token']}"];

			$query = array_merge($arg, [
				'page' => (int) $page,
				'perPage' => (int) $per_page,
			]);

			$url = add_query_arg($query, $this->filterApartmentsUrl);
			$response = wp_remote_get($url, ['headers' => $headers]);

			if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
				error_log('MIB getApartmentsForFrontEnd Primary API failed.');
				$useFallback = true;
			} else {
				$body = wp_remote_retrieve_body($response);
				$json = json_decode($body);

				$items = (is_object($json) && isset($json->data) && is_array($json->data)) ? $json->data : [];
				$total = (int) ($json->count ?? 0);
			}
		} else {
			$useFallback = true;
		}

		if ($useFallback) {

			$fallbackUrl = $this->mibOptions['mib-api-url'] ?? '';
			$fallbackKey = $this->mibOptions['mib-api-key'] ?? '';

			if (!empty($fallbackUrl) && !empty($fallbackKey)) {
				$headers = ['X-API-KEY' => $fallbackKey];
				$query = array_merge($arg, [
					'page' => (int) $page,
					'perPage' => (int) $per_page,
				]);
				// Ensure fallback URL is treated as base
				$url = add_query_arg($query, $fallbackUrl);
				//error_log("MIB getApartmentsForFrontEnd Fallback URL: " . $url);

				$response = wp_remote_get($url, ['headers' => $headers, 'sslverify' => false]);

				if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
					$body = wp_remote_retrieve_body($response);
					//error_log("MIB getApartmentsForFrontEnd Body Length: " . strlen($body));
					$json = json_decode($body);

					if (is_array($json)) {
						$items = $json;
					} elseif (is_object($json) && isset($json->data) && is_array($json->data)) {
						$items = $json->data;
					} else {
						$items = [];
					}

					$total = (int) ($json->count ?? count($items));
				} else {
					error_log('MIB getApartmentsForFrontEnd Fallback API failed or returned error.');
				}
			}
		}

		return ["data" => $items, "total" => $total];
	}

	public function getOneApartment($id)
	{
		$all_data = [];
		$total_records = 0;
		$useFallback = false;

		if (!empty($this->mibOptions['mib-login-email']) && !empty($this->mibOptions['mib-login-password']) && !empty($this->mibOptions['token'])) {
			$url = "https://ugyfel.mibportal.hu:3000/apartments/{$id}";

			$response = wp_remote_get($url, [
				'timeout' => 10,
				'redirection' => 10,
				'httpversion' => '1.1',
				'headers' => ['Authorization' => "Bearer {$this->mibOptions['token']}"],
			]);

			if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
				$body = wp_remote_retrieve_body($response);
				$json = json_decode($body);

				if ($json) {
					$all_data = [$json];
					$total_records = 1;
				}
			} else {
				$useFallback = true;
			}
		} else {
			$useFallback = true;
		}

		if ($useFallback) {
			$fallbackUrl = $this->mibOptions['mib-api-url'] ?? '';
			$fallbackKey = $this->mibOptions['mib-api-key'] ?? '';

			if (!empty($fallbackUrl) && !empty($fallbackKey)) {
				// We assume fallback URL is like .../api/apartments
				// Appending ID to path
				$url = rtrim($fallbackUrl, '/') . '/' . $id;
				error_log("MIB getOneApartment Fallback URL: " . $url);

				$response = wp_remote_get($url, [
					'headers' => ['X-API-KEY' => $fallbackKey],
					'timeout' => 10,
					'sslverify' => false
				]);

				if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
					error_log("MIB getOneApartment Success");
					$body = wp_remote_retrieve_body($response);
					$json = json_decode($body);
					if (is_array($json)) {
						$all_data = $json; // Assume array of objects
						$total_records = count($json);
					} elseif ($json) {
						$all_data = [$json];
						$total_records = 1;
					}
				}
			}
		}

		return ["data" => $all_data, "total" => $total_records];
	}

	public function getOneApartmentsById($id)
	{
		$all_data = [];
		$total_records = 0;
		$useFallback = false;

		if (!empty($this->mibOptions['mib-login-email']) && !empty($this->mibOptions['mib-login-password']) && !empty($this->mibOptions['token'])) {
			$url = add_query_arg(['name' => $id], "https://ugyfel.mibportal.hu:3000/apartments");

			$response = wp_remote_get($url, [
				'timeout' => 10,
				'redirection' => 10,
				'httpversion' => '1.1',
				'headers' => ['Authorization' => "Bearer {$this->mibOptions['token']}"],
			]);

			if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
				$body = wp_remote_retrieve_body($response);
				$json = json_decode($body);

				$first = (is_object($json) && isset($json->data) && is_array($json->data) && !empty($json->data))
					? $json->data[0]
					: null;

				if ($first) {
					$all_data = [$first];
					$total_records = 1;
				}
			} else {
				$useFallback = true;
			}
		} else {
			$useFallback = true;
		}

		if ($useFallback) {
			$fallbackUrl = $this->mibOptions['mib-api-url'] ?? '';
			$fallbackKey = $this->mibOptions['mib-api-key'] ?? '';

			if (!empty($fallbackUrl) && !empty($fallbackKey)) {
				$url = add_query_arg(['name' => $id], $fallbackUrl);
				error_log("MIB getOneApartmentsById Fallback URL: " . $url);

				$response = wp_remote_get($url, [
					'headers' => ['X-API-KEY' => $fallbackKey],
					'timeout' => 10,
					'sslverify' => false
				]);

				if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
					error_log("MIB getOneApartmentsById Success");
					$body = wp_remote_retrieve_body($response);
					$json = json_decode($body);

					$first = null;
					if (is_array($json) && !empty($json)) {
						$first = $json[0];
					} elseif (is_object($json) && isset($json->data) && is_array($json->data) && !empty($json->data)) {
						$first = $json->data[0];
					}

					if ($first) {
						$all_data = [$first];
						$total_records = 1;
					}
				}
			}
		}

		return ["data" => $all_data, "total" => $total_records];
	}

	public function getResidentialDocuments($id)
	{
		// Not implementing fallback here as per task scope focus on apartments, 
		// but fixing the empty token crash just in case by checking it inside.
		if (empty($this->mibOptions['token'])) {
			return [];
		}
		// ... existing logic ...
		$url = "https://ugyfel.mibportal.hu:3000/residential_parks/get/{$id}";
		$response = wp_remote_get($url, [
			'timeout' => 10,
			'redirection' => 10,
			'httpversion' => '1.1',
			'headers' => ['Authorization' => "Bearer {$this->mibOptions['token']}"],
		]);

		if (is_wp_error($response)) {
			error_log('MIB getResidentialDocuments error: ' . $response->get_error_message());
			return [];
		}

		$body = wp_remote_retrieve_body($response);
		$json = json_decode($body, true);

		return $json ?: [];
	}
}
