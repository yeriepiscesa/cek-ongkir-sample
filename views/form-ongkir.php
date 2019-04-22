<?php
$remote = wp_remote_get( RAJAONGKIR_BASE_URI . '/province', array(
	'headers' => array( 'key' => RAJAONGKIR_API_KEY )
) );	
$data_province = array();
if( is_array( $remote ) && $remote['response']['code'] == 200 ) {
	$remote_body = json_decode( $remote['body'] );	
	$data_province = $remote_body->rajaongkir->results;
}	
?>
<form method="post" class="frm-cek-ongkir" id="frmCekOngkir">    
    <div class="row">
	    <div class="col-6">
		    
			<h3>Kota Asal</h3>
		    <div class="form-group row">
		        <label class="col-4">Propinsi</label>
		        <select class="col-8" name="province_source" id="frm-ongkir-province-source">
			        <option value="">-- Pilih Propinsi --</option>
			        <?php foreach( $data_province as $row ): ?>
			        <option value="<?= $row->province_id ?>"><?= $row->province ?></option>
			        <?php endforeach; ?>
		        </select>
		    </div>
		    
		    <div class="form-group row">
		        <label class="col-4">Kota</label>
		        <select class="col-8" name="city_source" id="frm-ongkir-city-source">
			        <option value="">-- Pilih Kota --</option>
		        </select>
		    </div>
		    
		    <div class="form-group row">
			    <label class="col-4">Kurir</label>
			    <select class="col-8" name="courier" id="frm-ongkir-courier">
				    <option value="jne">JNE</option>
				    <option value="pos">Pos Indonesia</option>
				    <option value="tiki">TIKI</option>
			    </select>
		    </div>

	    </div>
	    
	    <div class="col-6">
		    
			<h3>Kota Tujuan</h3>
		    <div class="form-group row">
		        <label class="col-4">Propinsi</label>
		        <select class="col-8" name="province_dest" id="frm-ongkir-province-dest">
			        <option value="">-- Pilih Propinsi --</option>
			        <?php foreach( $data_province as $row ): ?>
			        <option value="<?= $row->province_id ?>"><?= $row->province ?></option>
			        <?php endforeach; ?>
		        </select>
		    </div>
		    
		    <div class="form-group row">
		        <label class="col-4">Kota</label>
		        <select class="col-8" name="city_dest" id="frm-ongkir-city-dest">
			        <option value="">-- Pilih Kota --</option>
		        </select>
		    </div>
		    
		    <div class="form-group row">
			    <label class="col-4">Berat (gram)</label>
			    <input class="col-8" type="number" name="weight" id="frm-ongkir-weight">
		    </div>
		    
		    <div class="row">
			    <label class="col-4">&nbsp;</label>
			    <button class="col-8" type="button" id="frm-ongkir-submit">Hitung Ongkir</button>
		    </div>
		    
	    </div>
    </div>
</form>
<div class="col-10" id="ongkir-cost-result">
</div>

<script type="text/javascript">
( function( $ ){
	
	$( '#frm-ongkir-province-source' ).change( function(){
		var $element = $( '#frm-ongkir-city-source' );
		var $parentEl = $( this );
		set_city_options( $element, $(this).val(), $parentEl );
	} );	
	
	$( '#frm-ongkir-province-dest' ).change( function(){
		var $element = $( '#frm-ongkir-city-dest' );
		var $parent = $( this );
		set_city_options( $element, $(this).val(), $parent );
	} );	
	
	function set_city_options( el, province, parentEl ) {
		parentEl.prop( 'disabled', 'disabled' );
		el.empty();			  
		el.append( new Option( '-- Pilih Kota --', '', false, false ) );			
		
		$.ajax( {
			"url": "<?= admin_url( 'admin-ajax.php' ) ?>",
			"data": { 'action':'administrative-action', 'province': province }, 
			"success": function( response ) {
				var result = $.parseJSON(response);
				$.each( result, function( key, val ) {					 	 
					var option = new Option( val, key, false, false );
					el.append(option);
				});
				parentEl.removeAttr( 'disabled' );
			}
		} );
	}
	
	$( '#frm-ongkir-submit' ).click( function() {
		$( '#ongkir-cost-result' ).html( 'Sedang menghitung...' );
		var data = {
			'origin': $( '#frm-ongkir-city-source' ).val(),
			'destination': $( '#frm-ongkir-city-dest' ).val(),
			'weight': $( '#frm-ongkir-weight' ).val(),
			'courier': $( '#frm-ongkir-courier' ).val()
		};
		$.post( {
			'url': '<?= SP_Ajax_CalculateOngkir_Action::url() ?>',
			'data': data	,
			'success': function( response ) {
				var result = $.parseJSON(response);
				
				var html = '';
				if( result.costs && result.costs.length >= 1 ) {
					html += '<table><tr><th>Layanan</th><th>Tarif(Rp)</th><th>Estimasi(hari)</th></tr>';
					$.each( result.costs, function( index, data ){
						console.log( data );
						html += '<tr>';
						html += '<td>' + data.service + ' (' + data.description + ')</td>';
						html += '<td>' + data.cost + '</td>';
						html += '<td>' + data.etd + '</td>';
						html += '</tr>';
					} );
					html += '</table>';
				} else {
					html = '<strong>Mohon maaf, saat ini data tidak dapat diterima. Mohon ulangi kembali atau pilih kurir lain.</strong>';
				}
				$( '#ongkir-cost-result' ).html( html );
				
			}
		} );
	} );
} )( jQuery );
</script>