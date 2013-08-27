<?php
/*
Plugin Name: TIEventos Ticker
Plugin URI:  https://github.com/odiego/tieventos-ticker
Description: Um <a href="widgets.php">widget</a> para exibir uma lista dos eventos de TI que estão acontecendo no Brasil.
Author: Diego de Souza Nunes
Version: 1.0
Author URI: http://www.tieventos.com.br
*/

/**
 * Copyright (c) 2013, TIEventos.com.br All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

/* TIEventosTicker Constructor */
class TIEventosTickerWidget extends WP_Widget {
	
	function TIEventosTickerWidget() { 
		$widget_options = array(
			'classname' => 'tieventos_ticker',
			'description' => 'Widget que exibe os eventos da TIEventos.com.br em forma de ticker.'
		);
		parent::WP_Widget(false, $name = 'TIEventos Widget', $widget_options); 
	}
	
	function form($instance) {
		// outputs the options form on admin
		$title          = esc_attr($instance['title']);
		$eventsquantity = esc_attr($instance['eventsqtd']);
		$enabledetails  = esc_attr($instance['enabledetails']);
		$effecttype     = esc_attr($instance['effecttype']);
		$fontsize       = esc_attr($instance['fontsize']);
		//TODO: add option to order the events list, add option to show or hide some event info
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
			<?php _e('Título do Widget:', 'tieventos-ticker'); ?>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('eventsqtd'); ?>"><?php _e('Exibir:', 'tieventos-ticker'); ?></label>
			<input class="small-text" id="<?php echo $this->get_field_id('eventsqtd'); ?>" name="<?php echo $this->get_field_name('eventsqtd'); ?>" type="number" min="1" value="<?php echo ( $eventsquantity ) ? $eventsquantity : '4'; ?>" /> eventos
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('fontsize'); ?>"><?php _e('Tamanho da Fonte:', 'tieventos-ticker'); ?></label>
			<input class="small-text" id="<?php echo $this->get_field_id('fontsize'); ?>" name="<?php echo $this->get_field_name('fontsize'); ?>" type="number" min="10" value="<?php echo ( $fontsize ) ? $fontsize : '10'; ?>" /> px
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('effecttype'); ?>"><?php _e('Qual o tipo de efeito?', 'tieventos-ticker'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'effecttype' ); ?>" name="<?php echo $this->get_field_name( 'effecttype' ); ?>">
				<option value="fade"<?php selected( $instance['effecttype'], 'fade' ); ?>><?php _e('Fade In / Fade Out', 'tieventos-ticker'); ?></option>
				<option value="movedown"<?php selected( $instance['effecttype'], 'movedown' ); ?>><?php _e('Mover lista para cima', 'tieventos-ticker'); ?></option>
				<!--<option value="movesides"<?php //selected( $instance['effecttype'], 'movesides' ); ?>><?php //_e('Mover lista para direita', 'tieventos-ticker'); ?></option> -->
			</select>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['enabledetails'], true ); ?> id="<?php echo $this->get_field_id( 'enabledetails' ); ?>" name="<?php echo $this->get_field_name( 'enabledetails' ); ?>" /> <label for="<?php echo $this->get_field_id( 'enabledetails' ); ?>"><?php _e('Link para página de detalhes do evento', 'tieventos-ticker'); ?></label><br />
		</p><?php
	}
	
	function update($new_instance, $old_instance) {
		// processes widget options to be saved
		$instance = $old_instance;
		$instance['title']         = strip_tags($new_instance['title']);
		$instance['eventsqtd']     = $new_instance['eventsqtd'];
		$instance['fontsize']      = $new_instance['fontsize'];
		$instance['enabledetails'] = $new_instance['enabledetails'];
		$instance['effecttype']    = strip_tags($new_instance['effecttype']);
		return $instance;
	}
	
	function widget($args, $instance) {
		// outputs the content of the widget
		extract( $args );
		$uri            = 'http://tieventos.com.br/tieventos/api/events';
		$title          = apply_filters('widget_title', $instance['title']);
		$eventsquantity = $instance['eventsqtd'];
		$enabledetails  = $instance['enabledetails'];
		$effecttype     = $instance['effecttype'];	
		$fontsize       = $instance['fontsize'];
		$content        = file_get_contents($uri);
		$feed           = json_decode($content);
		$detailUri      = '#';
		$target         = '';
	
		$output = array();
		$output[] = $before_widget;
		if ($title)
			$output[] = $before_title . $title . $after_title;

		$output[] = '<div class="tieventos-ticker" data-qtd="'.$eventsquantity.'" data-effect="'.$effecttype.'">';
		$output[] = '<ul>';
		foreach ($feed as $event){
			if ($enabledetails == true){
				$detailUri = 'http://www.tieventos.com.br/eventos/' . $event->id;
				$target    = ' target=_blank';				
			}			
			$eventdate = date_parse($event->date_start);
			$pfx_date = get_the_date( $event->date_start );
			$eventdate =  $eventdate["day"] . " de " . date("F", mktime(0, 0, 0, $eventdate["month"], 10)) . " de " . $eventdate["year"];
			$output[] = '<li>';
				$output[] = '<div class="tieventos-image">';
					$output[] = '<a href="'.$detailUri.'" '. $target .'><img width="45" height="45" src='. $event->logo_url .'></a>';
				$output[] = '</div>';
				$output[] = '<div class="tieventos-details">';
					$output[] = '<h5 style="font-size: '.$fontsize.'px !important;"><a href="'.$detailUri.'" '. $target .'><b>'. $event->name . '</b> - '. $event->address_city .' / ' . $event->address_state . '</a></h5>';
					$output[] = '<span>'. $eventdate .'</span>';				
				$output[] = '</div>';
			$output[] = '</li>';			
		}
		$output[] = '</ul>';	
		$output[] = '</div><!-- .tieventos_ticker -->';
		$output[] = $after_widget;

		echo implode('',$output);
	
	}
}

/* Register plugin's widget */
function tieventos_ticker_register_widget(){
	register_widget( 'TIEventosTickerWidget' );
}
add_action( 'widgets_init', 'tieventos_ticker_register_widget' );
add_action( 'wp_enqueue_scripts', 'tieventos_add_my_css_and_js' );

/**
* Enqueue plugin style-file and js-file
*/
function tieventos_add_my_css_and_js() {
	// Respects SSL, Style.css is relative to the current file
	wp_register_style( 'tieventos-style', plugins_url('tieventos-style.css', __FILE__));
	wp_register_script( 'tieventos-script', plugins_url('tieventos-javascript.js', __FILE__ ), array('jquery'), '', true);  
	wp_enqueue_style( 'tieventos-style' );
	wp_enqueue_script( 'tieventos-script' );  
}
?>