<?php

// Change data show in front page
if (! defined('BASE_FILTER_BEFORE_GET_FRONT_PAGE_ITEM')) {
    define('BASE_FILTER_BEFORE_GET_FRONT_PAGE_ITEM', 'before_get_front_page_item');
}

if (! defined('BASE_FILTER_BEFORE_GET_SINGLE')) {
    define('BASE_FILTER_BEFORE_GET_SINGLE', 'before_get_home_page_data_single');
}

// Use to add meta box to each module
if (! defined('BASE_ACTION_META_BOXES')) {
    define('BASE_ACTION_META_BOXES', 'meta_boxes');
}

if (! defined('BASE_ACTION_FORM_ACTIONS')) {
    define('BASE_ACTION_FORM_ACTIONS', 'base_form_actions');
}

if (! defined('BASE_ACTION_FORM_ACTIONS_TITLE')) {
    define('BASE_ACTION_FORM_ACTIONS_TITLE', 'base_form_actions_title');
}

if (! defined('BASE_LANGUAGE_FLAG_PATH')) {
    define('BASE_LANGUAGE_FLAG_PATH', '/vendor/core/core/base/images/flags/');
}

if (! defined('BASE_FILTER_GROUP_PUBLIC_ROUTE')) {
    define('BASE_FILTER_GROUP_PUBLIC_ROUTE', 'group_public_route');
}

if (! defined('BASE_FILTER_REGISTER_CONTENT_TABS')) {
    define('BASE_FILTER_REGISTER_CONTENT_TABS', 'register_platform_content_tabs');
}

if (! defined('BASE_FILTER_REGISTER_CONTENT_TAB_INSIDE')) {
    define('BASE_FILTER_REGISTER_CONTENT_TAB_INSIDE', 'register_platform_content_tab_inside');
}

if (! defined('BASE_ACTION_SITE_ERROR')) {
    define('BASE_ACTION_SITE_ERROR', 'handle_site_error');
}

if (! defined('BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION')) {
    define('BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION', 'base_top_form_content_notification');
}

if (! defined('BASE_FILTER_TOP_HEADER_LAYOUT')) {
    define('BASE_FILTER_TOP_HEADER_LAYOUT', 'base_filter_top_header_layout');
}

if (! defined('BASE_ACTION_ENQUEUE_SCRIPTS')) {
    define('BASE_ACTION_ENQUEUE_SCRIPTS', 'base_action_enqueue_scripts');
}

if (! defined('BASE_FILTER_DASHBOARD_MENU')) {
    define('BASE_FILTER_DASHBOARD_MENU', 'base_filter_dashboard_menu');
}

if (! defined('BASE_FILTER_APPEND_MENU_NAME')) {
    define('BASE_FILTER_APPEND_MENU_NAME', 'base_filter_append_menu_name');
}

if (! defined('BASE_ACTION_PUBLIC_RENDER_SINGLE')) {
    define('BASE_ACTION_PUBLIC_RENDER_SINGLE', 'base_action_public_render_single');
}

if (! defined('BASE_ACTION_AFTER_CREATE_CONTENT')) {
    define('BASE_ACTION_AFTER_CREATE_CONTENT', 'after_create_content');
}

if (! defined('BASE_ACTION_AFTER_UPDATE_CONTENT')) {
    define('BASE_ACTION_AFTER_UPDATE_CONTENT', 'after_update_content');
}

if (! defined('BASE_ACTION_AFTER_DELETE_CONTENT')) {
    define('BASE_ACTION_AFTER_DELETE_CONTENT', 'after_delete_content');
}

if (! defined('BASE_ACTION_BEFORE_EDIT_CONTENT')) {
    define('BASE_ACTION_BEFORE_EDIT_CONTENT', 'before_edit_content');
}

if (! defined('BASE_FILTER_PUBLIC_SINGLE_DATA')) {
    define('BASE_FILTER_PUBLIC_SINGLE_DATA', 'filter_public_single_data');
}

if (! defined('BASE_FILTER_PUBLIC_COMMENT_AREA')) {
    define('BASE_FILTER_PUBLIC_COMMENT_AREA', 'filter_public_comment_area');
}

if (! defined('BASE_FILTER_BEFORE_GET_BY_SLUG')) {
    define('BASE_FILTER_BEFORE_GET_BY_SLUG', 'before_get_home_page_data_by_slug');
}

if (! defined('BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM')) {
    define('BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM', 'before_get_admin_list_item');
}

if (! defined('BASE_FILTER_BEFORE_GET_ADMIN_SINGLE_ITEM')) {
    define('BASE_FILTER_BEFORE_GET_ADMIN_SINGLE_ITEM', 'before_get_admin_single_item');
}

if (! defined('BASE_FILTER_EMAIL_TEMPLATE')) {
    define('BASE_FILTER_EMAIL_TEMPLATE', 'base_filter_email_template');
}

if (! defined('BASE_FILTER_EMAIL_TEMPLATE_HEADER')) {
    define('BASE_FILTER_EMAIL_TEMPLATE_HEADER', 'base_filter_email_template_header');
}

if (! defined('BASE_FILTER_EMAIL_TEMPLATE_FOOTER')) {
    define('BASE_FILTER_EMAIL_TEMPLATE_FOOTER', 'base_filter_email_template_footer');
}

if (! defined('BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM')) {
    define('BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM', 'base_filter_after_login_or_register_form');
}

if (! defined('BASE_FILTER_BEFORE_RENDER_FORM')) {
    define('BASE_FILTER_BEFORE_RENDER_FORM', 'base_filter_before_render_form');
}

if (! defined('BASE_FILTER_AFTER_FORM_CREATED')) {
    define('BASE_FILTER_AFTER_FORM_CREATED', 'base_filter_after_form_created');
}

if (! defined('BASE_FILTER_FORM_EDITOR_BUTTONS')) {
    define('BASE_FILTER_FORM_EDITOR_BUTTONS', 'base_filter_form_editor_buttons');
}

if (! defined('BASE_ACTION_INIT')) {
    define('BASE_ACTION_INIT', 'init');
}

if (! defined('IS_IN_ADMIN_FILTER')) {
    define('IS_IN_ADMIN_FILTER', 'is_in_admin');
}

if (! defined('BASE_FILTER_ENUM_LABEL')) {
    define('BASE_FILTER_ENUM_LABEL', 'base_filter_enum_label');
}

if (! defined('BASE_FILTER_ENUM_ARRAY')) {
    define('BASE_FILTER_ENUM_ARRAY', 'base_filter_enum_array');
}

if (! defined('BASE_FILTER_ENUM_HTML')) {
    define('BASE_FILTER_ENUM_HTML', 'base_filter_enum_html');
}

if (! defined('BASE_FILTER_SITE_LANGUAGE_DIRECTION')) {
    define('BASE_FILTER_SITE_LANGUAGE_DIRECTION', 'base_filter_site_language_direction');
}

if (! defined('BASE_FILTER_ADMIN_LANGUAGE_DIRECTION')) {
    define('BASE_FILTER_ADMIN_LANGUAGE_DIRECTION', 'base_filter_admin_language_direction');
}

if (! defined('BASE_FILTER_FOOTER_LAYOUT_TEMPLATE')) {
    define('BASE_FILTER_FOOTER_LAYOUT_TEMPLATE', 'base_filter_footer_layout_template');
}

if (! defined('BASE_FILTER_HEADER_LAYOUT_TEMPLATE')) {
    define('BASE_FILTER_HEADER_LAYOUT_TEMPLATE', 'base_filter_header_layout_template');
}

if (! defined('BASE_FILTER_MENU_ITEMS_COUNT')) {
    define('BASE_FILTER_MENU_ITEMS_COUNT', 'base_filter_menu_items_count');
}

if (! defined('BASE_FILTER_AVAILABLE_EDITORS')) {
    define('BASE_FILTER_AVAILABLE_EDITORS', 'base_filter_available_editors');
}
