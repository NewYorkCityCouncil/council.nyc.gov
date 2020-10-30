<?php


if ( ! defined('ABSPATH')) exit;  // if direct access

class WidgetAccordions extends WP_Widget {

	function __construct() {
		
		parent::__construct( 'widget_accordions', __('Accordions', 'accordions'), array( 'description' => __( 'Show Accordions', 'accordions' ), ) );
	}

	public function widget( $args, $instance ) {
		
		$title 			= apply_filters( 'widget_title', $instance['title'] );
		$accordion_id	= isset( $instance[ 'accordion_id' ] ) ? $instance[ 'accordion_id' ] : '';
		
		echo $args['before_widget'];
		if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];
		echo do_shortcode("[accordions id='$accordion_id']");
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		
		$title 			= isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'Accordions', 'accordions' );
		$accordion_id	= isset( $instance[ 'accordion_id' ] ) ? $instance[ 'accordion_id' ] : '';
		$accordions		= get_posts( array( 'posts_per_page' => -1, 'post_type' => 'accordions' ) );
				
		echo "<p>";
		echo "<label for='{$this->get_field_id( 'title' )}'>".__('Title','accordions')." : </label>";
		echo "<input class='widefat' id='{$this->get_field_id( 'title' )}' name='{$this->get_field_name( 'title' )}' type='text' value='{$title}' />";
		echo "</p>";
		
		echo "<p>";
		echo "<label for='{$this->get_field_id( 'accordion_id' )}'>".__('Select Accordion','accordions')." : </label>";
		echo "<select name='{$this->get_field_name( 'accordion_id' )}' id='{$this->get_field_id( 'accordion_id' )}' class='widefat'>";
		
		foreach( $accordions as $accordion ){
			
			$selected = $accordion_id == $accordion->ID ? 'selected' : '';
			echo "<option value='{$accordion->ID}' $selected>{$accordion->post_title}</option>";
		}
		
		echo "</select>";
		echo "</p>";
	}
	
	public function update( $new_instance, $old_instance ) {
		
		$instance = array();
		$instance['title'] 			= isset( $new_instance['title'] ) 			? strip_tags( $new_instance['title'] ) : '';
		$instance['accordion_id'] 	= isset( $new_instance['accordion_id'] ) 	? strip_tags( $new_instance['accordion_id'] ) : '';
		return $instance;
	}
}