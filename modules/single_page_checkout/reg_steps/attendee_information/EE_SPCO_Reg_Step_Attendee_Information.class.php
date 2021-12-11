<?php

use EventEspresso\core\domain\services\registration\form\RegFormFactory;
use EventEspresso\core\domain\services\registration\form\base\CountryOptions;
use EventEspresso\core\domain\services\registration\form\base\StateOptions;
use EventEspresso\core\domain\services\registration\form\v1\RegFormQuestionFactory;
use EventEspresso\core\exceptions\EntityNotFoundException;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\form\legacy\FormSectionProperInterface;
use EventEspresso\core\services\loaders\LoaderFactory;

/**
 * Class EE_SPCO_Reg_Step_Attendee_Information
 *
 * @package     Event Espresso
 * @subpackage  core
 * @author      Brent Christensen
 * @since       4.5.0
 */
class EE_SPCO_Reg_Step_Attendee_Information extends EE_SPCO_Reg_Step
{
    /**
     * @var RegFormFactory
     */
    protected $reg_form_factory;

    /**
     * @var string
     */
    protected $reg_form_factory_version = RegFormFactory::VERSION_2;


    /**
     *    class constructor
     *
     * @access    public
     * @param EE_Checkout $checkout
     */
    public function __construct(EE_Checkout $checkout)
    {
        $this->_slug    = 'attendee_information';
        $this->_name    = esc_html__('Attendee Information', 'event_espresso');
        $this->checkout = $checkout;
        $this->_reset_success_message();
        $this->set_instructions(
            esc_html__('Please answer the following registration questions before proceeding.', 'event_espresso')
        );
        $this->reg_form_factory = new RegFormFactory($this->reg_form_factory_version);
    }


    public function translate_js_strings()
    {
        EE_Registry::$i18n_js_strings['required_field']            = esc_html__(
            ' is a required question.',
            'event_espresso'
        );
        EE_Registry::$i18n_js_strings['required_multi_field']      = esc_html__(
            ' is a required question. Please enter a value for at least one of the options.',
            'event_espresso'
        );
        EE_Registry::$i18n_js_strings['answer_required_questions'] = esc_html__(
            'Please answer all required questions correctly before proceeding.',
            'event_espresso'
        );
        EE_Registry::$i18n_js_strings['attendee_info_copied']      = sprintf(
            esc_html_x(
                'The attendee information was successfully copied.%sPlease ensure the rest of the registration form is completed before proceeding.',
                'The attendee information was successfully copied.(line break)Please ensure the rest of the registration form is completed before proceeding.',
                'event_espresso'
            ),
            '<br/>'
        );
        EE_Registry::$i18n_js_strings['attendee_info_copy_error']  = esc_html__(
            'An unknown error occurred on the server while attempting to copy the attendee information. Please refresh the page and try again.',
            'event_espresso'
        );
        EE_Registry::$i18n_js_strings['enter_valid_email']         = esc_html__(
            'You must enter a valid email address.',
            'event_espresso'
        );
        EE_Registry::$i18n_js_strings['valid_email_and_questions'] = esc_html__(
            'You must enter a valid email address and answer all other required questions before you can proceed.',
            'event_espresso'
        );
    }


    public function enqueue_styles_and_scripts()
    {
    }


    /**
     * @return boolean
     */
    public function initialize_reg_step(): bool
    {
        return true;
    }


    /**
     * @return FormSectionProperInterface
     * @throws EE_Error
     * @throws ReflectionException
     */
    public function generate_reg_form(): FormSectionProperInterface
    {
        return $this->reg_form_factory->getRegForm([$this]);
    }


    /**
     * @since   $VID:$
     */
    private function setLegacyFiltersForRegFormGeneration()
    {
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegistrantForm__generateFormArgs__question_groups_query_parameters',
            [$this, 'registrationQuestionGroupsQueryParameters'],
            1,
            2
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegistrantForm__generateFormArgs__question_group_reg_form',
            [$this, 'registrationQuestionGroupsRegForm'],
            1,
            3
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegistrantForm__generateFormArgs__printCopyInfo',
            [$this, 'registrationRegFormPrintCopyInfo'],
            1,
            2
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegFormQuestionGroup__generateFormArgs__related_questions_query_params',
            [$this, 'registrationRegFormRelatedQuestionsQueryParams'],
            1,
            3
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegFormQuestionGroup__generateFormArgs__before_question_group_questions',
            [$this, 'registrationRegFormBeforeQuestionGroupQuestions'],
            1,
            3
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegFormQuestionGroup__generateFormArgs__subsections_array',
            [$this, 'registrationRegFormSubsections'],
            1,
            3
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegFormQuestionGroup__generateFormArgs__after_question_group_questions',
            [$this, 'registrationRegFormAfterQuestionGroupQuestions'],
            1,
            3
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_AutoCopyAttendeeInfoForm__construct__template_args',
            [$this, 'autoCopyAttendeeInfoTemplateArgs'],
            1
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_CountryOptions__generateLegacyCountryOptions__country_options',
            [$this, 'generateQuestionInputCountryOptions'],
            1,
            4
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_StateOptions__generateLegacyStateOptions__state_options',
            [$this, 'generateQuestionInputStateOptions'],
            1,
            4
        );
    }


    /**
     * @param array           $query_params
     * @param EE_Registration $registration
     * @return mixed|void
     * @since   $VID:$
     */
    public function registrationQuestionGroupsQueryParameters(
        array $query_params,
        EE_Registration $registration
    ) {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information___registrations_reg_form__question_groups_query_parameters',
            $query_params,
            $registration,
            $this
        );
    }


    /**
     * @param RegFormQuestionGroup $question_group_reg_form
     * @param EE_Registration      $registration
     * @param EE_Question_Group    $question_group
     * @return mixed|void
     * @since   $VID:$
     */
    public function registrationQuestionGroupsRegForm(
        RegFormQuestionGroup $question_group_reg_form,
        EE_Registration $registration,
        EE_Question_Group $question_group
    ) {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information___question_group_reg_form__question_group_reg_form',
            $question_group_reg_form,
            $registration,
            $question_group,
            $this
        );
    }


    /**
     * @param int $print_copy_info
     * @param int $attendee_nmbr
     * @return mixed|void
     * @since   $VID:$
     */
    public function registrationRegFormPrintCopyInfo(
        int $print_copy_info,
        int $attendee_nmbr
    ) {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information___registrations_reg_form___printCopyInfo',
            $print_copy_info,
            $attendee_nmbr
        );
    }


    /**
     * @param array             $query_params
     * @param EE_Question_Group $question_group
     * @param EE_Registration   $registration
     * @return mixed|void
     * @since   $VID:$
     */
    public function registrationRegFormRelatedQuestionsQueryParams(
        array $query_params,
        EE_Question_Group $question_group,
        EE_Registration $registration
    ) {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information___question_group_reg_form__related_questions_query_params',
            $query_params,
            $question_group,
            $registration,
            $this
        );
    }


    /**
     * @param                   $html
     * @param EE_Registration   $registration
     * @param EE_Question_Group $question_group
     * @return mixed|void
     * @since   $VID:$
     */
    public function registrationRegFormBeforeQuestionGroupQuestions(
        $html,
        EE_Registration $registration,
        EE_Question_Group $question_group
    ) {
        return apply_filters(
            'FHEE__EEH_Form_Fields__generate_question_groups_html__before_question_group_questions',
            $html,
            $registration,
            $question_group,
            $this
        );
    }


    /**
     * @param array             $form_subsections
     * @param EE_Registration   $registration
     * @param EE_Question_Group $question_group
     * @return mixed|void
     * @since   $VID:$
     */
    public function registrationRegFormSubsections(
        array $form_subsections,
        EE_Registration $registration,
        EE_Question_Group $question_group
    ) {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information__question_group_reg_form__subsections_array',
            $form_subsections,
            $registration,
            $question_group,
            $this
        );
    }


    /**
     * @param                   $html
     * @param EE_Registration   $registration
     * @param EE_Question_Group $question_group
     * @return mixed|void
     * @since   $VID:$
     */
    public function registrationRegFormAfterQuestionGroupQuestions(
        $html,
        EE_Registration $registration,
        EE_Question_Group $question_group
    ) {
        return apply_filters(
            'FHEE__EEH_Form_Fields__generate_question_groups_html__after_question_group_questions',
            $html,
            $registration,
            $question_group,
            $this
        );
    }


    /**
     * @param array $template_args
     * @return mixed|void
     * @since   $VID:$
     */
    public function autoCopyAttendeeInfoTemplateArgs(array $template_args = [])
    {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information__auto_copy_attendee_info__template_args',
            $template_args
        );
    }


    /**
     * @param array           $country_options
     * @param EE_Registration $registration
     * @param EE_Question     $question
     * @param EE_Answer|null  $answer
     * @return mixed|void
     * @since   $VID:$
     */
    public function generateQuestionInputCountryOptions(
        array $country_options,
        EE_Registration $registration,
        EE_Question $question,
        ?EE_Answer $answer
    ) {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information___generate_question_input__country_options',
            $country_options,
            $this,
            $registration,
            $question,
            $answer
        );
    }


    /**
     * @param array           $state_options
     * @param EE_Registration $registration
     * @param EE_Question     $question
     * @param EE_Answer|null  $answer
     * @return mixed|void
     * @since   $VID:$
     */
    public function generateQuestionInputStateOptions(
        array $state_options,
        EE_Registration $registration,
        EE_Question $question,
        ?EE_Answer $answer
    ) {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information___generate_question_input__state_options',
            $state_options,
            $this,
            $registration,
            $question,
            $answer
        );
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\RegForm::getRegForm()
     *
     * @deprecated   $VID:$
     */
    private function _registrations_reg_form()
    {
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\RegForm::additionalAttendeeRegInfoInput()
     *
     * @deprecated   $VID:$
     */
    private function _additional_attendee_reg_info_input()
    {
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\RegForm::questionGroupRegForm()
     *
     * @deprecated   $VID:$
     */
    private function _question_group_reg_form()
    {
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\RegForm::questionGroupHeader()
     *
     * @deprecated   $VID:$
     */
    private function _question_group_header()
    {
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\CopyAttendeeInfoForm
     *
     * @deprecated   $VID:$
     */
    private function _copy_attendee_info_form()
    {
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\AutoCopyAttendeeInfoForm
     *
     * @deprecated   $VID:$
     */
    private function _auto_copy_attendee_info()
    {
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\CopyAttendeeInfoForm
     *
     * @deprecated   $VID:$
     */
    private function _copy_attendee_info_inputs()
    {
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\RegForm::additionalPrimaryRegistrantInputs()
     *
     * @deprecated   $VID:$
     */
    private function _additional_primary_registrant_inputs()
    {
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\RegFormQuestionFactory::create()
     *
     * @param EE_Registration $registration
     * @param EE_Question     $question
     * @return EE_Form_Input_Base
     * @throws EE_Error
     * @throws ReflectionException
     * @deprecated   $VID:$
     */
    public function reg_form_question(EE_Registration $registration, EE_Question $question): EE_Form_Input_Base
    {
        /** @var RegFormQuestionFactory $reg_form_question_factory */
        $reg_form_question_factory = LoaderFactory::getShared(RegFormQuestionFactory::class);
        return $reg_form_question_factory->create($registration, $question);
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\RegForm::generateQuestionInput()
     *
     * @deprecated   $VID:$
     */
    private function _generate_question_input()
    {
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\CountryOptions::forLegacyFormInput()
     *
     * @param array|null           $countries_list
     * @param EE_Question|null     $question
     * @param EE_Registration|null $registration
     * @param EE_Answer|null       $answer
     * @return array 2d keys are country IDs, values are their names
     * @throws EE_Error
     * @throws ReflectionException
     * @deprecated   $VID:$
     */
    public function use_cached_countries_for_form_input(
        array $countries_list = null,
        EE_Question $question = null,
        EE_Registration $registration = null,
        EE_Answer $answer = null
    ): array {
        /** @var CountryOptions $country_options */
        $country_options = LoaderFactory::getShared(CountryOptions::class, [$this->checkout->action]);
        return $country_options->forLegacyFormInput($countries_list, $question, $registration, $answer);
    }


    /**
     * looking for hooks?
     * this method has been replaced by:
     * EventEspresso\core\domain\services\registration\form\v1\StateOptions::forLegacyFormInput()
     *
     * @param array|null           $states_list
     * @param EE_Question|null     $question
     * @param EE_Registration|null $registration
     * @param EE_Answer|null       $answer
     * @return array 2d keys are state IDs, values are their names
     * @throws EE_Error
     * @throws ReflectionException
     * @deprecated   $VID:$
     */
    public function use_cached_states_for_form_input(
        array $states_list = null,
        EE_Question $question = null,
        EE_Registration $registration = null,
        EE_Answer $answer = null
    ): array {
        /** @var StateOptions $state_options */
        $state_options = LoaderFactory::getShared(StateOptions::class, [$this->checkout->action]);
        return $state_options->forLegacyFormInput($states_list, $question, $registration, $answer);
    }


    /********************************************************************************************************/
    /****************************************  PROCESS REG STEP  ****************************************/
    /********************************************************************************************************/


    /**
     * @return bool
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     */
    public function process_reg_step(): bool
    {
        $this->setLegacyFiltersForRegFormProcessing();
        // grab validated data from form
        $valid_data = $this->checkout->current_step->valid_data();
        // if we don't have any $valid_data then something went TERRIBLY WRONG !!!
        if (empty($valid_data)) {
            return $this->inValidDataError();
        }
        if (! $this->checkout->transaction instanceof EE_Transaction || ! $this->checkout->continue_reg) {
            return $this->inValidTransactionError();
        }
        // get cached registrations
        $registrations = $this->checkout->transaction->registrations($this->checkout->reg_cache_where_params);
        // verify we got the goods
        if (empty($registrations)) {
            return $this->noRegistrationsError();
        }
        $reg_form_handler = $this->reg_form_factory->getRegFormHandler($this->checkout);
        // extract attendee info from form data and save to model objects
        if (! $reg_form_handler->processRegistrations($registrations, $valid_data)) {
            // return immediately if the previous step exited early due to errors
            return false;
        }
        // if first pass thru SPCO,
        // then let's check processed registrations against the total number of tickets in the cart
        $registrations_processed = $reg_form_handler->attendeeCount();
        if (! $this->checkout->revisit && $registrations_processed !== $this->checkout->total_ticket_count) {
            return $this->registrationProcessingError($registrations_processed);
        }
        // mark this reg step as completed
        $this->set_completed();
        $this->_set_success_message(
            esc_html__('The Attendee Information Step has been successfully completed.', 'event_espresso')
        );
        // do action in case a plugin wants to do something with the data submitted in step 1.
        // passes EE_Single_Page_Checkout, and it's posted data
        do_action('AHEE__EE_Single_Page_Checkout__process_attendee_information__end', $this, $valid_data);
        return true;
    }


    /**
     * @since   $VID:$
     */
    private function setLegacyFiltersForRegFormProcessing()
    {
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegFormHandler__processRegistrations__bypass',
            [$this, 'preRegistrationProcess'],
            1,
            5
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegFormHandler__processRegFormData__registrant_form_data',
            [$this, 'validDataLineItem'],
            1,
            2
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegFormInputHandler__saveRegistrationFormInput',
            [$this, 'saveRegistrationFormInput'],
            1,
            4
        );
        add_filter(
            'FHEE__EventEspresso_core_domain_services_registration_form_v1_RegistrantData__ensureCriticalRegistrantDataIsSet',
            [$this, 'mergeAddressDetailsWithCriticalAttendeeDetails'],
            1
        );
    }


    /**
     * @param bool            $bypass
     * @param int             $attendee_count
     * @param EE_Registration $registration
     * @param array           $registrations
     * @param array           $reg_form_data
     * @return mixed|void
     * @since   $VID:$
     */
    public function preRegistrationProcess(
        bool $bypass,
        int $attendee_count,
        EE_Registration $registration,
        array $registrations,
        array $reg_form_data
    ) {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information___process_registrations__pre_registration_process',
            $bypass,
            $attendee_count,
            $registration,
            $registrations,
            $reg_form_data,
            $this
        );
    }


    /**
     * @param array           $reg_form_data
     * @param EE_Registration $registration
     * @return mixed|void
     * @since   $VID:$
     */
    public function validDataLineItem(array $reg_form_data, EE_Registration $registration)
    {
        return apply_filters(
            'FHEE__EE_Single_Page_Checkout__process_attendee_information__valid_data_line_item',
            $reg_form_data,
            $registration
        );
    }


    /**
     * @param bool            $save
     * @param EE_Registration $registration
     * @param                 $form_input
     * @param                 $input_value
     * @return mixed|void
     * @since   $VID:$
     */
    public function saveRegistrationFormInput(bool $save, EE_Registration $registration, $form_input, $input_value)
    {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information___save_registration_form_input',
            $save,
            $registration,
            $form_input,
            $input_value,
            $this
        );
    }


    /**
     * @param bool $merge_data
     * @return mixed|void
     * @since   $VID:$
     */
    public function mergeAddressDetailsWithCriticalAttendeeDetails(bool $merge_data)
    {
        return apply_filters(
            'FHEE__EE_SPCO_Reg_Step_Attendee_Information__merge_address_details_with_critical_attendee_details',
            $merge_data
        );
    }


    /**
     * @return bool
     * @since   $VID:$
     */
    private function inValidDataError(): bool
    {
        EE_Error::add_error(
            esc_html__('No valid question responses were received.', 'event_espresso'),
            __FILE__,
            __FUNCTION__,
            __LINE__
        );
        return false;
    }


    /**
     * @return bool
     * @since   $VID:$
     */
    private function inValidTransactionError(): bool
    {
        EE_Error::add_error(
            esc_html__(
                'A valid transaction could not be initiated for processing your registrations.',
                'event_espresso'
            ),
            __FILE__,
            __FUNCTION__,
            __LINE__
        );
        return false;
    }


    /**
     * @return bool
     * @since   $VID:$
     */
    private function noRegistrationsError(): bool
    {
        // combine the old translated string with a new one, in order to not break translations
        $error_message = esc_html__(
            'Your form data could not be applied to any valid registrations.',
            'event_espresso'
        );
        $error_message .= sprintf(
            esc_html_x(
                '%3$sThis can sometimes happen if too much time has been taken to complete the registration process.%3$sPlease return to the %1$sEvent List%2$s and reselect your tickets. If the problem continues, please contact the site administrator.',
                '(line break)This can sometimes happen if too much time has been taken to complete the registration process.(line break)Please return to the (link)Event List(end link) and reselect your tickets. If the problem continues, please contact the site administrator.',
                'event_espresso'
            ),
            '<a href="' . get_post_type_archive_link('espresso_events') . '" >',
            '</a>',
            '<br />'
        );
        EE_Error::add_error($error_message, __FILE__, __FUNCTION__, __LINE__);
        return false;
    }


    /**
     * @param int $registrations_processed
     * @return bool
     * @since   $VID:$
     */
    private function registrationProcessingError(int $registrations_processed): bool
    {
        // generate a correctly translated string for all possible singular/plural combinations
        if ($this->checkout->total_ticket_count === 1 && $registrations_processed !== 1) {
            $error_msg = sprintf(
                esc_html_x(
                    'There was %1$d ticket in the Event Queue, but %2$ds registrations were processed',
                    'There was 1 ticket in the Event Queue, but 2 registrations were processed',
                    'event_espresso'
                ),
                $this->checkout->total_ticket_count,
                $registrations_processed
            );
        } elseif ($this->checkout->total_ticket_count !== 1 && $registrations_processed === 1) {
            $error_msg = sprintf(
                esc_html_x(
                    'There was a total of %1$d tickets in the Event Queue, but only %2$ds registration was processed',
                    'There was a total of 2 tickets in the Event Queue, but only 1 registration was processed',
                    'event_espresso'
                ),
                $this->checkout->total_ticket_count,
                $registrations_processed
            );
        } else {
            $error_msg = sprintf(
                esc_html__(
                    'There was a total of 2 tickets in the Event Queue, but 2 registrations were processed',
                    'event_espresso'
                ),
                $this->checkout->total_ticket_count,
                $registrations_processed
            );
        }
        EE_Error::add_error($error_msg, __FILE__, __FUNCTION__, __LINE__);
        return false;
    }


    /**
     *    update_reg_step
     *    this is the final step after a user  revisits the site to edit their attendee information
     *    this gets called AFTER the process_reg_step() method above
     *
     * @return bool
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     */
    public function update_reg_step(): bool
    {
        // save everything
        if ($this->process_reg_step()) {
            $this->checkout->redirect     = true;
            $this->checkout->redirect_url = add_query_arg(
                [
                    'e_reg_url_link' => $this->checkout->reg_url_link,
                    'revisit'        => true,
                ],
                $this->checkout->thank_you_page_url
            );
            $this->checkout->json_response->set_redirect_url($this->checkout->redirect_url);
            return true;
        }
        return false;
    }
}
