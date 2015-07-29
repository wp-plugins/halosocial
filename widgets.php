<?php
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category widget
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */
add_action( 'widgets_init', function(){
	register_widget( 'HALO_Module_Widget' );
});
/**
* Adds HALO_Module_Widget widget.
*/
class HALO_Module_Widget extends WP_Widget {
	/**
	* Register widget with WordPress.
	*/
	function __construct() {
		parent::__construct(
			'HALO_Module_Widget', // Base ID
			__('HaloSocial Widgets', 'text_domain'), // Name
			array( 'description' => __( 'HaloSocial widgets!', 'text_domain' ), ) // Args
		);
	}
	
	/**
	* Front-end display of widget.
	*
	* @see WP_Widget::widget()
	*
	* @param array $args Widget arguments.
	* @param array $instance Saved values from database.
	*/
	public function widget( $args, $instance ) {
		//boot the halo lib
		haloBoot();
		if(HALOPluginModel::isActive('HALOWidgets', 'halo')) {
			//load default css
            $this->loadStyles();
            //load modernize js
            $this->loadScripts();

			echo $args['before_widget'];
			$title = '';
			if ( ! empty( $instance['title'] ) ) {
				$title = $instance['title'];
			}

			if ( ! empty( $instance['widget'] ) ) {
				$widget = $instance['widget'];
				echo HALOUIBuilder::getInstance('', 'div_wrapper', array(
                    'html' => HALOUIBuilder::getInstance('', $widget, array('title' => $title))->fetch(),
                    'class' => 'halo-singe-wrap halo-widget-wrapper'
                ))->fetch();
			}
			
				//echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
			echo $args['after_widget'];
		}
	}
	
	/**
	* Back-end widget form.
	*
	* @see WP_Widget::form()
	*
	* @param array $instance Previously saved values from database.
	*/
	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] )?$instance[ 'title' ]:'';
		$widget = isset( $instance[ 'widget' ] )?$instance[ 'widget' ]:'widgets.sitemembers';
		$widgetList = array();
		$widgetList[] = array('title' => 'HaloSocial Site Members', 	'value' => 'widgets.sitemembers');
		$widgetList[] = array('title' => 'HaloSocial Events', 'value' => 'widgets.upcomingevents');
		$widgetList[] = array('title' => 'HaloSocial Featured Pages', 	'value' => 'widgets.featuredpages');
		$widgetList[] = array('title' => 'HaloSocial Popular Groups', 	'value' => 'widgets.populargroups');
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'widget' ); ?>"><?php _e( 'Widget:' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'widget' ); ?>" name="<?php echo $this->get_field_name( 'widget' ); ?>" value="<?php echo esc_attr( $widget ); ?>">
			<?php foreach($widgetList as $opt) { ?>
			<option value="<?php echo $opt['value'] ?>" <?php if($widget == $opt['value']) echo 'selected="selected"' ?>><?php echo $opt['title'] ?></option>
			<?php } ?>
		</select>
		</p>
		<?php
	}
	
	/**
	* Sanitize widget form values as they are saved.
	*
	* @see WP_Widget::update()
	*
	* @param array $new_instance Values just sent to be saved.
	* @param array $old_instance Previously saved values from database.
	*
	* @return array Updated safe values to be saved.
	*/
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['widget'] = ( ! empty( $new_instance['widget'] ) ) ? strip_tags( $new_instance['widget'] ) : '';
		return $instance;
	}

    /**
     * Load styles
     * @param  string $layout
     */
    public function loadStyles($layout = 'default') {
        $minify = HALOConfig::get('global.cssMinify')?'.min':'';
        //bootstrap css
        // HALOAssetHelper::addCss('assets/css/bootstrap'.$minify.'.css');
        //fontawesome css
        HALOAssetHelper::addCss('assets/css/fontawesome'.$minify.'.css');
        //halo css
        HALOAssetHelper::addCss('assets/css/halosingle'.$minify.'.css');
        //magnific_popup css
        HALOAssetHelper::addCss('assets/css/magnific_popup'.$minify.'.css');
        HALOAssetHelper::addCss('assets/css/halowidgets'.$minify.'.css');
        Event::fire('system.loadDefaultCss', array($layout));
		
		HALOAssetHelper::enqueueStyle();
    }

    /**
     * Load scripts
     */
    public function loadScripts() {
        HALOAssetHelper::printInlineScript();
        HALOAssetHelper::addScript('assets/js/modernizr.js');
        HALOAssetHelper::loadDefaultScript();
    }
}