<?php
 
if ( ! class_exists( 'FCAvisoLegal' ) ) {

    class FCAvisoLegal extends FCModule{

        function __construct() {

            // Register the settings from configuration pages
            add_action( 'admin_init', array( $this, 'register_the_settings' ) );

            // Reemplazamos la página de aviso legal
            add_filter('the_content', array( $this, 'replace_content' ));

        }

        function register_the_settings() {
			
			$page = 'fc-power-aviso-legal';
			$group = $page;
			$option_name = 'fc_power_aviso_legal';
			
			$this->load_options_values($option_name);

			// Sección
			$section = 'fc_power_section_general';
			add_settings_section(
				$section, // id
				'Datos del aviso legal', // title
				'__return_false', // callback
				$page // page
			);

			$field = 'headertag';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Tag de los títulos',
				array( $this, 'input_select' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
					'options'   => array('strong'=>'Negrita', 'h2'=>'Encabezado h2', 'h3'=>'Encabezado h3', 'h4'=>'Encabezado h4', 'h5'=>'Encabezado h5', 'h6'=>'Encabezado h6'),
				)
			);			

			$field = 'empresa';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Nombre fiscal de la empresa',
				array( $this, 'input_text' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);			
			
			$field = 'alias';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Alias',
				array( $this, 'input_text' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);			

			$field = 'poblacion';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Población donde está registrada',
				array( $this, 'input_text' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);			


			$field = 'email';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Email de contacto',
				array( $this, 'input_text' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);		


			$field = 'cif_nif';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'CIF o NIF',
				array( $this, 'input_text' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);		


			$field = 'direccion';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Dirección',
				array( $this, 'input_text' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
				)
			);		


			$field = 'datos_registro';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Datos registro mercantil',
				array( $this, 'input_textarea' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
					'description' => 'Ejemplo: inscrita en el Registro Mercantil de ... inscripción ..., Tomo ... , Sección G...., Folio ......, Hoja .....'
				)
			);		


			$pages = get_pages(); 
			$options = array('0'=>'-- No usar --');
			foreach($pages as $the_page){
				$options[$the_page->ID] = $the_page->post_title;
			}
			$field = 'pagina';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Página a utilizar',
				array( $this, 'input_select' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
					'options'   => $options
				)
			);		

			include 'template.php';
			$field = 'plantilla';
			$this->fields_to_save[] = $field;
			add_settings_field(
				$option_name.'['.$field.']',
				'Plantilla',
				array( $this, 'input_textarea' ),
				$page,
				$section,
				array(
					'label_for' => $option_name.'['.$field.']',
					'field' => $field,
					'default' => $template,
					'classes' => array('fcp_aviso_legal_plantilla')
				)
			);		


			register_setting( $group, $option_name, array($this, 'setting_validate') );

        }

		function replace_content($content){
			$fc_power_aviso_legal_options = get_option('fc_power_aviso_legal');	
			if(isset($fc_power_aviso_legal_options['pagina']) && get_the_ID() == $fc_power_aviso_legal_options['pagina']){
				$replace_vars = array();
				foreach($fc_power_aviso_legal_options as $k=>$var){
					$replace_vars['%%'.strtoupper($k).'%%'] = $var;
				}	
				$replace_vars['%%WWWWW%%'] = '<i>' . site_url() . '</i>';
				$template = $fc_power_aviso_legal_options['plantilla'];
				$content = str_replace(array_keys($replace_vars), array_values($replace_vars), $template);
			}
			return $content;
		}
		
        function fc_power_view() {
            include( dirname(__FILE__).'/edit-view.php' );
        }
	
    }

}

new FCAvisoLegal();