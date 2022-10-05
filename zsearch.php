<?php
/**
* Plugin Name: Zsearch
* Plugin URI: https://dayzsolutions.com/
* Description: search product for woocommerce.
* Version: 0.1
* Author: Dayz
* Author URI: https://dayzsolutions.com/
**/

class REST_API_Widget extends WP_Widget {
 
    /**
    * Sets up the widgets name etc
    */
    public function __construct() {
        $widget_ops = array( 
        'classname' => 'rest-api-widget',
        'description' => 'A REST API widget that pulls posts from a different website');
    
        parent::__construct( 'rest_api_widget', 'REST API Widget', $widget_ops );
    }
    
    
    
    /**
    * Outputs the content of the widget
    *
    * @param array $args
    * @param array $instance
    */
    public function widget( $args, $instance ) {
        // outputs the content of the widget
        if ( isset ( $_GET['a'] ) && !empty( $_GET['a'] ) ) {
            $response = wp_remote_get( 'http://website-with-api.com/wp-json/wp/v2/ht_kb?filter[s]='.$_GET['a'] );
        } else {
            $response = wp_remote_get( 'http://website-with-api.com/wp-json/wp/v2/ht_kb/' );
        }
    
        if( is_wp_error( $response ) ) {
            return;
        }
    
        $posts = json_decode( wp_remote_retrieve_body( $response ) );
    
        if( empty( $posts ) ) {
            return;
        }
    
        echo $args['before_widget'];
        if( !empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
        }
     
        // Main Widget Content Here
        get_search_form();
    
        if( !empty( $posts ) ) { 
            echo '<ul>';
            foreach( $posts as $post ) {
                echo '<li><a href="' . $post->link. '">' . $post->title->rendered . '</a></li>';
            }
            echo '</ul>'; 
        }
        echo $args['after_widget'];
    }
    
    /**
    * Outputs the options form on admin
    *
    * @param array $instance The widget options
    */
    public function form( $instance ) {
        // outputs the options form on admin
    
        $title = ( !empty( $instance['title'] ) ) ? $instance['title'] : '';
        ?>;
            <label for="<?php echo $this->get_field_name( 'title' ); ?>">Title: </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" 
         name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        <?php
        }
   }
    
   function api_search_form( $form ) {
    
    $form = '<form role="search" method="get" class="search-form" action="' . home_url( '/' ) . '">
            <label>
                <span class="screen-reader-text">' . _x( 'Search for:', 'label' ) . '</span>
                <input type="search" class="search-field" placeholder="' . esc_attr_x( 'Search …', 'placeholder' ) .'" value="' . get_search_query() . '" name="a" title="' . esc_attr_x( 'Search                 for:', 'label' ) .'" />
            </label>
            <button type="submit" class="search-submit"><span class="screen-reader-text">Search</span></button>
            </form>';
    
    return $form;
   }
   add_filter( 'get_search_form', 'api_search_form', 100 );
    
    
   add_action( 'widgets_init', function(){
    register_widget( 'REST_API_Widget' );
   });

