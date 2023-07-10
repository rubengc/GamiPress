<?php
/**
 * Listeners
 *
 * @package GamiPress\Forminator\Listeners
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/*
 * ----------------------------------------
 *  FORMS
 * ----------------------------------------
 */

/**
 * Listener for a new form submission
 *
 * @since 1.0.0
 *
 * @param int   $form_id - the form id
 * @param array $response - the post response
 */
function gamipress_forminator_form_submission_listener( $form_id, $response ) {

    // Bail if there is any error on the form
    if ( ! $response['success'] ) return;

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) return;

    // Trigger submit any form
    do_action( 'gamipress_forminator_form_submission', $form_id, $user_id, $response );

    // Trigger submit a specific form
    do_action( 'gamipress_forminator_specific_form_submission', $form_id, $user_id, $response );

}
add_action( 'forminator_form_after_save_entry', 'gamipress_forminator_form_submission_listener', 10, 2 );

/**
 * Listener for form field submission
 *
 * @since 1.0.0
 *
 * @param Forminator_Form_Entry_Model   $entry - the entry model
 * @param int                           $form_id - the form id
 * @param array                         $field_data - the entry data
 */
function gamipress_forminator_form_field_submission_listener( $entry, $form_id, $field_data ) {

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) return;

    foreach( $field_data as $data ) {

        // Skip field
        if( ! isset( $data['name'] ) && ! isset( $data['value'] ) ) continue;

        // Skip forminator hidden fields
        if( $data['name'] === '_forminator_user_ip' ) continue;

        $field_name = $data['name'];
        $field_value = $data['value'];

        /**
         * Excluded fields event by filter
         *
         * @since 1.0.4
         *
         * @param bool      $exclude        Whatever to exclude or not, by default false
         * @param string    $field_name     Field name
         * @param mixed     $field_value    Field value
         * @param array     $field          Field setup array
         */
        if( apply_filters( 'gamipress_forminator_form_exclude_field', false, $field_name, $field_value, $data ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_forminator_form_field_value_submission', $form_id, $user_id, $field_name, $field_value, $field_data );

        // Trigger event for submit a specific field value of a specific poll
        do_action( 'gamipress_forminator_specific_form_field_value_submission', $form_id, $user_id, $field_name, $field_value, $field_data );
    }

}
add_action( 'forminator_custom_form_submit_before_set_fields', 'gamipress_forminator_form_field_submission_listener', 10, 3 );

/*
 * ----------------------------------------
 *  POLLS
 * ----------------------------------------
 */

/**
 * Listener for a new poll submission
 *
 * @since 1.0.0
 *
 * @param Forminator_Form_Entry_Model   $entry - the entry model
 * @param int                           $form_id - the form id
 * @param array                         $field_data - the entry data
 */
function gamipress_forminator_poll_submission_listener( $entry, $form_id, $field_data ) {

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) return;

    // Trigger vote on any poll
    do_action( 'gamipress_forminator_poll_submission', $form_id, $user_id, $field_data );

    // Trigger vote on a specific poll
    do_action( 'gamipress_forminator_specific_poll_submission', $form_id, $user_id, $field_data );

}
add_action( 'forminator_polls_submit_before_set_fields', 'gamipress_forminator_poll_submission_listener', 10, 3 );

/**
 * Listener for poll field submission
 *
 * @since 1.0.0
 *
 * @param Forminator_Form_Entry_Model   $entry - the entry model
 * @param int                           $form_id - the form id
 * @param array                         $field_data - the entry data
 */
function gamipress_forminator_poll_field_submission_listener( $entry, $form_id, $field_data ) {

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) return;

    foreach( $field_data as $data ) {

        // Skip field
        if( ! isset( $data['name'] ) && ! isset( $data['value'] ) ) continue;

        // Skip forminator hidden fields
        if( $data['name'] === '_forminator_user_ip' ) continue;

        $field_name = $data['name'];
        $field_value = $data['value'];

        /**
         * Excluded fields event by filter
         *
         * @since 1.0.4
         *
         * @param bool      $exclude        Whatever to exclude or not, by default false
         * @param string    $field_name     Field name
         * @param mixed     $field_value    Field value
         * @param array     $field          Field setup array
         */
        if( apply_filters( 'gamipress_forminator_poll_exclude_field', false, $field_name, $field_value, $data ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_forminator_poll_field_value_submission', $form_id, $user_id, $field_name, $field_value, $field_data );

        // Trigger event for submit a specific field value of a specific poll
        do_action( 'gamipress_forminator_specific_poll_field_value_submission', $form_id, $user_id, $field_name, $field_value, $field_data );
    }

}
add_action( 'forminator_polls_submit_before_set_fields', 'gamipress_forminator_poll_field_submission_listener', 10, 3 );

/*
 * ----------------------------------------
 *  QUIZZES
 * ----------------------------------------
 */

/**
 * Listener for a new quiz submission
 *
 * @since 1.0.0
 *
 * @param Forminator_Form_Entry_Model $entry      - the entry model
 * @param int                         $form_id    - the form id
 * @param array                       $field_data - the entry data
 */
function gamipress_forminator_quiz_submission_listener( $entry, $form_id, $field_data ) {

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) return;

    // Trigger submit any quiz
    do_action( 'gamipress_forminator_quiz_submission', $form_id, $user_id, $field_data );

    // Trigger submit a specific quiz
    do_action( 'gamipress_forminator_specific_quiz_submission', $form_id, $user_id, $field_data );

}
add_action( 'forminator_quizzes_submit_before_set_fields', 'gamipress_forminator_quiz_submission_listener', 10, 3 );

/**
 * Listener for quiz completion
 *
 * @since 1.0.0
 *
 * @param Forminator_Form_Entry_Model $entry      - the entry model
 * @param int                         $form_id    - the form id
 * @param array                       $field_data - the entry data
 */
function gamipress_forminator_quiz_completion_listener( $entry, $form_id, $field_data ) {

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) return;

    // Loop all answers to meet if all of them are correct
    $all_correct = true;

    foreach( $field_data as $data ) {
        if( isset( $data['isCorrect'] ) && $data['isCorrect'] === false ) {
            $all_correct = false;
            break;
        }
    }

    if( $all_correct ) {
        // Trigger pass any quiz
        do_action( 'gamipress_forminator_pass_quiz', $form_id, $user_id, $field_data );

        // Trigger pass a specific quiz
        do_action( 'gamipress_forminator_pass_specific_quiz', $form_id, $user_id, $field_data );
    } else {
        // Trigger fail any quiz
        do_action( 'gamipress_forminator_fail_quiz', $form_id, $user_id, $field_data );

        // Trigger fail a specific quiz
        do_action( 'gamipress_forminator_fail_specific_quiz', $form_id, $user_id, $field_data );
    }

}
add_action( 'forminator_quizzes_submit_before_set_fields', 'gamipress_forminator_quiz_completion_listener', 10, 3 );

/**
 * Listener for quiz field submission
 *
 * @since 1.0.0
 *
 * @param Forminator_Form_Entry_Model $entry      - the entry model
 * @param int                         $form_id    - the form id
 * @param array                       $field_data - the entry data
 */
function gamipress_forminator_quiz_field_submission_listener( $entry, $form_id, $field_data ) {

    $user_id = get_current_user_id();

    // Bail if user is not logged in
    if( $user_id === 0 ) return;

    // Quizzes have 2 data formats
    // Personality quizzes has an array of answers inside with keys: question, answer
    // Knowledge quizzes has an array of fields with keys: question, answer, isCorrect

    $answers = $field_data;

    if( isset( $field_data[0] )
        && isset( $field_data[0]['value'] )
        && isset( $field_data[0]['value']['answers'] ) ) {
        $answers = $field_data[0]['value']['answers'];
    }

    foreach( $answers as $data ) {

        // Skip field
        if( ! isset( $data['question'] ) && ! isset( $data['answer'] ) ) continue;

        $field_name = $data['question'];
        $field_value = $data['answer'];

        /**
         * Excluded fields event by filter
         *
         * @since 1.0.4
         *
         * @param bool      $exclude        Whatever to exclude or not, by default false
         * @param string    $field_name     Field name
         * @param mixed     $field_value    Field value
         * @param array     $field          Field setup array
         */
        if( apply_filters( 'gamipress_forminator_quiz_exclude_field', false, $field_name, $field_value, $data ) )
            continue;

        // Trigger event for submit a specific field value
        do_action( 'gamipress_forminator_quiz_field_value_submission', $form_id, $user_id, $field_name, $field_value, $field_data );

        // Trigger event for submit a specific field value of a specific quiz
        do_action( 'gamipress_forminator_specific_quiz_field_value_submission', $form_id, $user_id, $field_name, $field_value, $field_data );
    }

}
add_action( 'forminator_quizzes_submit_before_set_fields', 'gamipress_forminator_quiz_field_submission_listener', 10, 3 );