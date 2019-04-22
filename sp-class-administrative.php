<?php
	
class SP_Ajax_Administrative_Action extends WP_AJAX {
	
	protected $action = 'administrative-action';
	protected function run() {
		
		if( isset( $_GET[ 'province' ] ) && $_GET[ 'province' ] != '' ) {
			
			$prov = trim( $_GET['province'] );
			$remote = wp_remote_get( RAJAONGKIR_BASE_URI . '/city?province=' . $prov, array(
				'headers' => array( 'key' => RAJAONGKIR_API_KEY )
			) );	
			
			$data = array();
			if( $remote['response']['code'] == 200 ) {
				$remote_body = json_decode( $remote['body'] );	
				$rows = $remote_body->rajaongkir->results;
				foreach( $rows as $row ) {
					$data[ $row->city_id ] = trim( $row->type . " " . $row->city_name );
				}
			}
			
			echo json_encode( $data, true );
		}
		
	}
	
}

SP_Ajax_Administrative_Action::listen();