@php Theme::layout('no-sidebar'); @endphp

<div class="container">
    <div style="margin: 40px 0;">
        <h4 style="color: #f00">You need to setup your homepage first!</h4>

        <p><strong>1. Go to Admin -> Plugins then activate all plugins.</strong></p>
        <p><strong>2. Go to Admin -> Pages and create a page:</strong></p>

        <div style="margin: 20px 0;">
            <div>- Content:</div>
            <div style="border: 1px solid rgba(0, 0, 0, 0.1);padding: 10px;margin-top: 10px;direction: ltr;">
                <div>[featured-posts][/featured-posts]</div>
                <div>[recent-posts title="What's new?"][/recent-posts]</div>
                <div>[featured-categories-posts title="Best for you"][/featured-categories-posts]</div>
                <div>[all-galleries limit="8"][/all-galleries]</div>
            </div>
            <br>
            <div>- Template: <strong>No sidebar</strong>.</div>
        </div>

        <p><strong>3. Then go to Admin -> Appearance -> Theme options -> Page to set your homepage.</strong></p>
    </div>
</div>
