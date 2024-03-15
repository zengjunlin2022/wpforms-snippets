<?php
/*
Plugin Name: WPForms DatePicker Format
Plugin URI: https://wpforms.com/
Description: DatePicker 日期格式化
Author: Ken
Version: 1.0
Author URI: https://wpforms.com/
*/

/**
 * Filters the date field formats available for the Date Picker in the form builder.
 *
 * @link   https://wpforms.com/developers/wpforms_datetime_date_formats/
 *
 * @param  array $formats Date format options.
 * @return array
 */

function wpf_dev_date_field_formats( $formats ) {

    // Item key is JS date character - see https://flatpickr.js.org/formatting/
    // Item value is in PHP format - see http://php.net/manual/en/function.date.php

    // Adds new format Monday, 20th of December 2021
    $formats[ 'Y-m-d' ] = 'Y-m-d';

    return $formats;
}

add_filter( 'wpforms_datetime_date_formats', 'wpf_dev_date_field_formats', 10, 1 );
