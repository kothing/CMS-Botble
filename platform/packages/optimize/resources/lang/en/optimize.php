<?php

return [
    'settings' => [
        'title' => 'Optimize page speed',
        'description' => 'Minify HTML output, inline CSS, remove comments...',
        'enable' => 'Enable optimize page speed?',
    ],
    'collapse_white_space' => 'Collapse white space',
    'collapse_white_space_description' => 'This filter reduces bytes transmitted in an HTML file by removing unnecessary whitespace.',
    'elide_attributes' => 'Elide attributes',
    'elide_attributes_description' => 'This filter reduces the transfer size of HTML files by removing attributes from tags when the specified value is equal to the default value for that attribute. This can save a modest number of bytes, and may make the document more compressible by canonicalizing the affected tags.',
    'inline_css' => 'Inline CSS',
    'inline_css_description' => 'This filter transforms the inline "style" attribute of tags into classes by moving the CSS to the header.',
    'insert_dns_prefetch' => 'Insert DNS prefetch',
    'insert_dns_prefetch_description' => 'This filter injects tags in the HEAD to enable the browser to do DNS prefetching.',
    'remove_comments' => 'Remove comments',
    'remove_comments_description' => 'This filter eliminates HTML, JS and CSS comments. The filter reduces the transfer size of HTML files by removing the comments. Depending on the HTML file, this filter can significantly reduce the number of bytes transmitted on the network.',
    'remove_quotes' => 'Remove quotes',
    'remove_quotes_description' => 'This filter eliminates unnecessary quotation marks from HTML attributes. While required by the various HTML specifications, browsers permit their omission when the value of an attribute is composed of a certain subset of characters (alphanumerics and some punctuation characters).',
    'defer_javascript' => 'Defer javascript',
    'defer_javascript_description' => 'Defers the execution of javascript in the HTML. If necessary cancel deferring in some script, use data-pagespeed-no-defer as script attribute to cancel deferring.',
];
