<?php
	
class SP_Ajax_CalculateOngkir_Action extends WP_AJAX {
	
	protected $action = 'calculate-ongkir';
	
	protected function run() {
		
		if( $this->requestType( 'POST' ) ) {
		
			$data_to_send =  array(
				'origin' => $_POST['origin'],
				'destination' => $_POST['destination'],
				'weight' => $_POST['weight'],
				'courier' => $_POST['courier']
			);
			$remote = wp_remote_post( RAJAONGKIR_BASE_URI . '/cost', array(
				'method' => 'POST',
				'headers' => array( 
					'key' => RAJAONGKIR_API_KEY,
					'content-type' => 'application/x-www-form-urlencoded'
				),
				'body' => $data_to_send
			) );	
			
			$data = array();
			if( $remote['response']['code'] == 200 ) {
				$remote_body = json_decode( $remote['body'] );	
				$rows = $remote_body->rajaongkir->results;
				foreach( $rows as $row ) {
					$data['name'] = $row->name;
					$data['costs'] = array();
					foreach( $row->costs as $cost ) {
						array_push( $data[ 'costs' ], array(
							'service' => $cost->service,
							'description' => $cost->description,
							'cost' => $cost->cost[0]->value,
							'etd' => $cost->cost[0]->etd
						) );
					}
				}
			}
			
			echo json_encode( $data, true );
		
		}
		
	}
	
}

SP_Ajax_CalculateOngkir_Action::listen();