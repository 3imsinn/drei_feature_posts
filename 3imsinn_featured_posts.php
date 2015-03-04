<?php
/*
Plugin Name: 3imsinn Featured Posts
Plugin URI: http://www.3imsinn.de/featuredPosts
Description: The Plugin collects posts to a feature.
Version: 1.0
Author: Michael Schneider - behaltet Dieses Plugin 3imsinn
Author URI: http://www.3imsinn.de/michaelschneider
License: free for private & commercial use
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : michael.schneider@3imsinn.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


    // CSS Erweiertering wird intitalisiert
    function drei_register_styles()
    {
        wp_register_style('drei_box', plugins_url('/css/drei_box.css', __FILE__), array(), '', 'all' );
        wp_register_style('drei_box', get_template_directory_uri(). '/css/drei_box.css', array(), '', 'all' );
        wp_enqueue_style('drei_box');
        
    }

    add_action( 'wp_enqueue_scripts', 'drei_register_styles' ); 
    add_action( 'wp_ajax_feature_action', 'feature_action_callback' );
    
    // Ansicht des Feature im Backend-Edit
    add_filter('the_content', 'dreiimsinn_feature_display'); 



    // Ajax-Callback für Sortierung der Posts im Feature-Backend 
    function feature_action_callback() 
    {
	   global $wpdb;
	
		$order = explode(',', $_POST['order']);
		$counter = 0;
	
        foreach ($order as $post_id) {
			$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $post_id) );
			$counter++;
		}
         
		return true;
        wp_die(); 
    }


    



    // Aufruf der Datenbank -> Select Posts & Postmeta
    function drei_getPosts($id) {
            global $wpdb;	
            
            $table_posts = $wpdb->get_blog_prefix().'posts'; 
            $table_postmeta = $wpdb->get_blog_prefix().'postmeta'; 
            
            $getFeatureID = $id;
    
           
            $featureName_query = 'select * from ' ;
            $featureName_query .= $wpdb->get_blog_prefix();
            $featureName_query .= 'dreiimsinnfeaturedposts ' ;    
            $featureName_query .= 'where uid = %d  ' ;
            $searchName_term = '';
            $featureName_query .= "";
            $featureName_items  = $wpdb->get_results( $wpdb->prepare( $featureName_query, $getFeatureID, $searchName_term, $searchName_term ), ARRAY_A );
          
	
            $feature_query = 'select * from ' ;
            $feature_query .= $table_posts.','.$table_postmeta ;
            $feature_query .= ' WHERE '.$table_postmeta.'.meta_value = %d AND '  ;
            $feature_query .= $table_posts.'.ID = '.$table_postmeta.'.post_id AND '  ;
            $feature_query .= $table_postmeta.'.meta_key = \'featuredPost\''  ;
            
            $search_term = ' ';
            $feature_query .= ' ORDER by '.$table_posts.'.menu_order ASC';
            $feature_items  = $wpdb->get_results( $wpdb->prepare( $feature_query, $getFeatureID , $search_term, $search_term ), ARRAY_A );
            
            return  $feature_items;
        }


        // Generiert die Ansicht für die Ausgabe im Theme 
        function dreiimsinn_feature_display($content) 
        {
            global $wpdb;	
            
            $table_posts = $wpdb->get_blog_prefix().'posts'; 
            $table_postmeta = $wpdb->get_blog_prefix().'postmeta'; 
            
            $getFeatureID = get_post_meta(get_the_ID(), 'featuredPost', true);
    
            if($getFeatureID != "" AND is_single()){
            $featureName_query = 'select * from ' ;
            $featureName_query .= $wpdb->get_blog_prefix();
            $featureName_query .= 'dreiimsinnfeaturedposts ' ;    
            $featureName_query .= 'where uid = %d  ' ;
            $searchName_term = '';
            $featureName_query .= "ORDER by uid DESC";
            $featureName_items  = $wpdb->get_results( $wpdb->prepare( $featureName_query, $getFeatureID, $searchName_term, $searchName_term ), ARRAY_A );
          
	
            $feature_query = 'select * from ' ;
            $feature_query .= $table_posts.','.$table_postmeta ;
            $feature_query .= ' WHERE '.$table_postmeta.'.meta_value = %d AND '  ;
            $feature_query .= $table_posts.'.ID = '.$table_postmeta.'.post_id AND '  ;
            $feature_query .= $table_postmeta.'.meta_key = \'featuredPost\''  ;
            
            $search_term = ' ';
            $feature_query .= ' ORDER by '.$table_posts.'.menu_order ASC';
            $feature_items  = $wpdb->get_results( $wpdb->prepare( $feature_query, $getFeatureID , $search_term, $search_term ), ARRAY_A );

            if(isset($featureName_items[0])){        
            $box = '<div class="drei_feature_box">
                    <div class="drei_feature_box_header">
                        <h3>Feature: '.$featureName_items[0]['title'].'</h3>
                    </div>
                    
                    <div class="drei_feature_box_body">';
            
                $box .= '<div class="drei_feature_box_body_left" > <img src="'.$featureName_items[0]['image_url'].'" width="200" ><br>                                            <i>'.$featureName_items[0]['txt'].'</i><br>
                        </div>
                        <div class="drei_feature_box_body_right"> <ul>';
    
                   
                foreach($feature_items as $item){
                $box .= '<li class="drei_list"><a href="'.$item['guid'].'">'.$item['post_title'].'</a> </li>';    
                }
            
                $box .= '</ul><div style="clear:both;"></div></div>
                        </div>
                        </div>
                        <br>';    

                $content = $content.' <br><br>'.$box;  	
            }
            }
            return $content; 
            }





        // Registrierung der Select-Box im Edit-Backend
        function prfx_custom_meta() 
        {
            add_meta_box( 'prfx_meta', __( 'Add Article to a feature?', 'prfx-textdomain' ), 'prfx_meta_callback', 'post' );
        }

        add_action( 'add_meta_boxes', 'prfx_custom_meta' );

        
        // Callback der Select-Box im Edit-Backend
        function prfx_meta_callback( $post ) 
        {
            global $wpdb;
            wp_nonce_field( basename( __FILE__ ), 'prfx_nonce' );
            $prfx_stored_meta = get_post_meta( $post->ID );

            $feature_query = 'select * from ' ;
            $feature_query .= $wpdb->get_blog_prefix();
            $feature_query .= 'dreiimsinnfeaturedposts ' ;    
            $feature_query .= 'where uid != %d  ' ;
            $search_term = '';
            $feature_query .= "ORDER by uid DESC";
            $feature_items  = $wpdb->get_results( $wpdb->prepare( $feature_query, $id = null, $search_term, $search_term ), ARRAY_A );

            echo'
            <table>
            <tr>
             <td style="width: 100px">Feature:</td>
             <td>
            
            <select name="featuredPost">
            <option value="0">select a feature</option>
            ';             
            if ( $feature_items ) {
                foreach ( $feature_items as $feature_item ) {
                 echo '<option value="'.$feature_item['uid'].'"'; 
                    if(isset($prfx_stored_meta['featuredPost'][0]) AND ($prfx_stored_meta['featuredPost'][0] == $feature_item['uid']))
                     { echo 'selected';} 
                 else {}
                    echo '>'.$feature_item['title'].'</option>';
                    }
                }
    
              echo'</select>
             </td>
            </tr>
            </table>
            ';
            }

        // Sichern der Select-Box im Edit-Backend
        function prfx_meta_save( $post_id )
        {
            $is_autosave = wp_is_post_autosave( $post_id );
            $is_revision = wp_is_post_revision( $post_id );
            $is_valid_nonce = ( isset( $_POST[ 'prfx_nonce' ] ) && wp_verify_nonce( $_POST[ 'prfx_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
            if ( $is_autosave || $is_revision || !$is_valid_nonce )
            {
                return;
            }
 
            if( isset( $_POST[ 'featuredPost' ] ) ) 
            {
                update_post_meta( $post_id, 'featuredPost', $_POST[ 'featuredPost' ] );
            }
        }

    // Save der Select Box registrieren    
    add_action( 'save_post', 'prfx_meta_save' );


    

    // Feature Objekt erzeugen
    $object = new DreiimsinnFeaturedPosts();


    class DreiimsinnFeaturedPosts{

        public function __construct()
        {
            register_activation_hook( __FILE__, array($this, 'DreiimsinnFeaturedPosts_activation'));
            add_action('init', array($this, 'init'));
        }

        public function init()
        {
            add_action('admin_menu',  array($this, 'addMenu'));
        } 

        function feature_register_meta_box()
        {
            add_meta_box(
            'feature_meta_box',      
            esc_html__( 'Feature', 'example' ),    
            array($this, 'feature_meta_box'),   
            'post',         
            'side',         
            'default'     
            );
        wp_register_style('drei_box', plugins_url('/css/drei_box.css', __FILE__));
        }

        function feature_meta_box($post){
        global $wpdb;

        if ( $post->post_type == 'post' || $post->post_type == 'page' ) {
            $metavalue = get_post_meta($post->ID, 'featuredPosts', true);
            var_dump($post);
             if( isset( $_POST[ 'featuredPosts' ] ) ) {
               update_post_meta( $post_id, 'featuredPosts', sanitize_text_field( $_POST[ 'feturedPosts' ] ) );
            }
        }

        $featured_id = esc_html( get_post_meta( $post->ID, 'FeaturedPosts', true ) );

            $feature_query = 'select * from ' ;
            $feature_query .= $wpdb->get_blog_prefix();
            $feature_query .= 'dreiimsinnfeaturedposts ' ;    
            $feature_query .= 'where uid != %d  ' ;
            $search_term = '';
            $feature_query .= "ORDER by uid DESC";
            $feature_items  = $wpdb->get_results( $wpdb->prepare( $feature_query, $id = null, $search_term, $search_term ), ARRAY_A );

            echo'<table>
            <tr>
             <td style="width: 100px">Feature:</td>
             <td>
            
            <select name="featuredPosts">
            <option value="">none</option>
            ';             
            if ( $feature_items ) {
                foreach ( $feature_items as $feature_item ) {
                 echo '<option value="'.esc_attr( $feature_item['uid'] ); 
                    if($featured_id == $feature_item['uid'])
                     { echo 'selected';} 
                    echo '">'.$feature_item['title'].'</option>';
                    }
                }
    
              echo'</select>
             </td>
            </tr>
            </table>
            ';
        }
        
    public function DreiimsinnFeaturedPosts_activation()
    {
        global $wpdb; 
        $this->DreiimsinnFeaturedPosts_create_table($wpdb->get_blog_prefix());
    }


    /**
     * Creates Database 
     */

    public function DreiimsinnFeaturedPosts_create_table($prefix){
      $creation_query =

        'CREATE TABLE ' . $prefix . 'dreiimsinnfeaturedposts (
            `uid` int(20) NOT NULL AUTO_INCREMENT,
            `txt` text,
            `title` VARCHAR( 128 ) NULL,
            `image_url` VARCHAR( 150 ) NULL,
            PRIMARY KEY (`uid`)
            );';

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $creation_query );
    
    }    


    public function addMenu(){
        add_menu_page('Feature', 'Feature', 10, '3imsinn_featured_posts',  array($this, 'Feature'),'', '4');
    }

    
    public static function Feature(){
        if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    
    wp_enqueue_script('jquery');
    wp_enqueue_media();


if(isset($_POST['action']) AND $_POST['action'] == "admin_post_save_feature")
        {
            
            global $wpdb;

                $feature_data = array();
                $feature_data['title'] = ( isset( $_POST['title'] ) ? $_POST['title'] : '' );
                $feature_data['txt'] = ( isset( $_POST['txt'] ) ? $_POST['txt'] : '' );
                $feature_data['image_url'] = ( isset( $_POST['image_url'] ) ? $_POST['image_url'] : '' );
                
            if ( isset( $_POST['add'] ) AND $_POST['add'] == '1' ){
                $wpdb->insert($wpdb->get_blog_prefix() . 'dreiimsinnfeaturedposts', $feature_data);
            }

            if ( isset($_POST['add'] ) AND $_POST['add']  > '1' ){
               
                $wpdb->update($wpdb->get_blog_prefix() . 'dreiimsinnfeaturedposts', 
                array( 
                    'title' => $feature_data['title'],  
                    'txt' => $feature_data['txt'],    
                    'image_url' => $feature_data['image_url']    

                    ),
                    array( 'uid' => $_POST['add'] ), 
                array( 
                    '%s',   
                    '%s',   
                    '%s'    
         
                        ), 
                array( '%d' ) 
                ); 


              }
        
        }
            
            if(isset($_POST['feature'])){
                global $wpdb;
                $features_to_delete = $_POST['feature'];
            
                foreach ( $features_to_delete as $feature_to_delete ) {
                   $wpdb->delete( $wpdb->get_blog_prefix() . 'dreiimsinnfeaturedposts', array( 'uid' => intval( $feature_to_delete ) ), $where_format = null );
                }
            }


        
    if(isset($_GET['statusFeature']) AND $_GET['statusFeature'] > "0"){
           include __DIR__."/templates/editfeature_template.php";
        }
     else{   
        include __DIR__."/templates/feature_template.php";
        }
    }

    
        public function shortcode(){
        return "add your image and html here...";
    }

    
}



?>