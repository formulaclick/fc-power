<?php
 
if ( ! class_exists( 'FCModule' ) ) {

    class FCModule {

    	var $fields_to_save = array();

    	public function load_options_values($option_name){
    		$this->options = get_option($option_name, null);
    	}

    	public function setting_validate($input){
 			$options_array = array();
 			foreach($this->fields_to_save as $field){
	 		    if( isset( $input[$field] ) )
			         $options_array[$field] = $input[$field];				
 			}
		    return $options_array;    
    	}

		public function input_text( $args ) {
			$name = $args['label_for'];
			$field = $args['field'];
			$value = (isset($this->options) && !is_null($this->options) && isset($this->options[$field])) ? $this->options[$field] : null;

			if(is_null($value) && isset( $args['default'] )) $value = $args['default'];

			$classes = array( 'regular-text' );
			if ( isset( $args['classes'] ) ) {
				$classes = $args['classes'];
			}
			
			$description = '';
			if ( isset( $args['description'] ) ) {
				$description = '<br /><span class="description">'.$args['description'].'</span>';
			}
				
			printf(
				'<input name="%s" id="%s" type="text" class="%s" value="%s" />%s',
				esc_attr( $name ),
				esc_attr( $name ),
				esc_attr( implode( ' ', $classes ) ),
				esc_attr( $value ),
				$description
			);
		}

		public function input_textarea( $args ) {
			$name = $args['label_for'];
			$field = $args['field'];
			$value = (isset($this->options) && !is_null($this->options) && isset($this->options[$field])) ? $this->options[$field] : null;
		
			if(is_null($value) && isset( $args['default'] )) $value = $args['default'];

			$classes = array( 'regular-text' );
			if ( isset( $args['classes'] ) ) {
				$classes = $args['classes'];
			}
			
			$description = '';
			if ( isset( $args['description'] ) ) {
				$description = '<br /><span class="description">'.$args['description'].'</span>';
			}
				
			printf(
				'<textarea name="%s" id="%s" class="%s">%s</textarea>%s',
				esc_attr( $name ),
				esc_attr( $name ),
				esc_attr( implode( ' ', $classes ) ),
				$value,
				$description
			);
		}
			
		public function input_checkbox( $args ) {
			$name = $args['label_for'];
			$field = $args['field'];
			$value = (isset($this->options) && !is_null($this->options) && isset($this->options[$field])) ? $this->options[$field] : null;
	
			if(is_null($value) && isset( $args['default'] )) $value = $args['default'];

			$classes = array();
			if ( isset( $args['classes'] ) ) {
				$classes = $args['classes'];
			}

			$description = '';
			if ( isset( $args['description'] ) ) {
				$description = $args['description'];
			}

			printf(
				'<input name="%s" id="%s" type="checkbox" class="%s" %s />%s',
				esc_attr( $name ),
				esc_attr( $name ),
				esc_attr( implode( ' ', $classes ) ),
				checked( 'on', $value, false ),
				$description
			);
		}
	
		public function input_select( $args ) {
			$name = $args['label_for'];
			$field = $args['field'];
			$value = (isset($this->options) && !is_null($this->options) && isset($this->options[$field])) ? $this->options[$field] : null;
			
			if(is_null($value) && isset( $args['default'] )) $value = $args['default'];

			$classes = array();
			if ( isset( $args['classes'] ) ) {
				$classes = $args['classes'];
			}
	
			$options = array();
			if ( isset( $args['options'] ) ) {
				$options = $args['options'];
			}
	
			$multiple = false;
			if ( isset( $args['multiple'] ) && $args['multiple'] ) {
				$multiple = true;
			}
	
			printf(
				'<select name="%s" id="%s" class="%s" %s>',
				esc_attr( $name ) . ( $multiple ? '[]' : '' ),
				esc_attr( $name ),
				esc_attr( implode( ' ', $classes ) ),
				$multiple ? 'multiple="multiple" size="10"' : ''
			);
			
			foreach ( $options as $option_key => $option ) {
	
				$selected = ( is_string( $value ) && (string)$option_key === $value ) ||
							( is_array( $value ) && in_array( $option_key, $value ) );
				
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $option_key ),
					selected( $selected, true, false ),
					esc_attr( $option )
				);
			}
	
			echo '</select>';
		}

    }

}