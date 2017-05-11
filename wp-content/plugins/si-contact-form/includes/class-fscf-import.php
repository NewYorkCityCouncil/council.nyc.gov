<?php

/**
 * Description of class-fscf-import
 * Import class to import settings from pre 4.0 versions of the plugin
 * Functions are called statically, so no need to instantiate the class
 * @authors Mike Challis and Ken Carlson
 */

class FSCF_Import {

	static $global_defaults, $form_defaults, $field_defaults;
	static $global_options, $form_options;
	static $old_global_options, $old_form_options;
	
	static $select_type_fields = array(
		'checkbox',
		'checkbox-multiple',
		'select',
		'select-multiple',
		'radio'
	);
	
	static function import_old_version( $force = '' ) {
		
//		global $fscf_special_slugs;		// List of reserve slug names
		
		// ***** Import global options *****

        // upgrade import only back to version 2.5.6, because before that, there was no 'si_contact_form_gb' setting
		self::$old_global_options = get_option( 'si_contact_form_gb' );
		if ( empty( self::$old_global_options ) ) {
			return;
		}
//print_r(self::$old_global_options)."<br>\n";
		self::$global_options = FSCF_Util::get_global_options();

        // import a few global options
		$copy_fields = array( 'donated', 'vcita_dismiss' );
		foreach ( $copy_fields as $field ) {
			if ( ! empty( self::$old_global_options[$field] ) )
				self::$global_options[$field] = self::$old_global_options[$field];
		}
        // import this global option
        // Highest form ID (used to assign ID to new form)
		// When forms are deleted, the remaining forms are NOT renumberd, so max_form_num might be greater than
		// the number of existing forms
		if ( ! empty( self::$old_global_options['max_forms'] ) )
			self::$global_options['max_form_num'] = self::$old_global_options['max_forms'];
//print 'max_form_num:'.self::$global_options['max_form_num']."<br>\n";
		// ***** Import form options *****
        $max_fields_shim = 8;

        if($force == 'force') {
              // force is when they pressed the button import from 3.xx, they are warned this replaces the 4.xx forms
               self::$global_options['form_list'] = array(); // delete current form list
               // delete current 4.xx forms
               delete_option('fs_contact_global');

               // delete up to 100 forms (a unique configuration for each contact form)
               for ($i = 1; $i <= 100; $i++) {
                 delete_option("fs_contact_form$i");
               }
        }
		for ($frm=1; $frm<=self::$global_options['max_form_num']; $frm++) {
//print 'importing form:'.$frm."<br>\n";
			$old_opt_name = 'si_contact_form';
			$old_opt_name .= ($frm==1) ? '': $frm;
			self::$old_form_options = get_option($old_opt_name);
			if ( ! self::$old_form_options ) continue;


            if($force == 'force') {


            } else {
                   // Make sure that the options for this form doesn't already exist
                   self::$form_options = FSCF_Util::get_form_options($frm, $use_defaults=false);
			       if ( self::$form_options ) continue;
            }

            // if max fields is missing it will be 8, or the value of the last one in the loop.
            if (isset(self::$old_form_options['max_fields']) && self::$old_form_options['max_fields'] > 0)
                $max_fields_shim = self::$old_form_options['max_fields'];
            else
               self::$old_form_options['max_fields'] = $max_fields_shim;

			$new_form_options = self::convert_form_options(self::$old_form_options, self::$old_form_options['max_fields']);
//print_r($new_form_options)."<br>\n";
			// Save the imported form
			$form_option_name = 'fs_contact_form' . $frm;
			// Add form name to the form list...
            if ($new_form_options['form_name'] == '')
                       $new_form_options['form_name'] = __( 'imported', 'si-contact-form' );

			self::$global_options['form_list'][$frm] = $new_form_options['form_name'];
			update_option ( $form_option_name, $new_form_options );

		}	// end for loop (forms)

		self::$global_options['import_success'] = true;
		self::$global_options['import_msg'] = true;
        // recalibrate max_form_num to the highest form number (not count)
        ksort( self::$global_options['form_list'] );
        self::$global_options['max_form_num'] = max(array_keys(self::$global_options['form_list']));
//print_r(self::$global_options)."<br>\n";
		update_option( 'fs_contact_global', self::$global_options );

		// Display a notice on the admin page
		FSCF_Util::add_admin_notice(__( 'Fast Secure Contact Form has imported settings from the old version.', 'si-contact-form' ), 'updated');

        // Force reload of global and form options
		FSCF_Options::unload_options();
		
	}  // end function import_old_version

	
	static function convert_form_options( $old_options, $max_fields ) {
		// Converts form options from version 3.x to 4.x
		// Returns converted options array
		global $fscf_special_slugs;		// List of reserve slug names

        //print_r($old_options); exit;

		// Start with the current version form defaults
		$new_options = FSCF_Util::get_form_defaults();

		foreach ( $new_options as $key => $val ) {
			//if ( ! empty($old_options[$key]) ) // caused empty  Welcome introduction to appear filled in
            if ( isset($old_options[$key]) )
				$new_options[$key] = stripslashes($old_options[$key]);
		}
		
		// ***** Import fields *****

		// Keep a list of slugs so we can be sure they are unique
		$slug_list = $fscf_special_slugs;
		// Standard fields should already have been added by defaults
		// Import standard field settings
		$std_fields = array( 'name', 'email', 'subject', 'message' );
		// This assumes that the standard fields in the form defaults are in the same order as
		//   the names in the above array
		foreach ( $std_fields as $key => $val ) {

            if ( 'subject' == $val ) { // was there an optional subject select list?
               if (!empty($old_options['email_subject_list'])) {
                  $new_options['fields'][$key]['options'] = $old_options['email_subject_list'];
                  $new_options['fields'][$key]['type'] = 'select';
               }
            }
			// Make sure this goes to the correct field!
			$test = ( 'name' == $val ) ? 'full_name' : $val;
			$slug_list[] = $test;
			if ( $new_options['fields'][$key]['slug'] == $test ) {
				// name_type, etc. could be 'required', 'not_required', or 'not_available'
				if ( 'not_required' == $old_options[$val . '_type'] ) {
					// Standard fields are required by default, so change this
					$new_options['fields'][$key]['req'] = 'false';
				} else if ( 'not_available' == $old_options[$val . '_type'] ) {
					$new_options['fields'][$key]['disable'] = 'true';
				}
			} else {
				// Error: this is the wrong field!
				// This could happen if the standard fields in the default form are in a different
				// order than in $std_fields
			}
		}	// end foreach $std_fields

        //print_r($new_options);

		// Import the old "extra fields"
		// This will ignore any field properties no longer used
		for ($fld=1; $fld<= $max_fields; $fld++){
            $old_type = $old_options['ex_field' . $fld . '_type'];
			if ( ! empty($old_options['ex_field'.$fld.'_label']) || 'fieldset' == $old_type || 'fieldset-close' == $old_type ) {
				// Add a new field with the default properties
				$new_field = FSCF_Util::get_field_defaults();
				foreach ( $new_field as $key => $val ) {
					$old_prop = 'ex_field' . $fld . '_' . $key;

					// Need special treatment for: default option / default_text
					// Need to parse and reformat select options lists, checkboxres, etc.
					switch ( $key ) {
						case "default":
							// The old version has both default_text and default_option
							if ( in_array($old_type, self::$select_type_fields)
									&& $old_options['ex_field' . $fld . '_default'] > 0 ) {
								$new_field['default'] = $old_options['ex_field' . $fld . '_default'];
							} else if ( ! empty($old_options['ex_field' . $fld . '_default_text']) ) {
								$new_field['default'] = stripslashes($old_options['ex_field' . $fld . '_default_text']);
							}
							break;

						case "label":
                            if ( empty($old_options['ex_field'.$fld.'_label']) && ( 'fieldset' == $old_type || 'fieldset-close' == $old_type ) )
                               $old_options['ex_field'.$fld.'_label'] = sprintf( __( 'Field %s', 'si-contact-form' ), $fld );
							// Check for options added to the label (e.g. Color:,Red;Green;Blue ), etc.
							$new_field[$key] = $old_options[$old_prop];
							if ( in_array($old_type, self::$select_type_fields) && 'checkbox' != $old_type )
								$new_field = self::parse_label($new_field);
                            if ( 'checkbox' == $old_type ) {
                               // label might have \, (not needed in 4.x version, remove it)
                               $new_field['label'] = str_replace( '\,', ',', $new_field['label'] ); // "\," changes to ","
                               $new_field['label'] = stripslashes( $new_field['label'] );
                            }
							break;

						default:
                            if ( ! empty($old_options[$old_prop]) )
							  $new_field[$key] = stripslashes($old_options[$old_prop]);

					}	// End switch
				}	// end foreach $new_field

				// Create the slug for the field from the field label
				// the sanitize title function encodes UTF-8 characters, so we need to undo that

                // this line croaked on some chinese characters
				//$new_field['slug'] = substr( urldecode(sanitize_title_with_dashes(remove_accents($new_field['label']))), 0, FSCF_MAX_SLUG_LEN );

//echo 'slug before:'.$new_field['label']."<br>\n";
                $new_field['slug'] = remove_accents($new_field['label']);
                $new_field['slug'] = preg_replace('~([^a-zA-Z\d_ .-])~', '', $new_field['slug']);
                $new_field['slug'] = substr( urldecode(sanitize_title_with_dashes($new_field['slug'])), 0, FSCF_MAX_SLUG_LEN );
                if ($new_field['slug'] == '')
                   $new_field['slug'] = 'na';
				if ( '-' == substr( $new_field['slug'], strlen($new_field['slug'])-1, 1) )
						$new_field['slug'] = substr( $new_field['slug'], 0, strlen($new_field['slug'])-1);

				// Make sure the slug is unique
				$new_field['slug'] = FSCF_Options::check_slug($new_field['slug'], $slug_list);
//echo 'slug jafter:'.$new_field['slug']."<br>\n";
				$slug_list[] = $new_field['slug'];

				$new_options['fields'][] = $new_field;

			} 	// end if old field label not empty

		}	// for loop through fields

		return($new_options);
		
	}	// end function convert_form_options
	
	
	static function parse_label( $field ) {
		// Parse label from old verson to remove options, etc.
		// Returns the modified field

		// find the label and the options inside $field['label']
		$exf_opts_array = array( );
		$exf_opts_label = '';
		$exf_array_test = trim( $field['label'] );
		if ( preg_match( '#(?<!\\\)\,#', $exf_array_test ) ) {

			list($exf_opts_label, $value) = preg_split( '#(?<!\\\)\,#', $exf_array_test ); //string will be split by "," but "\," will be ignored
			$exf_opts_label = trim( str_replace( '\,', ',', $exf_opts_label ) ); // "\," changes to ","
			$value = trim( str_replace( '\,', ',', $value ) ); // "\," changes to ","
			if ( $exf_opts_label != '' && $value != '' ) {
				if (  preg_match( "/;/", $value ) ) {
					// multiple options
					$exf_opts_array = explode( ";", $value );
				}
			}
		}

		foreach ( $exf_opts_array as $key => $opt ) {
             $opt = trim($opt);
			if ( 0 == $key )
				$field['options'] = stripslashes($opt);
			else
				$field['options'] .=  "\n" . stripslashes($opt);
		}


		// Check for inline indicator
		if ( preg_match( '/^{inline}/', $exf_opts_label ) ) {
			$exf_opts_label = str_replace( '{inline}', '', $exf_opts_label );
			$field['inline'] = 'true';
		}
		
		$field['label'] = stripslashes($exf_opts_label);
        //print_r($field);
		return($field);
	}	// end function parse_label()
	
	
	
} // end class FSCF_Import

// end of file  