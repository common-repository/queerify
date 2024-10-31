<?php
// Main class definition file 

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Queerify')) :

class Queerify {

    private $sections_array = array();
    private $fields_array = array();

    public function __construct() {
        // Enqueue the admin resources
        add_action('admin_enqueue_scripts', array($this, 'admin_resources'));
        // Enqueue the front-end facing resources
        add_action('wp_enqueue_scripts', array($this, 'public_resources'));
        // Add the plugin settings 
        add_action('admin_init', array($this, 'admin_init'));
        // Create the plugin menu
        add_action('admin_menu', array($this, 'admin_menu'));

    }
    
    // Set option defaults
    public function set_defaults($optionGroup, $key) {
        $options = get_option($optionGroup);
        $defaults = array(
            'minification' => 'off',
            'flags' => 'gay',
        );
        $options = wp_parse_args($options, $defaults);

        if(isset($options[$key]))
            return $options[$key];

        return $defaults;
    }
    // Set reused variables
    public function set_reusables() {
        $minification = $this->set_defaults('queerify_basic', 'minification');
        $choosenFlag = $this->set_defaults('queerify_basic', 'flags');
        $relativeImgPath = plugin_dir_url( __FILE__ ) . 'resources/img/';
        // used print_r(get_current_screen()); under ID = $hook
        $hook = 'settings_page_queerify_settings';

        $reusables = array(
            'minification' => $minification,
            'flag' => $choosenFlag,
            'path' => $relativeImgPath,
            'hook' => $hook
        );

        if (isset($reusables)) {
            return $reusables;
        }
        return $defaults;
    }

    // Plugin resources - admin
    public function admin_resources($hook) {
        $reusables = $this->set_reusables();
        if ($reusables['hook'] != $hook) {
            return;
        }

        if($reusables['minification'] == 'on') { // if minification is checked
            wp_enqueue_style('admin-style-min', plugin_dir_url( __FILE__ ) . 'resources/css/admin-style.css', array());
            wp_enqueue_script('admin-script-min', plugin_dir_url( __FILE__ ) . 'resources/js/admin-script.js', array('jquery'), true);
            wp_localize_script('admin-script-min', 'phpData', array(
                'flag' => $reusables['flag'],
                'imgPath' => $reusables['path'])
                              );
        } else { // not minified
            wp_enqueue_style('admin-style', plugin_dir_url( __FILE__ ) . 'resources/css/admin-style.css', array());
            wp_enqueue_script('admin-script', plugin_dir_url( __FILE__ ) . 'resources/js/admin-script.js', array('jquery'), true);
            wp_localize_script('admin-script', 'phpData', array(
                'flag' => $reusables['flag'],
                'imgPath' => $reusables['path'])
                              );
        }
    }
    // Front-end resources
    public function public_resources() {
        $reusables = $this->set_reusables();

        if($reusables['minification'] == 'on') { // if minification is checked
            wp_enqueue_style('public-style-min', plugin_dir_url( __FILE__ ) . 'resources/css/public-style-min.css', array());
            wp_enqueue_script('public-script-min', plugin_dir_url( __FILE__ ) . 'resources/js/public-script-min.js', array('jquery'), true);
            wp_localize_script('public-script-min', 'phpData', array(
                'flag' => $reusables['flag'],
                'imgPath' => $reusables['path'])
                              );
        } else { // not minified
            wp_enqueue_style('public-style', plugin_dir_url( __FILE__ ) . 'resources/css/public-style.css', array());
            wp_enqueue_script('public-script', plugin_dir_url( __FILE__ ) . 'resources/js/public-script.js', array('jquery'), true);
            wp_localize_script('public-script', 'phpData', array(
                'flag' => $reusables['flag'],
                'imgPath' => $reusables['path'])
                              );
        }
    }

    // Set sections
    public function set_sections($sections) {
        // Bail if not array.
        if (!is_array($sections)) {
            return false;
        }
        // Assign to the sections array.
        $this->sections_array = $sections;
        return $this;
    }
    // Add a single section
    public function add_section($section) {
        // Bail if not array.
        if (!is_array($section)) {
            return false;
        }
        // Assign the section to sections array.
        $this->sections_array[] = $section;
        return $this;
    }

    // Set fields
    public function set_fields($fields) {
        // Bail if not array.
        if (!is_array($fields)) {
            return false;
        }
        // Assign the fields.
        $this->fields_array = $fields;
        return $this;
    }
    // Add a single field
    public function add_field($section, $field_array) {
        // Set the defaults
        $defaults = array(
            'id'   => '',
            'name' => '',
            'desc' => '',
            'type' => 'text',
        );
        // Combine the defaults with user's arguements.
        $arg = wp_parse_args($field_array, $defaults);
        // Each field is an array named against its section.
        $this->fields_array[$section][] = $arg;
        return $this;
    }

    // Plugin settings
    function admin_init() {
        // Register the sections
        foreach ($this->sections_array as $section) {
            if (false == get_option($section['id'])) {
                // Add a new field as section ID.
                add_option( $section['id'] );
            }
            // Deals with sections description.
            if (isset( $section['desc']) && !empty( $section['desc'])) {
                // Build HTML
                $section['desc'] = '<div class="decription">' . $section['desc'] . '</div>';
                // Create the callback for description.
                $callback = function() use ($section) {
                    echo str_replace('"', '\"', $section['desc']);
                };
            } elseif (isset($section['callback'])) {
                $callback = $section['callback'];
            } else {
                $callback = null;
            }

            add_settings_section($section['id'], $section['title'], $callback, $section['id']);
        }
        // Register settings fields
        foreach ($this->fields_array as $section => $field_array) {
            foreach ($field_array as $field) {
                // ID
                $id = isset($field['id']) ? $field['id'] : false;
                // Type
                $type = isset($field['type']) ? $field['type'] : 'text';
                // Name
                $name = isset($field['name']) ? $field['name'] : 'No Name Added';
                // Label
                $label_for = "{$section}[{$field['id']}]";
                // Description
                $description = isset($field['desc']) ? $field['desc'] : '';
                // Size
                $size = isset($field['size']) ? $field['size'] : null;
                // Options
                $options = isset($field['options']) ? $field['options'] : '';
                // Standard default value
                $default = isset($field['default']) ? $field['default'] : '';
                // Standard default placeholder
                $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';

                // Sanitize Callback
                $sanitize_callback = isset($field['sanitize_callback']) ? $field['sanitize_callback'] : '';

                $args = array(
                    'id'                => $id,
                    'type'              => $type,
                    'name'              => $name,
                    'label_for'         => $label_for,
                    'desc'              => $description,
                    'section'           => $section,
                    'size'              => $size,
                    'options'           => $options,
                    'std'               => $default,
                    'placeholder'       => $placeholder,
                    'sanitize_callback' => $sanitize_callback,
                );

                // Add a new field to a section of a settings page
                $field_id = $section . '[' . $field['id'] . ']';

                add_settings_field(
                    $field_id,
                    $name,
                    array($this, 'callback_' . $type),
                    $section,
                    $section,
                    $args
                );
            }
        }

        // Creates our settings in the fields table
        foreach ($this->sections_array as $section) {
            // Registers a setting and its sanitization callback
            register_setting($section['id'], $section['id'], array($this, 'sanitize_fields'));
        } 

    }

    // Sanitize callback for Settings API fields
    public function sanitize_fields($fields) {
        foreach ($fields as $field_slug => $field_value) {
            $sanitize_callback = $this->get_sanitize_callback($field_slug);
            // If callback is set, call it
            if ($sanitize_callback) {
                $fields[$field_slug] = call_user_func($sanitize_callback, $field_value);
                continue;
            }
        }
        return $fields;
    }

    // Get sanitization callback for given option slug
    function get_sanitize_callback($slug = '') {
        if (empty($slug)) {
            return false;
        }

        // Iterate over registered fields and see if we can find proper callback
        foreach ($this->fields_array as $section => $field_array) {
            foreach ($field_array as $field) {
                if ($field['name'] != $slug) {
                    continue;
                }
                // Return the callback name.
                return isset($field['sanitize_callback']) && is_callable($field['sanitize_callback']) ? $field['sanitize_callback'] : false;
            }
        }
        return false;
    }

    // Get field description for display
    public function get_field_description($args) {
        if (!empty($args['desc'])) {
            $desc = sprintf('<p class="description">%s</p>', $args['desc']);
        } else {
            $desc = '';
        }
        return $desc;
    }

    // Displays a title field for a settings field
    function callback_title($args) {
        $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
        if ('' !== $args['name']) {
            $name = $args['name'];
        } else {
        };
        $type = isset( $args['type'] ) ? $args['type'] : 'title';
        $html = '';
        echo $html;
    }

    // Displays a checkbox for a settings field
    function callback_checkbox($args) {

        $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));

        $html  = '<fieldset>';
        $html .= sprintf('<label for="%1$s[%2$s]">', $args['section'], $args['id']);
        $html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id']);
        $html .= sprintf('<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked($value, 'on', false ));
        $html .= sprintf('%1$s</label>', $args['desc']);
        $html .= '</fieldset>';

        echo $html;
    }

    // Displays a selectbox for a settings field
    function callback_select($args) {

        $value = esc_attr($this->get_option($args['id'], $args['section'], $args['std']));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

        $html = sprintf('<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id']);
        foreach ($args['options'] as $key => $label) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected($value, $key, false ), $label);
        }
        $html .= sprintf( '</select>' );
        $html .= $this->get_field_description( $args );

        echo $html;
    }

    // Get the value of a settings field
    function get_option($option, $section, $default = '') {

        $options = get_option($section);

        if (isset($options[$option])) {
            return $options[$option];
        }
        return $default;
    }

    // Add submenu page to the Settings main menu
    public function admin_menu() {
        // add_options_page($page_title, $menu_title, $capability, $menu_slug, array($this, $callable));
        add_options_page(
            'Queerify',
            'Queerify',
            'activate_plugins',
            'queerify_settings',
            array($this, 'plugin_page')
        );
    }
    // Plugin page wrap
    public function plugin_page() {
        $pluginVersion = QUEERIFY_VERSION;
        $noSrcipt = esc_html__('IMPORTANT: Javascript must be turned ON in your browser settings in order for this plugin to work!', 'queerify');
        $ratingH3 = esc_html__('Ratings & Reviews', 'queerify');
        $ratingStrong = esc_html__('If you like this plugin, please consider leaving a', 'queerify');
        $rating = esc_html__('rating', 'queerify');
        $thanks = esc_html__('A huge thanks in advance!', 'queerify');
        $ratingButton = esc_html__('Leave us a rating', 'queerify');
        $metaH3 = esc_html__('About the plugin', 'queerify');
        $metaStrong = esc_html__('Developed by:', 'queerify');
        $contactSupport = esc_html__('Need some support?', 'queerify');
        $contactForum = esc_html__('Contact the developers via the Support Forum', 'queerify');
        $contactButton = esc_html__('Contact us', 'queerify');
        echo "<div class='queerify-wrap'>
            <div class='title'>
                <h1>Queerify <span style='font-size:50%;'>v {$pluginVersion}</span></h1>
                <a class='logo' href='https://www.imoptimal.com/' target='_blank'></a>
            </div>
            <noscript>{$noSrcipt}</noscript>";
        $this->show_navigation();
        $this->show_forms();
        echo "<div class='rating'>
                <h3>{$ratingH3}</h3>
                <p>
                    <strong>{$ratingStrong} ★★★★★ {$rating}</strong><br>
                    <strong>{$thanks}</strong>
                </p>
                <a href='https://wordpress.org/support/plugin/queerify/reviews/' target='_blank' class='button button-primary'>{$ratingButton}</a>
            </div>
            <div class='meta-info'>
                <h3>{$metaH3}</h3>
                <strong>{$metaStrong}</strong> <a href='https://www.imoptimal.com/' target='_blank'>Imoptimal</a>
                <div class='contact-info'>
                    <strong>{$contactSupport}</strong> <br>
                    <strong>{$contactForum}</strong>
                    <div>
                        <a href='https://wordpress.org/support/plugin/queerify/' target='_blank' class='button button-primary'>{$contactButton}</a>
                    </div>
                </div>
            </div>
        </div>";
    }
    // Show navigations as tab
    function show_navigation() {
        $html = '<h2 class="nav-tab-wrapper">';

        foreach ($this->sections_array as $tab) {
            $html .= sprintf('<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title']);
        }
        $html .= '</h2>';
        echo $html;
    }
    // Show the section settings forms
    // This function displays every sections in a different form
    function show_forms() { ?>
<div class="metabox-holder">
    <?php foreach ($this->sections_array as $form) { ?>
    <!-- style="display: none;" -->
    <div id="<?php echo $form['id']; ?>" class="group" >
        <form method="post" action="options.php">
            <?php
                                                    do_action('queerify_form_top_' . $form['id'], $form);
                                                    settings_fields($form['id']);
                                                    do_settings_sections($form['id']);
                                                    do_action('queerify_form_bottom_' . $form['id'], $form);
            ?>
            <div style="padding-left: 10px">
                <?php submit_button(null, 'primary', 'submit_'.$form['id']); ?>
            </div>
        </form>
    </div>
    <?php } ?>
</div>
<?php }

}
endif;
?>