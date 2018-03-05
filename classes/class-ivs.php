<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('ivsPlugin')) {
	class ivsPlugin {

	    public $lastkey;
	    public $tags;
	    public $images;

		public function __construct() {
			$this->initHooks();
		}

		public function initHooks() {
			add_action('admin_enqueue_scripts', array($this, 'ivsAdminScripts'));

			add_action('wp_ajax_plupload_action', array($this, "g_plupload_action"));

			add_action('add_meta_boxes', function(){
				add_meta_box('gallery_photos', __('Image Gallery'), array($this, 'upload_meta_box'), 'gallery', 'normal', 'high');
			});

			add_action('wp_ajax_photo_gallery_upload', array($this, 'handelUploadedFile'));

			add_action('save_post', array($this, 'ivsSaveMetaBox' ));
		}

		public function ivsAdminScripts(){
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-droppable');
			wp_enqueue_script('plupload-all');
			wp_register_script('myplupload', IVS_BASE_URL . '/assets/js/myplupload.js', array('jquery'));
			wp_enqueue_script('myplupload');
			wp_enqueue_style('myplupload', IVS_BASE_URL . '/assets/css/myplupload.css');
			wp_enqueue_script( 'admin-scripts', IVS_BASE_URL . '/assets/js/admin-scripts.js', array(), '2018', true );

		}

		public function loadImagesList() {
			add_thickbox();
		    if($this->images) {
			    foreach ( $this->images as $key => $image ) {
				    echo "<li>";
				    echo "<div style=\"max-height:150px;  min-height:150px; overflow: hidden\"><a href=\"#TB_inline?width=500&height=400&inlineId=ivs-options-$key\" class=\"thickbox\">";
				    echo "<img class=\"alignnone size-medium wp-image-" . $image['id'] . "\" src=\"" . $image['url'] . "\" width=\"150\" height=\"auto\"></a><span class=\"close\"></span></div>";
				    echo "<input type=\"hidden\" name=\"images[$key][id]\" value=\"" . $image['id'] . "\">";
                    echo "<input type=\"hidden\" name=\"images[$key][url]\" value=\"" . $image['url'] . "\">"; ?>
                    <div id="ivs-options-<?php echo $key; ?>" style="display:none;">
                    <p>
                    <table class="widefat">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Show in</th>
                                    <th>Filtering Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <img class="alignnone size-medium wp-image-$image" src="<?php echo $image['url']; ?>" width="150" height="auto">
                                </td>
                                <td>
                                    Destacado <input id="img_dest_<?php echo $key; ?>" type="checkbox" name="images[<?php echo $key; ?>][dest]" value="on" <?php if(isset($image['dest'])) checked("on", $image['dest']); ?>><br/>
                                    Durante <input id="img_dest_<?php echo $key; ?>" type="checkbox" name="images[<?php echo $key; ?>][streaming]" value="on" <?php if(isset($image['streaming'])) checked("on", $image['streaming']); ?>><br/>
                                    Despues <input id="img_dest_<?php echo $key; ?>" type="checkbox" name="images[<?php echo $key; ?>][after]" value="on" <?php if(isset($image['after'])) checked("on", $image['after']); ?>><br/>
                                </td>
                                <td>
	                                <?php if($this->tags) { ?>
                                        Tags
                                        <select multiple class="tagselector" size="6" id="img_tags_<?php echo $key; ?>" type="text" name="images[<?php echo $key; ?>][tags][]">
                                            <?php foreach ($this->tags as $i => $tag) { ?>
                                                <option value="<?php echo $tag->term_id; ?>" <?php echo (isset($image['tags']) && in_array($tag->term_id, $image['tags'])) ? "selected" : ""; ?>><?php echo $tag->name; ?></option>
                                            <?php } ?>
                                        </select><br/>
                                        <sup>Usa Ctrl para seleccionar más de uno.</sup>
	                                <?php } else { ?>
	                                    No Tags Created
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                    </p>
                    </div>
                    <?php echo "</li>";
			    }

		    }
        }

		public function upload_meta_box(){
			global $post;
			$this->tags = get_terms( array( 'post_types' => 'gallery', 'taxonomy' => 'tag', 'hide_empty' => false ) );
			$this->images = get_post_meta($post->ID, 'gallery-details', true);
			$this->lastkey = count($this->images);
			?>
            <div class="meta"><ul data-key="<?php echo $this->lastkey; ?>">
                    <?php $this->loadImagesList(); ?>
                </ul></div>
            <div class="clear clearfix"></div>
            <div id="plupload-upload-ui" class="hide-if-no-js">
                <div id="drag-drop-area">
                    <div class="drag-drop-inside">
                        <p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
                        <p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
                        <p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" /></p>
                    </div>
                </div>
            </div>

			<?php

			$plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'plupload-browse-button',
				'container'           => 'plupload-upload-ui',
				'drop_element'        => 'drag-drop-area',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'max_file_size'       => wp_max_upload_size().'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// additional post data to send to our ajax hook
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'action'      => 'photo_gallery_upload',            // the ajax action name
				),
			);

			// we should probably not apply this filter, plugins may expect wp's media uploader...
			$plupload_init = apply_filters('plupload_init', $plupload_init);
			$tags = get_terms( array( 'post_types' => 'images', 'taxonomy' => 'video_tags', 'hide_empty' => false ) );
            ?>

            <script type="text/javascript">

                jQuery(document).ready(function($){

                    var lastkey = $('.meta ul').attr('data-key');
                    // create the uploader and pass the config from above
                    var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

                    // checks if browser supports drag and drop upload, makes some css adjustments if necessary
                    uploader.bind('Init', function(up){
                        var uploaddiv = $('#plupload-upload-ui');

                        if(up.features.dragdrop){
                            uploaddiv.addClass('drag-drop');
                            $('#drag-drop-area')
                                .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                                .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                        }else{
                            uploaddiv.removeClass('drag-drop');
                            $('#drag-drop-area').unbind('.wp-uploader');
                        }
                    });

                    uploader.init();

                    // a file was added in the queue
                    uploader.bind('FilesAdded', function(up, files){
                        var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);

                        plupload.each(files, function(file){
                            if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
                                // file size error?

                            }else{

                                // a file was added, you may want to update your DOM here...
                                console.log(lastkey);

                            }
                        });

                        up.refresh();
                        up.start();
                    });

                    // a file was uploaded
                    uploader.bind('FileUploaded', function(up, file, response) {
                      var obj = $.parseJSON(response['response']);
                      var imgurl = '<div style="max-height:150px; min-height:150px; overflow: hidden"><a href=\"#TB_inline?width=500&height=400&inlineId=ivs-options-'+ lastkey +'" class="thickbox"><img class="alignnone size-medium wp-image-' + obj.id + '" src="' + obj.url + '" width="150" height="auto"></a><span class="close"></span></div>';
                      var hidden = '<input type="hidden" name="images[' + lastkey + '][id]" value="' + obj.id + '"><input type="hidden" name="images[' + lastkey + '][url]" value="' + obj.url + '">';
                      var options = '<div id="ivs-options-' + lastkey +'" style="display:none;"><p><table class="widefat"><thead><tr><th>Image</th><th>Show in</th><th>Filtering Tags</th></tr></thead><tbody><tr><td><img class="alignnone size-medium wp-image-$image" src="' + obj.url + '" width="150" height="auto"></td><td>Destacado<input id="img_dest_' + lastkey + '" type="checkbox" name="images[' + lastkey + '][dest]" value="on"><br/>Durante <input id="img_dest_' + lastkey + '" type="checkbox" name="images[' + lastkey + '][streaming]" value="on"><br/>Despues <input id="img_dest_' + lastkey + '" type="checkbox" name="images[' + lastkey + '][after]" value="on"><br/></td>';
						<?php if($this->tags) { ?>
                        options += '<td>Tags<select multiple class="tagselector" size="6" id="img_tags_' + lastkey + '" type="text" name="images[' + lastkey + '][tags][]">';
                            <?php foreach ($this->tags as $i => $tag) { ?>
                                    options += '<option value="<?php echo $tag->term_id; ?>"><?php echo $tag->name; ?></option>';
                            <?php } ?>
                        options += '</select><br/><sup>Usa Ctrl para seleccionar más de uno.</sup></td>';
                        <?php } else { ?>
                        options += 'No Tags Created';
		                <?php } ?>
                        options += '</tr></tbody></p></div>';
                      $('.meta ul').append('<li>' + imgurl + hidden + options + '</li>');
                      var newkey = lastkey++;
                      $('.meta ul').attr('data-key', newkey);
                    });

                });

            </script>
			<?php
		}

		public function ivsLoadWidget() {
			register_widget( 'unir_video_event' );
			register_widget( 'unir_cfform_widget');
		}


		public function handelUploadedFile() {

				check_ajax_referer('photo-upload');

				// you can use WP's wp_handle_upload() function:
				$file = $_FILES['async-upload'];
				$status = wp_handle_upload($file, array('test_form'=>true, 'action' => 'photo_gallery_upload'));

				// and output the results or something...
				$result = array('path' => $status['file'], 'url' => $status['url']);
				$result['id'] = wp_insert_attachment( array(
						'post_mime_type' => $status['type'],
						'post_title' => preg_replace('/\.[^.]+$/', '', basename($file['name'])),
						'post_content' => '',
						'post_status' => 'inherit'
					), $status['file']);

				echo json_encode($result);

				exit;
			}

		public function ivsSaveMetaBox($post_id) {
			if(isset($_POST['images'])) {
				$image = $_POST['images'];
				$image = array_values($image);
				foreach ($image as $key => $img) {
					if(empty($img['url']))
						unset($image[$key]);
				}
				update_post_meta( $post_id, 'gallery-details', $image);
			}
        }
    }
}