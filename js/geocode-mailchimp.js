jQuery( document ).ready( function() {


	// Remove the 'required' attribute for the address fields that aren't zip
	// If we don't do this, (some) browser validation methods will throw JavaScript errors b/c the fields are required but not visible
	remove_required_attribute_from_address_fields();

	// Target 'addr1' field
	jQuery( 'body' ).on( 'blur', '[name$="[zip]"]', function() {
		// store the entered zip
		var zip_code = jQuery( this ).val();

		// setup our regex pattern to check against
		var regex_pattern = /^\d{5}(?:[-\s]\d{4})?$/;

		// regex confirm correct date format
		if( regex_pattern.test( zip_code ) ) {

			// display pre-loader and fire off AJAX
			jQuery( '[name$="[zip]' ).css({
				'background' : 'url('+admin_data.preloader+')',
				'background-repeat' : 'no-repeat',
				'background-position' : '0',
				'background-position-x' : '98%'
			});

			// setup the ajax data right hurr
			var data = {
				'action': 'geocode_zip',
				'zip_code': zip_code
			};

			// Run our ajax request
			jQuery.post( admin_data.ajax_url, data, function( response ) {
				// populate the fields
				jQuery( '[name$="[addr1]"]' ).val( '-' ); // Address must contain a value, or address will not save
				jQuery( '[name$="[city]' ).val( response.city ); // Populate city
				jQuery( '[name$="[state]' ).val( response.state ); // Populate state
				jQuery( '[name$="[country]' ).val( response.country ); // Populate Country
				// display a green checkmark to show it's a valid US zip
				jQuery( '[name$="[zip]' ).css({
					'background' : 'url('+admin_data.green_checkmark+')',
					'background-repeat' : 'no-repeat',
					'background-position' : '0',
					'background-position-x' : '98%'
				});
				// enable our submit button
				jQuery( '.yikes-easy-mc-form' ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
			});

		} else {

			// disable our submit button
			jQuery( '.yikes-easy-mc-form' ).find( 'input[type="submit"]' ).attr( 'disabled', 'disabled' );

			// display a red x to show it's an invalid US zip
			jQuery( '[name$="[zip]' ).css({
				'background' : 'url('+admin_data.red_x+')',
				'background-repeat' : 'no-repeat',
				'background-position' : '0',
				'background-position-x' : '98%'
			});
			//console.log( 'error' ); console.log( admin_data.red_x );
		}

	});
});

/**
* Loop through all the address fields and remove the required attribute and class from each field except zip 
* If we don't do this, (some) browser validation methods will throw JavaScript errors b/c the fields are required but not visible
*
* @since 1.1.2
*/
function remove_required_attribute_from_address_fields() {
	jQuery( '.yikes-easy-mc-address' ).each( function() {
		var required_class = jQuery( this ).parents( 'label' ).hasClass( 'yikes-mailchimp-field-required' );
		var required_prop  = ( jQuery( this ).prop( 'required' ) === true || jQuery( this ).prop( 'required' ) === 'required' );
		var not_on_zip     = jQuery( this ).attr( 'name' ).indexOf( 'zip' ) === -1 ? true : false;

		// If we're not && this field's label has the class 'yikes-mailchimp-field-required' && this field has the required property
		if ( not_on_zip === true && required_class === true && required_prop === true ) {
			jQuery( this ).removeClass( 'yikes-easy-mc-address' ).prop( 'required', false ).parents( 'label' ).removeClass( 'yikes-easy-mc-address' );
		}
	});
}