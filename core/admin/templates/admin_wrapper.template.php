<div class="wrap espresso-admin">

    <h1><?php esc_attr_e('Event Espresso', 'event_espresso'); ?>&nbsp;-&nbsp;<?php echo $admin_page_title; ?></h1>

    <div id="ajax-notices-container"></div>

    <?php echo $nav_tabs; ?>

    <?php
    do_action('AHEE__admin_wrapper__template__before_admin_page_content');
    echo $before_admin_page_content;
    echo $admin_page_content;
    echo $after_admin_page_content;
    do_action('AHEE__admin_wrapper__template__after_admin_page_content');
    ?>

</div>
