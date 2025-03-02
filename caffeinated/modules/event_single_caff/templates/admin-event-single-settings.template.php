<?php

use EventEspresso\core\services\request\sanitizers\AllowedTags;

add_filter('FHEE__EEH_Form_Fields__label_html', '__return_empty_string');
$values = EEH_Form_Fields::prep_answer_options(
    array(
        array('id' => 1, 'text' => esc_html__('Yes', 'event_espresso')),
        array('id' => 0, 'text' => esc_html__('No', 'event_espresso')),
    )
);
?>

<!--*************************   Event Single  ****************************-->

<h2 class="ee-admin-settings-hdr">
    <?php esc_html_e(
        'Single Event Pages',
        'event_espresso'
    ); ?><?php // echo wp_kses(EEH_Template::get_help_tab_link('event_single_settings_info'),AllowedTags::getAllowedTags()); ?>
</h2>
<table class="form-table">
    <tbody>
    <tr>
        <th>
            <label for="display_status_banner_single">
                <?php esc_html_e('Display Status Banner', 'event_espresso'); ?>
            </label>
        </th>
        <td>
            <?php echo wp_kses(
                EEH_Form_Fields::select(
                    'display_status_banner_single',
                    $display_status_banner_single,
                    $values,
                    'display_status_banner_single',
                    'display_status_banner_single'
                ),
                AllowedTags::getWithFormTags()
            ); ?>
            <p class="description"><?php
                esc_html_e(
                    'Selecting "Yes" will inject an Event Status banner with the title whenever Events are displaying on the single event page.',
                    'event_espresso'
                ); ?></p>
        </td>
    </tr>

    <tr>
        <th>
            <label for="display_venue">
                <?php esc_html_e('Display Venue Details', 'event_espresso'); ?>
                <?php echo wp_kses(EEH_Template::get_help_tab_link('display_addresses_in_reg_form_info'), AllowedTags::getAllowedTags()); ?>
            </label>
        </th>
        <td>
            <?php echo wp_kses(
                EEH_Form_Fields::select(
                    'display_venue',
                    $display_venue,
                    $values,
                    'display_venue',
                    'display_venue'
                ),
                AllowedTags::getWithFormTags()
            ); ?>
            <p class="description"><?php
                esc_html_e(
                    'Do not use this if you are using the venue shortcodes in your event description.',
                    'event_espresso'
                ); ?></p>
        </td>
    </tr>

    <tr>
        <th>
            <label for="EED_Events_Single_use_sortable_display_order">
                <?php esc_html_e('Use Custom Display Order?', 'event_espresso'); ?>
            </label>
        </th>
        <td>
            <?php
            echo wp_kses(
                EEH_Form_Fields::select(
                    'use_sortable_display_order',
                    $use_sortable_display_order,
                    $values,
                    'EED_Events_Single_use_sortable_display_order',
                    'EED_Events_Single_use_sortable_display_order'
                ),
                AllowedTags::getWithFormTags()
            );
            ?>
        </td>
    </tr>

    <tr>
        <th>
            <?php esc_html_e('Display Order', 'event_espresso'); ?>
            <?php echo wp_kses(EEH_Template::get_help_tab_link('display_addresses_in_reg_form_info'), AllowedTags::getAllowedTags()); ?>
        </th>
        <td>

            <?php wp_nonce_field(
                'espresso_update_event_single_order',
                'espresso_update_event_single_order_nonce',
                false
            ); ?>
            <?php echo wp_kses($event_single_display_order, AllowedTags::getWithFormTags()); ?>

            <p class="description"><?php
                esc_html_e(
                    'Drag and Drop the above to determine the display order of the Event Description, Date and Times, Ticket Selector, and Venue Information on the single event page.',
                    'event_espresso'
                ); ?></p>

        </td>
    </tr>

    </tbody>
</table>

