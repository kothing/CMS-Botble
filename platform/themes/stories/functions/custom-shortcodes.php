<?php

app()->booted(function () {
    add_shortcode('custom-html', __('Custom HTML'), __('Add custom HTML content'), function ($shortCode) {
        return html_entity_decode($shortCode->content);
    });

    shortcode()->setAdminConfig('custom-html', function ($attributes, $content) {
        return '<div class="form-group mb-3">
            <label class="control-label">' . __('Content') . '</label>' .
            Form::textarea('content', $content, ['class' => 'form-control', 'data-shortcode-attribute' => 'content', 'rows' => 3, 'placeholder' => __('HTML code')]) .
        '</div>';
    });
});
