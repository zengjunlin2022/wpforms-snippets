<?php
/*
Plugin Name: WPForms Smart Tag Serial Number
Plugin URI: https://wpforms.com/
Description: 為表單生成流水號，格式為YYYYMMDDXXXXX，其中XXXXX是當天提交的第幾個表單，不足五位數的前面補0。
Author: Ken
Version: 1.0
Author URI: https://wpforms.com/

根據日期以及表單提交的記數生成流水號，格式為YYYYMMDDXXXXX，其中XXXXX是當天提交的第幾個表單，不足五位數的前面補0。
*/

/*
 * Create a unique_id numeric-only Smart Tag and assign it to each form submission.
 *
 * @link https://wpforms.com/developers/how-to-create-a-unique-id-for-each-form-entry/
 */

// Generate Unique ID Smart Tag for WPForms
function wpf_dev_register_smarttag( $tags ) {

    // Key is the tag, item is the tag name.
    $tags[ 'serial_number_id' ] = 'Serial Number ID';

    return $tags;
}

add_filter( 'wpforms_smart_tags', 'wpf_dev_register_smarttag' );

// Generate Unique ID value
function wpf_dev_process_smarttag( $content, $tag, $form_data, $fields, $entry_id ) {
    // PHP Warning:  Undefined array key "id" in $form_data[ 'id' ]
    if ( empty( $form_data ) ) {
        return $content;
    }

    // 當前formId
    $formId = $form_data[ 'id' ];
    // 獲取當前日期，格式YYYYMMDD
    $currentDate = date('Ymd');
    // 當前日期的開始時間Y-m-d 00:00:00 與結束時間 Y-m-d 23:59:59
    $currentDateStart = strtotime( $entry_id .' 00:00:00' );
    $currentDateEnd = strtotime( $entry_id .' 23:59:59' );
    // 獲取當天提交的表單數量
    $submitCount = wpforms()->entry->get_entries(
        array(
            'form_id' => $formId,
            'date' => array(
                $currentDateStart,
                $currentDateEnd
            )
        ),
        true
    );
    // $submitCount 不足五位數的前面補0
    if ( empty( $submitCount ) ) {
        $submitCount = 0;
    }
    $submitCount = str_pad( $submitCount, 5, '0', STR_PAD_LEFT );
    // 拼接流水號
    $serialNo = $currentDate . $submitCount;

    // Only run if it is our desired tag.
    if ( 'serial_number_id' === $tag && !$entry_id ) {

        // Replace the tag with our Unique ID.
        $content = str_replace( '{serial_number_id}', $serialNo, $content );
    } elseif ( 'serial_number_id' === $tag && $entry_id ) {

        foreach ($form_data[ 'fields' ] as $field) {

            if ( preg_match('/\b{serial_number_id}\b/', $field[ 'default_value' ]) ) {
                $field_id = $field[ 'id' ];
                break;
            }

        }

        $content = str_replace( '{serial_number_id}', $fields[$field_id][ 'value' ], $content);
    }

    return $content;
}

add_filter( 'wpforms_smart_tag_process', 'wpf_dev_process_smarttag', 10, 5 );