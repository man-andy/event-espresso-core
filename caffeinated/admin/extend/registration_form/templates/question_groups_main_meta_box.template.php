<?php

/**
 * Template: includes/core/admin/registration_form/templates/question_groups_main_meta_box.template.php
 * For displaying a question group for editing/creating within the EE Admin page
 */

use EventEspresso\core\services\request\sanitizers\AllowedTags;

/**
 * @var int               $QSG_ID         the main question group's ID
 * @var EE_Question_Group $question_group the main question group we're displaying
 * @var EE_Question[]     $all_questions
 * @var array             $values         Array of arrays, where each sub-array contains 2 keys:
 *                                        'id' (internal value)
 *                                        'name' (label for displaying)
 */

$QSG_system = $question_group->system_group();

$disabled = ! empty($QSG_system) ? 'disabled' : '';
$id = ! empty($QST_system) ? '_disabled' : '';
?>

<div id="group-details" class="edit-group padding">
    <table class="form-table">
        <tbody>

        <tr>
            <th>
                <label for="QSG_name">
                    <?php esc_html_e('Group Name', 'event_espresso'); ?>
                    <?php echo wp_kses(EEH_Template::get_help_tab_link('group_name_info'), AllowedTags::getAllowedTags()); ?>
                </label>
            </th>
            <td>
                <input id="QSG_name" name="QSG_name" value="<?php echo esc_attr($question_group->get_f('QSG_name')); ?>" type="text"
                       class="regular-text"><br/>

            </td>
        </tr>

        <tr>
            <th>
                <label for="QSG_identifier">
                    <?php esc_html_e('Group Identifier', 'event_espresso'); ?>
                    <?php echo wp_kses(EEH_Template::get_help_tab_link('group_identifier_info'), AllowedTags::getAllowedTags()); ?>
                </label>
            </th>
            <td>
                <input id="QSG_identifier" name="QSG_identifier<?php echo esc_attr($id); ?>"
                       value="<?php echo esc_attr($question_group->get_f('QSG_identifier')); ?>" type="text"
                       class="regular-text" <?php echo esc_attr($disabled); ?>>
                <?php if (! empty($QSG_system)) { ?>
                    <p><span class="description" style="color:#D54E21;">
                            <?php esc_html_e('System question group! This field cannot be changed.', 'event_espresso') ?>
                    </span><br/></p>
                <?php } ?>

            </td>
        </tr>
        <tr>
            <th>
                <label for="QSG_desc">
                    <?php esc_html_e('Description', 'event_espresso'); ?>
                    <?php echo wp_kses(EEH_Template::get_help_tab_link('group_description_info'), AllowedTags::getAllowedTags()); ?>
                </label>
            </th>
            <td>
                <textarea id="QSG_desc" name="QSG_desc" class="regular-text" rows="2"
                          cols="40"><?php echo esc_textarea($question_group->get_f('QSG_desc')); ?></textarea>
            </td>
        </tr>

        <tr>
            <th>
                <label for="QSG_order">
                    <?php esc_html_e('Question Group Order', 'event_espresso'); ?>
                    <?php echo wp_kses(EEH_Template::get_help_tab_link('group_order_info'), AllowedTags::getAllowedTags()); ?>
                </label>
            </th>
            <td>
                <input id="QSG_order" name="QSG_order" class="regular-text"
                       value="<?php echo esc_attr($question_group->order()); ?>"/>
            </td>
        </tr>

        <tr>
            <th>
                <label>
                    <?php esc_html_e('Show Name', 'event_espresso'); ?>
                    <?php echo wp_kses(EEH_Template::get_help_tab_link('show_group_name_info'), AllowedTags::getAllowedTags()); ?>
                </label>
            </th>
            <td>
                <label for="QSG_show_group_name">
                    <?php echo wp_kses(
                        EEH_Form_Fields::select_input(
                            'QSG_show_group_name',
                            $values,
                            $question_group->show_group_name()
                        ),
                        AllowedTags::getWithFormTags()
                    ); ?>
                    <p class="description"><?php esc_html_e('Show Group Name on Registration Page?', 'event_espresso'); ?></p>
                </label>
            </td>
        </tr>

        <tr>
            <th>
                <label>
                    <?php esc_html_e(' Show Description', 'event_espresso'); ?>
                    <?php echo wp_kses(EEH_Template::get_help_tab_link('show_group_description_info'), AllowedTags::getAllowedTags()); ?>
                </label>
            </th>
            <td>
                <label for="QSG_show_group_order">
                    <?php echo wp_kses(
                        EEH_Form_Fields::select_input(
                            'QSG_show_group_desc',
                            $values,
                            $question_group->show_group_desc()
                        ),
                        AllowedTags::getWithFormTags()
                    ); ?>
                    <p class="description"><?php
                        esc_html_e(' Show Group Description on Registration Page?', 'event_espresso');
                    ?></p>
                </label>
                <input type="hidden" name="QSG_system" value="<?php echo esc_attr($question_group->system_group()); ?>">
            </td>
        </tr>

        </tbody>
    </table>
</div>

<div id="group-questions" class="edit-group padding question-group-questions-container postbox">
    <div class="handlediv" title="<?php esc_attr_e('Click to toggle', 'event_espresso'); ?>"><br></div>
    <h2 class="handle"><?php esc_html_e('Questions', 'event_espresso'); ?></h2>
    <div class="form-table question-group-questions inside">
        <div class="padding">
            <p><span class="description"><?php
                    esc_html_e(
                        'Select which questions should be shown in this group by checking or unchecking boxes. You can drag and drop questions to reorder them. Your changes will be updated when you save.',
                        'event_espresso'
                    ); ?></span></p>
            <div>
                <ul class="question-list-sortable">
                    <?php
                    $question_order = 0;
                    $question_group_questions = $question_group->questions();
                    foreach ($all_questions as $question_ID => $question) {
                        if ($question instanceof EE_Question) {
                            /*@var $question EE_Question*/
                            $checked = isset($question_group_questions[ $question_ID ]) ? 'checked' : '';
                            // disable questions from the personal information question group
                            // is it required in the current question group? if so don't allow admins to remove it
                            $disabled = in_array(
                                $question->system_ID(),
                                EEM_Question::instance()->required_system_questions_in_system_question_group(
                                    $QSG_system
                                )
                            ) ? 'disabled' : '';
                            // limit where system questions can appear
                            if (
                                $question->system_ID() &&
                                ! in_array(
                                    $question->system_ID(),
                                    EEM_Question::instance()->allowed_system_questions_in_system_question_group(
                                        $QSG_system
                                    )
                                )
                            ) {
                                continue; // skip over system question not assigned to this group except for the address system group cause we want the address questions to display even if they aren't selected (but still not show the personal system questions).  The third condition checks if we're displaying a non system question group and the question is a system question, then we skip because for non-system question groups we only want to show non-system questions.
                            }
                            ?>
                            <li class="ee-question-sortable">
                                <label for="question-<?php echo absint($question_ID); ?>">
                                    <input type="checkbox" name="questions[<?php echo absint($question_ID); ?>]"
                                           id="question-<?php echo absint($question_ID); ?>"
                                           value="<?php echo absint($question_ID); ?>" <?php echo esc_attr($disabled); ?> <?php echo esc_attr($checked); ?>>
                                    <span class="question-text"><?php
                                        echo wp_kses(trim($question->display_text()), AllowedTags::getAllowedTags())
                                             . (95 <= strlen(trim($question->display_text()))
                                                ? "&hellip;"
                                                : '');
                                                                ?>
                                    </span>
                                    <input class="question-group-QGQ_order" type="hidden"
                                           name="question_orders[<?php echo absint($question_ID); ?>]"
                                           value="<?php echo esc_attr($question_order); ?>">
                                </label>
                                <?php
                                if (
                                    EE_Registry::instance()->CAP->current_user_can(
                                        'ee_edit_question',
                                        'espresso_registration_form_edit_question',
                                        $question->ID()
                                    )
                                ) {
                                    $edit_query_args = array(
                                        'action' => 'edit_question',
                                        'QST_ID' => $question->ID(),
                                    );
                                    $edit_link = EE_Admin_Page::add_query_args_and_nonce($edit_query_args, EE_FORMS_ADMIN_URL);

                                    echo '<a href="' . $edit_link . '" target="_blank" aria-label="' .
                                        sprintf(
                                            esc_attr__('Edit %s', 'event_espresso'),
                                            $question->admin_label()
                                        )
                                        . '"><span class="dashicons dashicons-edit"></span>
                                        </a>';
                                }
                                ?>
                            </li>
                            <?php
                            $question_order++;
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
