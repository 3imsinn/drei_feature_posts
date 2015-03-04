<?php 

global $wpdb; 


if (isset($_GET['uid'])) { 

            $id = $_GET['uid']; 
            // Prepare query to retrieve bugs from database

            $feature_query = 'select * from ' ;
            $feature_query .= $wpdb->get_blog_prefix();
            $feature_query .= 'dreiimsinnfeaturedposts ' ;    
            $feature_query .= 'where uid ='.$id ;
            $feature_items  = $wpdb->get_row($feature_query); 

            // Alle Posts dieses Features
            $itemPosts = drei_getPosts($id); 
            
    
}
            


?>

    <script type="text/javascript">
    jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#image_url').val(image_url);
        });
        });
    
  
        
//        jQuery('.drei_list').sortable({
//            items: '.list_item',
//            opacity: 0.5,
//            cursor: 'pointer',
//            axis: 'y',
//            update: function() {
//                var ordr = jQuery(this).sortable('serialize') + '&action=feature_action_callback';
//                jQuery.post(ajaxurl, ordr, function(response){
//                    alert(response);
//                });
//            }
//      });
        
        
        
        
        
    var resourceSort = $('.drei_list');
 
	resourceSort.sortable({
		update: function(event, ui) {
			$('#loading-animation').show(); // Show the animate loading gif while waiting

			opts = {
				url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
				type: 'POST',
				async: true,
				cache: false,
				dataType: 'json',
				data:{
					action: 'feature_action', // Tell WordPress how to handle this ajax request
					order: resourceSort.sortable('toArray').toString() // Passes ID's of list items in	1,3,2 format
				},
				success: function(response) {
					$('#loading-animation').hide(); // Hide the loading animation
					
                   return; 
			},
				error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
					alert('There was an error saving the updates'+response);
					$('#loading-animation').hide(); // Hide the loading animation
					return; 
				}
			};
           
			$.ajax(opts);
		
        }
	});	    
        
        
    
    });
        
        
        
        
        
        
        
    </script>

<div>
        <fieldset>
            <form id="feature_form" method="post" action="admin.php?page=3imsinn_featured_posts">
            <input type="hidden" name="action" value="admin_post_save_feature">

                <h3><?php if(isset($id)){ echo "Edit"; } else{ echo "Add";} ?> Feature</h3>  
                                    
                <input type="hidden" name="add" value="<?php if(isset($id)){ echo $id; } else{ echo "1";} ?>">
                <div>
                    <label><strong>Titel</strong><br>
                    <input type="text" name="title" class="widefat" value="<?php if(isset($id)){ echo $feature_items->title; } ?>" />
                </div>
                <br><br>
                <div>
                    <label for="txt"><strong>Text</strong></label><br>
                    <textarea class="widefat" rows="5" cols="50" name='txt'><?php if(isset($id)){ echo $feature_items->txt; } ?></textarea>
                </div>
                <p>
                    <strong> Alle Artikel zum Feature</strong>
                    <br/>
                    <ul class="drei_list drei_white" >
                    <?php 
                    
                    if(isset($itemPosts))
                    {
                        foreach($itemPosts as $post)
                        {
                            echo '<li id="'.$post['ID'].'" class="list_item">'.$post['post_title'].'</li>';
                        }
                    }
                    ?>  
                    </ul>
                </p>
                    
                    
                <p><br/><br/>
                    <label for="image_url"><strong>Bild zum Feature</strong></label><br>
                    <?php if(isset($id)){ echo '<img src="'.$feature_items->image_url.'" width="150"><br/>'; } ?>
                    <input type="text" name="image_url" id="image_url" value="<?php if(isset($id)){ echo $feature_items->image_url; } ?>" class="regular-text">
                    <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
                </p>
            
                <p>
                    <input type="submit" value="Save" class="button button-primary" name="submit" />
                </p>
            </form>
        </fieldset>        
    </div>
</div>