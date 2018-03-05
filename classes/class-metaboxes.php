<?php

if(!class_exists('ivsMetaboxes')) {
	class ivsMetaboxes {
		public function __construct() {
			$this->initHooks();
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		public function initHooks() {
			add_action('add_meta_boxes', function(){  add_meta_box('ivs-Videos-Upload', 'Video Gallery',array($this, 'videosEditorSetUp'), 'videos','normal'); }, 9);
			add_action('save_post', array($this, 'saveMetaBox' ));
		}

		public function displayCallBack( $post ) {
			$gallery = get_post_meta($post->ID, 'gallery', true);
			$posts = get_posts(array(
				'post_per_page' => -1,
				'offset' => 0,
				'order' => 'DESC',
				'post_type' => 'gallery',
				'post_status' => 'publish'
			));
			$tags = get_terms( array( 'post_types' => 'gallery', 'taxonomy' => 'tag', 'hide_empty' => false ) );
			$vids = get_posts(array(
				'post_per_page' => -1,
				'offset' => 0,
				'order' => 'DESC',
				'post_type' => 'videos',
				'post_status' => 'publish'
			));
			$vidstags = get_terms( array( 'post_types' => 'videos', 'taxonomy' => 'video_tags', 'hide_empty' => false ) ); ?>
            <div class="draganddropmeta">
            <div class="tasks">
                <ul class="accordion droppable">
                    <li>Agrega Galerias de Fotos o de Videos</li>
                    <?php if($gallery) {
                        foreach ($gallery as $key => $gall) { ?>
                        <li>
				            <a class="toggle" href="#"><?php echo $key ?> - Galerias de <?php echo ($gallery[$key]['type'] == "video") ? "Videos" : "Imagenes"; ?></a>
                                   <ul class="inner">
                                       <table class="widefat">
                                           <thead>
                                           <tr>
                                               <th>
                                                   <h4>Título</h4>
                                               </th>
                                               <th>
                                                   <h4>Galería</h4>
                                               </th>
                                               <th>
                                                   <h4>Filtros por Tags</h4>
                                               </th>
                                           </tr>
                                           </thead>
                                           <tbody>
                                           <tr>
                                               <td>
                                                   <input type="text" class="newtitle" name="gallery[<?php echo $key; ?>][title]" value="<?php echo isset($gallery[$key]['title']) ? $gallery[$key]['title'] : ""; ?>">
                                               </td>
                                               <td>
                                                   <select class="galleryid" name="gallery[<?php echo $key; ?>][id]">
                                                    <option value="">Selecciona la Galería a Mostrar</option>
                                                       <?php if ($gallery[$key]['type'] == "video") {
                                                           foreach ( $vids as $posttype ) {
                                                               echo "<option value=\"" . $posttype->ID . "\" " . selected( $gall['id'], $posttype->ID ) . ">" . $posttype->post_title . "</option>";
                                                           }
                                                       } else {
                                                           foreach ( $posts as $posttype ) {
                                                               echo "<option value=\"" . $posttype->ID . "\" " . selected( $gall['id'], $posttype->ID ) . ">" . $posttype->post_title . "</option>";
                                                           }
                                                       }?>
                                                    </select>
                                               </td>
                                               <td>
                                                   Filtro por Tags <br><select multiple class="tagselector" size="6" type="text" name="gallery[<?php echo $key; ?>][tags][]">";
			                                           <?php
			                                           if ($gallery[$key]['type'] == "video") {
				                                           foreach ($vidstags as $i => $tag) {
					                                           echo "<option value=\"$tag->term_id\"";
					                                           echo isset($gall['tags']) && in_array($tag->term_id, $gall['tags']) ? "selected" : "";
					                                           echo ">$tag->name</option>";
				                                           }
			                                           } else {
				                                           foreach ( $tags as $i => $tag ) {
					                                           echo "<option value=\"$tag->term_id\"";
					                                           echo isset( $gall['tags'] ) && in_array( $tag->term_id, $gall['tags'] ) ? "selected" : "";
					                                           echo ">$tag->name</option>";
				                                           }
			                                           }?>
                                                   </select>
                                                   <br/><sup>Usa Ctrl para seleccionar más de uno.</sup>
                                               </td>
                                           </tr>
                                           </tbody>
                                       </table>
                                       <a href="#" class="delete-this">Eliminar</a></p>
                                       <input type="hidden" class="hidenfield" name="gallery[<?php echo $key; ?>][type]" value="<?php echo isset($gallery[$key]['type']) ? $gallery[$key]['type'] : ""; ?>">
                                   </ul>
                        </li>
                      <?php  }
                    } ?>
                </ul>
            </div>
            <br style="clear:both">
            <div class="options">
                <p>Opciones</p>
                <ul class="accordion draggable" data-array="<?php echo (isset($key)) ? $key + 1 : 0; ?>">
				        <li>
				            <a class="toggle notthis" href="#">Galerias de Imagenes</a>
                                   <ul class="inner">
                                       <table class="widefat">
                                           <thead>
                                            <tr>
                                                <th>
                                                    <h4>Título</h4>
                                                </th>
                                                <th>
                                                    <h4>Galería</h4>
                                                </th>
                                                <th>
                                                    <h4>Filtros por Tags</h4>
                                                </th>
                                            </tr>
                                           </thead>
                                           <tbody>
                                            <tr>
                                                <td>
                                                    <input type="text" class="newtitle">
                                                </td>
                                                <td>
                                                    <select  class="galleryid">
                                                        <option value="">Selecciona la Galería a Mostrar</option>
		                                                <?php foreach ($posts as $key => $posttype) {
			                                                echo "<option value=\"". $posttype->ID."\" ". selected($gallery, $posttype->ID) .">" . $posttype->post_title . "</option>";
		                                                } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select multiple class="tagselector" size="6" type="text">";
		                                                <?php foreach ($tags as $i => $tag) {
			                                                echo "<option value=\"$tag->term_id\">$tag->name</option>";
		                                                } ?>
                                                    </select>
                                                    <br/><sup>Usa Ctrl para seleccionar más de uno.</sup>
                                                </td>
                                            </tr>
                                           </tbody>
                                       </table>
                                       <input type="hidden" class="hidenfield" value="imagen">
                                   </ul>

                        </li>
                        <li>
                            <a class="toggle notthis" href="#">Galerias de Videos</a>
                            <ul class="inner">
                                <table class="widefat">
                                    <thead>
                                    <tr>
                                        <th>
                                            <h4>Título</h4>
                                        </th>
                                        <th>
                                            <h4>Galería</h4>
                                        </th>
                                        <th>
                                            <h4>Filtros por Tags</h4>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="newtitle">
                                        </td>
                                        <td>
                                            <select class="galleryid">
                                                <option value="">Selecciona la Galería a Mostrar</option>
                                                <?php foreach ($vids as $key => $posttype) {
                                                    echo "<option value=\"". $posttype->ID."\" ". selected($gallery, $posttype->ID) .">" . $posttype->post_title . "</option>";
                                                } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select multiple class="tagselector" size="6" type="text">";
                                                <?php foreach ($vidstags as $i => $vtag) {
                                                    echo "<option value=\"$vtag->term_id\">$vtag->name</option>";
                                                } ?>
                                            </select><br/><sup>Usa Ctrl para seleccionar más de uno.</sup>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <a href="#" class="delete-this">Eliminar</a></p>
                                <input type="hidden" class="hidenfield" value="video">
                            </ul>

                        </li>
                </ul>
            </div>
            </div>
            <?php
        }

		public function featuredVideoInsteadImage( $content, $post_id ) {
			$field_value = esc_attr( get_post_meta( $post_id, 'show_video', true ) );
			$videourl = get_post_meta($post_id, 'videourl', true);
			$field_label = "<p><label for=\"show_video\"><input type=\"checkbox\" name=\"show_video\" id=\"show_video\" value=\"$field_value\" " . checked( $field_value, 1, false) . "> Mostrar Video</label></p>";
			$field_label .= "<p><label for=\"videourl\">Video URL</label><input type='text' name='videourl' value=\"$videourl\"></p>";

			return $content .= $field_label;
		}

		public function addSubtitleField($post) {
			$subtitle = get_post_meta($post->ID, 'subtitle', true);
			echo "<p><input type='text' name='subtitle' value=\"$subtitle\" class='widefat'></p>";
		}

		public function videosEditorSetUp($post){
			$videos = get_post_meta( $post->ID, 'video-details', true );
			$each = !empty($videos) ? count($videos) : 0;
			$tags = get_terms( array( 'post_types' => 'videos', 'taxonomy' => 'video_tags', 'hide_empty' => false ) );?>
            <div class="clone-field" data-clone="<?php echo $each; ?>">
                <table id="list-sort" class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <td class="check-column num"></td>
                            <td>Video URL</td>
                            <td>Mostrar en</td>
                            <td>Filtros</td>
                            <td class="check-column"></td>
                        </tr>
                    </thead>
                    <tbody>
					<?php if(!empty($videos)) {
						foreach ( $videos as $key => $videos ) {
							$result = array();
							?>
                            <tr <?php if($key == 0 ) echo 'class="img-clone"'?>>
                                <td class="index" >
									<?php echo $key + 1; ?>
                                </td>
                                <td>
                                        <input id="img_upl_button_<?php echo $key;?>" data-current="<?php echo $key; ?>" type="button" value="Upload Image"/>
                                        <input id="image_URL_<?php echo $key; ?>" name="video[<?php echo $key; ?>][url]" type="text"
                                               value="<?php echo $videos['url']; ?>" style="width:400px;"/>
                                </td>
                                <td>
                                        Destacado <input id="img_dest_<?php echo $key; ?>" type="checkbox" name="video[<?php echo $key; ?>][dest]" value="on" <?php if(isset($videos['dest'])) checked("on", $videos['dest']); ?>><br/>
                                        Durante <input id="img_dest_<?php echo $key; ?>" type="checkbox" name="video[<?php echo $key; ?>][streaming]" value="on" <?php if(isset($videos['streaming'])) checked("on", $videos['streaming']); ?>><br/>
                                        Despues <input id="img_dest_<?php echo $key; ?>" type="checkbox" name="video[<?php echo $key; ?>][after]" value="on" <?php if(isset($videos['after'])) checked("on", $videos['after']); ?>><br/>
                                </td>
                                <td>
                                    <?php if($tags) { ?>
                                        Tags
                                        <select multiple class="tagselector" size="6" id="img_tags_<?php echo $key; ?>" type="text" name="video[<?php echo $key; ?>][tags][]">
											<?php foreach ($tags as $i => $tag) { ?>
                                                <option value="<?php echo $tag->term_id; ?>" <?php echo (isset($videos['tags']) && in_array($tag->term_id, $videos['tags'])) ? "selected" : ""; ?>><?php echo $tag->name; ?></option>
											<?php } ?>
                                        </select><br/>
                                        <sup>Usa Ctrl para seleccionar más de uno.</sup>
                                    <?php } else { ?>
                                          No hay Tags Creados
                                    <?php } ?>
                                </td>
                                <td>
                                    <span class="dashicons dashicons-dismiss delete-this" data-this="<?php echo $key; ?>"></span>
                                </td>
                            </tr>
							<?php
						}
					} ?>
                    <tr <?php if(!isset($key)) echo 'class="img-clone"'?>>
                        <td class="index">
							<?php echo $each + 1; ?>
                        </td>
                        <td>
                                <input id="my_image_URL_<?php echo $each; ?>" name="video[<?php echo $each; ?>][url]" type="text" placeholder="Url del Video"/>
                        </td>
                        <td>
                                Destacado <input id="img_dest_<?php echo $each; ?>" type="checkbox" name="video[<?php echo $each; ?>][dest]" value="on"><br/>
                                Durante <input id="img_dest_<?php echo $each; ?>" type="checkbox" name="video[<?php echo $each; ?>][streaming]" value="on"><br/>
                                Despues <input id="img_dest_<?php echo $each; ?>" type="checkbox" name="video[<?php echo $each; ?>][after]" value="on"><br/>
                        </td>
                        <td>
			                <?php if($tags) { ?>
                                Tags
                                <select multiple class="tagselector" size="6" id="img_tags_<?php echo $each; ?>" type="text" name="video[<?php echo $each; ?>][tags][]">
									<?php foreach ($tags as $i => $tag) { ?>
                                        <option value="<?php echo $tag->term_id; ?>"><?php echo $tag->name; ?></option>
									<?php } ?>
                                </select><br/>
                                <sup>Usa Ctrl para seleccionar más de uno.</sup>
			                <?php } else { ?>
                                 No hay Tags Creados
			                <?php } ?>
                        </td>
                        <td>
                            <span class="dashicons dashicons-dismiss delete-this" data-this="<?php echo $each; ?>"></span>
                        </td>
                        <script>
                            jQuery(document).ready(function ($) {
                                jQuery('#img_upl_button_<?php echo $each;?>').click(function () {
                                    window.send_to_editor = function (html) {
                                        imgurl = jQuery(html).attr('src')
                                        jQuery('#my_image_URL_<?php echo $each;?>').val(imgurl);
                                        jQuery('#picsrc_<?php echo $each;?>').attr("src", imgurl);
                                        tb_remove();
                                    }

                                    formfield = jQuery('#my_image_URL_<?php echo $each;?>').attr('name');
                                    tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
                                    return false;
                                });
                            });
                        </script>
                    </tr>
                    </tbody>
                </table>
            </div>
            <span class="dashicons dashicons-plus-alt"></span><?php

		}

		public function saveMetaBox($post_id) {


			if(isset($_POST['video'])) {
				$video = $_POST['video'];
				$video = array_values($video);
				foreach ($video as $key => $vid) {
					if(empty($vid['url']))
						unset($video[$key]);
				}
				update_post_meta( $post_id, 'video-details', $video);
			}
		}

	}

	new ivsMetaboxes();
}
