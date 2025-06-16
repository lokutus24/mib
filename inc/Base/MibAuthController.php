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

		$args = array(
		    'headers' => array(
		        'Authorization' => "Bearer {$this->mibOptions['token']}"
		    )
		);

		$total_records = 0; // Az összes rekord száma
		$per_page = 50; // Az oldalankénti rekordok száma
		$page = 1; // Az első oldalról kezdünk
		$all_data = array(); // Az összes adatot tároló tömb

		do {

		    $args['body'] = array(
		        'page' => $page,
		        'perPage' => $per_page
		    );

		    $response = wp_remote_get($this->filterApartmentsUrl, $args);


		    if (!is_wp_error($response)) {

		        $body = wp_remote_retrieve_body($response);
		        $data = json_decode($body);

		        if ($data) {
		            $all_data = array_merge($all_data, $data->data); // Hozzáadjuk az aktuális oldal adatait az összes adathoz
		            $total_records += count($data->data); // Frissítjük az összes rekord számát

		            $page++; // Növeljük az oldalszámot a következő kéréshez
		        }

		    }else{
		        // Hiba kezelése, ha szükséges
		        break; // Kilépünk a ciklusból, ha hiba történt
		    }

		} while (count($data->data) == $per_page); // Amíg az aktuális oldalon a rekordok száma megegyezik az oldalankénti rekordok számával

        return $all_data;
		
	}

	public function getApartmentsForFrontEnd($per_page = 50, $page = 1, $arg = [])
	{

		$args = array(
		    'headers' => array(
		        'Authorization' => "Bearer {$this->mibOptions['token']}"
		    )
		);

		$total_records = 0; // Az összes rekord száma
		$all_data = array(); // Az összes adatot tároló tömb

		$arg['page'] = $page;
		$arg['perPage'] = $per_page;
		$args['body'] = $arg;

	    $response = wp_remote_get($this->filterApartmentsUrl, $args);

	    if (!is_wp_error($response)) {

	        $body = wp_remote_retrieve_body($response);
	        $data = json_decode($body);

	        if ($data) {
	            $all_data = $data->data; // Hozzáadjuk az aktuális oldal adatait az összes adathoz
	            $total_records = $data->count; // Frissítjük az összes rekord számát
	        }

	    }

        return ["data" => $all_data, "total" => $total_records];
		
	}

	public function getOneApartment($id){

		$parkId = $this->mibOptions['mib-residential-park-id'];
		$response = wp_remote_get( "https://ugyfel.mibportal.hu:3000/apartments/{$parkId}/{$id}", array(
		    'timeout'     => 0, // Set to 0 for no timeout or to a reasonable time in seconds.
		    'redirection' => 10,
		    'httpversion' => '1.1',
		    'headers'     => array(
		        'Authorization' => "Bearer {$this->mibOptions['token']}"
		    ),
		    'body'        => array(
		        'id' => $id
		    ),
		    'method'      => 'GET',
		    'data_format' => 'body'
		));

		if ( !is_wp_error( $response ) ) {

		    $body = wp_remote_retrieve_body( $response );
		    $data = json_decode($body);

		    if ($data) {
	            $all_data = $data; // Hozzáadjuk az aktuális oldal adatait az összes adathoz
	            $total_records = 1; // Frissítjük az összes rekord számát
	        }
		    return ["data" => [$all_data], "total" => $total_records];
		}
	}

	public function getOneApartmentsById($id){

		$response = wp_remote_get( "https://ugyfel.mibportal.hu:3000/apartments?name={$id}", array(
		    'timeout'     => 0, // Set to 0 for no timeout or to a reasonable time in seconds.
		    'redirection' => 10,
		    'httpversion' => '1.1',
		    'headers'     => array(
		        'Authorization' => "Bearer {$this->mibOptions['token']}"
		    ),
		    'body'        => array(
		        'name' => $id,
		    ),
		    'method'      => 'GET',
		    'data_format' => 'body'
		));

		if ( !is_wp_error( $response ) ) {

		    $body = wp_remote_retrieve_body( $response );
		    $data = json_decode($body);

		    if ($data) {
	            $all_data = $data->data[0]; // Hozzáadjuk az aktuális oldal adatait az összes adathoz
	            $total_records = 1; // Frissítjük az összes rekord számát
	        }
		    return ["data" => [$all_data], "total" => $total_records];
		}
	}
}