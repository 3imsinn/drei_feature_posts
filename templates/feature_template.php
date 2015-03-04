<?php 

global $wpdb; 

?>

<div class="wrap">
    <h2>Welcome to the Feature-Page</h2><a href="admin.php?page=3imsinn_featured_posts&statusFeature=1" class="add-new-h2">Add a new Feature</a>
    <br />
    <br />
    
    <?php
        $feature_items = array();
        $search_mode = false;

        if ( empty( $_GET['uid'] ) ) { 

            $feature_query = 'select * from ' ;
            $feature_query .= $wpdb->get_blog_prefix();
            $feature_query .= 'dreiimsinnfeaturedposts ' ;    
            $feature_query .= 'where uid != %d  ' ;

            // Add search string in query if present
            if ( $search_mode ) {
                $search_term = '%' . $search_string . '%';
                $bug_query .= "where uid like '%s' ";
                $bug_query .= "or title like '%s' ";
            } else {
                $search_term = '';
            }

            $feature_query .= "ORDER by uid DESC";

    $feature_items  = $wpdb->get_results( $wpdb->prepare( $feature_query, $id = null, $search_term, $search_term ), ARRAY_A );



            

    ?>


    <h3>Manage Features</h3>
    <form method="post" action="admin.php?page=3imsinn_featured_posts">
    <input type="hidden" name="action" value="delete_feature" />

    <!-- Adding security through hidden referrer field -->
    <?php wp_nonce_field( 'feature_deletion' ); ?>
    
    <table class="wp-list-table widefat fixed" >
    <thead>
        <tr>
            <th style="width: 50px"></th>
            <th style="width: 80px">ID</th>
            <th style=width: 300px>Title</th>
            <th>Description</th>
        </tr>
    </thead>

    <?php 
        // Display bugs if query returned results
        if ( $feature_items ) {
            foreach ( $feature_items as $feature_item ) {
                echo '<tr style="background: #FFF">';
                echo '<td><input type="checkbox" name="feature[]" value="';
                echo esc_attr( $feature_item['uid'] ) . '" /></td>';
                echo '<td>' . $feature_item['uid'] . '</td>';
                echo '<td><a href="admin.php?page=3imsinn_featured_posts&statusFeature=2&uid='.esc_attr( $feature_item['uid']).'">' . $feature_item['title'] . '</a></td>';
                echo '<td>' . $feature_item['txt'] . '</td></tr>';
            }
        } else {
            echo '<tr style="background: #FFF">';
            echo '<td colspan=4>No Feature Found</td></tr>';
        }
    ?>
    </table><br />
    
    
    <input type="submit" value="Delete Selected" class="button-primary"/>
    </form>

    
    <?php } elseif ( isset( $_GET['id'] ) && ( $_GET['id'] == "new" || is_numeric( $_GET['id'] ) ) ) {

        // Display bug creation and editing form if bug is new
        // or numeric id was sen       
        $bug_id = $_GET['id'];
        $bug_data = array();
        $mode = 'new';

        // Query database if numeric id is present
        if ( is_numeric( $bug_id ) ) {
            $bug_query = 'select * from ' . $wpdb->get_blog_prefix();
            $bug_query .= 'ch7_bug_data where bug_id = ' . $bug_id;

            $bug_data = $wpdb->get_row( $wpdb->prepare( $bug_query ), ARRAY_A );

            if ( $bug_data ) $mode = 'edit';
        } else {
            $bug_data['bug_title'] = '';
            $bug_data['bug_description'] = '';
            $bug_data['bug_version'] = '';
            $bug_data['bug_status'] = '';
        }

        // Display title based on current mode
        if ( $mode == 'new' ) {
            echo '<h3>Add New Bug</h3>';
        } elseif ( $mode == 'edit' ) {
            echo '<h3>Edit Bug #' . $bug_data['bug_id'] . ' - ';
            echo $bug_data['bug_title'] . '</h3>';
        }
        ?>

        <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <input type="hidden" name="action" value="save_ch7bt_bug" />
        <input type="hidden" name="bug_id" value="<?php echo esc_attr( $bug_id ); ?>" />

        <!-- Adding security through hidden referrer field -->
        <?php wp_nonce_field( 'ch7bt_add_edit' ); ?>

        <!-- Display bug editing form, with previous values if available -->
        <table>
            <tr>
                <td style="width: 150px">Title</td>
                <td><input type="text" name="bug_title" size="60" value="<?php echo esc_attr( $feature_data['title'] ); ?>"/></td>
            </tr>
            <tr>
                <td>Description</td>
                <td><textarea name="bug_description" cols="60"><?php echo esc_textarea( $feature_data['feature_description'] ); ?></textarea></td>
            </tr>
            <tr>
                <td>Bild</td>
            </tr>
            <td>
            </td>
        </tr>

        </table>
        <input type="submit" value="Submit" class="button-primary"/>
        </form>

    <?php } ?>
    

