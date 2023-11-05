<h1 <?php echo $class->get_render_attribute_string('title'); ?>><?php echo $class->get_settings_for_display('title') ?></h1>
<img src="<?php echo esc_url($class->get_settings_for_display('image')["url"]) ?>" alt="<?php echo $class->get_settings_for_display('title') ?>">
<?php

print_r(json_encode($settings,JSON_PRETTY_PRINT));


?>