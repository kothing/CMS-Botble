@php Theme::layout('homepage'); @endphp

<div class="container">
    <div style="margin: 40px 0;">
        <h4 style="color: #f00; margin-bottom: 15px;">You need to setup your homepage first!</h4>

        <p><strong>1. Go to Admin -> Plugins then activate all plugins.</strong></p>
        <p><strong>2. Go to Admin -> Pages and create a page:</strong></p>

        <div style="margin: 20px 0;">
            <div>- Content:</div>
            <div style="border: 1px solid rgba(0,0,0,.1); padding: 10px; margin-top: 10px;direction: ltr;">
                <div>[about-banner title="Hello, I’m &lt;span&gt;Steven&lt;/span&gt;" subtitle="Welcome to my blog" text_muted="Travel Blogger., Content Writer., Food Guides" image="general/featured.png" newsletter_title="Don't miss out on the latest news about Travel tips, Hotels review, Food guide..."][/about-banner]</div>
                <div>[featured-posts title="Featured posts"][/featured-posts]</div>
                <div>[blog-categories-posts category_id="2"][/blog-categories-posts]</div>
                <div>[categories-with-posts category_id_1="3" category_id_2="4" category_id_3="5"][/categories-with-posts]</div>
                <div>[featured-categories title="Categories"][/featured-categories]</div>
            </div>
            <br>
            <div>- Template: <strong>Homepage</strong>.</div>
        </div>

        <p><strong>3. Then go to Admin -> Appearance -> Theme options -> Page to set your homepage.</strong></p>
    </div>
</div>
