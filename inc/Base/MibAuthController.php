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
		    'body'        => json_encode(['email' => $this->mibOptions['mib-login-email'], 'password' => $this->mibOptions['mib-login-password']]),
		    'headers'     => array(
		        'Content-Type' => 'application/json',
		    ),
		    'timeout'     => '5',
		    'redirection' => '5',
		    'httpversion' => '1.0',
		    'blocking'    => true,
		    'cookies'     => array()
		);

		$response = wp_remote_post($this->loginUrl, $args);
      	

		if (is_wp_error($response)) {

		    $response_message = json_decode($response->get_error_message());
		    $this->mibOptions['token'] = '';
		    $this->mibOptions['expiry'] = ''; 
		    update_option('mib_options', $this->mibOptions);

		}else{

		    $response_message = json_decode(wp_remote_retrieve_body($response) );

		    if (isset($response_message->token)) {

		    	$now = new \DateTime();
				$now->modify('+1 hour');

		    	$this->mibOptions['token'] = $response_message->token;
		    	$this->mibOptions['expiry'] = $now->format('Y-m-d H:i:s'); 
		    	update_option('mib_options', $this->mibOptions);
		    }else{
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
	    if (empty($this->mibOptions['token'])) {
	        return []; // nincs token → üres tömb, ne dőljön el
	    }

	    $headers = ['Authorization' => "Bearer {$this->mibOptions['token']}"];
	    $per_page = 50;
	    $page = 1;
	    $all_data = [];

	    do {
	        $url = add_query_arg([
	            'page'    => $page,
	            'perPage' => $per_page,
	        ], $this->filterApartmentsUrl);

	        $response = wp_remote_get($url, ['headers' => $headers]);

	        if (is_wp_error($response)) {
	            error_log('MIB getApartments wp_remote_get error: ' . $response->get_error_message());
	            break;
	        }

	        $body = wp_remote_retrieve_body($response);
	        $json = json_decode($body);

	        $items = (is_object($json) && isset($json->data) && is_array($json->data)) ? $json->data : [];

	        $all_data = array_merge($all_data, $items);
	        $count_on_page = is_array($items) ? count($items) : 0;

	        $page++;

	    } while ($count_on_page === $per_page);

	    return $all_data;
	}

	public function getApartmentsForFrontEnd($per_page = 50, $page = 1, $arg = [])
	{
	    if (empty($this->mibOptions['token'])) {
	        return ["data" => [], "total" => 0];
	    }

	    $headers = ['Authorization' => "Bearer {$this->mibOptions['token']}"];

	    $query = array_merge($arg, [
	        'page'    => (int)$page,
	        'perPage' => (int)$per_page,
	    ]);

	    $url = add_query_arg($query, $this->filterApartmentsUrl);
	    $response = wp_remote_get($url, ['headers' => $headers]);

	    if (is_wp_error($response)) {
	        error_log('MIB getApartmentsForFrontEnd wp_remote_get error: ' . $response->get_error_message());
	        return ["data" => [], "total" => 0];
	    }

	    $body = wp_remote_retrieve_body($response);
	    $json = json_decode($body);

	    $items = (is_object($json) && isset($json->data) && is_array($json->data)) ? $json->data : [];
	    $total = (int)($json->count ?? 0);

	    return ["data" => $items, "total" => $total];
	}

	public function getOneApartment($id)
	{
	    $all_data = [];
	    $total_records = 0;

	    if (empty($this->mibOptions['token'])) {
	        return ["data" => $all_data, "total" => $total_records];
	    }

	    $parkId = $this->mibOptions['mib-residential-park-id'];
	    $url = "https://ugyfel.mibportal.hu:3000/apartments/{$parkId}/{$id}";

	    $response = wp_remote_get($url, [
	        'timeout'     => 10,
	        'redirection' => 10,
	        'httpversion' => '1.1',
	        'headers'     => ['Authorization' => "Bearer {$this->mibOptions['token']}"],
	    ]);

	    if (!is_wp_error($response)) {
	        $body = wp_remote_retrieve_body($response);
	        $json = json_decode($body);

	        if ($json) {
	            $all_data = [$json];
	            $total_records = 1;
	        }
	    } else {
	        error_log('MIB getOneApartment error: ' . $response->get_error_message());
	    }

	    return ["data" => $all_data, "total" => $total_records];
	}

        public function getOneApartmentsById($id)
        {
            $all_data = [];
            $total_records = 0;

	    if (empty($this->mibOptions['token'])) {
	        return ["data" => $all_data, "total" => $total_records];
	    }

	    $url = add_query_arg(['name' => $id], "https://ugyfel.mibportal.hu:3000/apartments");

	    $response = wp_remote_get($url, [
	        'timeout'     => 10,
	        'redirection' => 10,
	        'httpversion' => '1.1',
	        'headers'     => ['Authorization' => "Bearer {$this->mibOptions['token']}"],
	    ]);

	    if (!is_wp_error($response)) {
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
	        error_log('MIB getOneApartmentsById error: ' . $response->get_error_message());
	    }

            return ["data" => $all_data, "total" => $total_records];
        }

        public function getResidentialDocuments($id)
        {
            if (empty($this->mibOptions['token'])) {
                return [];
            }

            $url = "https://ugyfel.mibportal.hu:3000/residential_parks/get/{$id}";
            $response = wp_remote_get($url, [
                'timeout'     => 10,
                'redirection' => 10,
                'httpversion' => '1.1',
                'headers'     => ['Authorization' => "Bearer {$this->mibOptions['token']}"],
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
