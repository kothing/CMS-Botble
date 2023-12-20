{!! '<' . '?' . 'xml version="1.0" encoding="UTF-8"?>' . "\n" !!}
@if (null != $style)
    {!! '<' . '?' . 'xml-stylesheet href="' . asset($style) . '" type="text/xsl"?>' . "\n" !!}
@endif
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    @foreach ($items as $item)
        <url>
            <loc>{{  $item['loc'] }}</loc>
            @if ($item['lastmod'] !== null)
                <lastmod>{{ date('Y-m-d\TH:i:sP', strtotime($item['lastmod'])) }}</lastmod>
            @endif
            @if (!empty($item['alternates']))
                @foreach ($item['alternates'] as $alternate)
                    <xhtml:link rel="alternate" media="{{ $alternate['media'] }}" href="{{ $alternate['url'] }}"/>
                @endforeach
            @endif
            <news:news>
                <news:publication>
                    <news:name>{{  $item['googlenews']['sitename'] }}</news:name>
                    <news:language>{{  $item['googlenews']['language'] }}</news:language>
                </news:publication>
                <news:publication_date>{{  date('Y-m-d\TH:i:sP',
                    strtotime($item['googlenews']['publication_date'])) }}</news:publication_date>
                <news:title>{{  $item['title'] }}</news:title>
                @if (isset($item['googlenews']['access']))
                    <news:access>{{ $item['googlenews']['access'] }}</news:access>
                @endif

                @if (isset($item['googlenews']['keywords']))
                    <news:keywords>{{ implode(',', $item['googlenews']['keywords']) }}</news:keywords>
                @endif

                @if (isset($item['googlenews']['genres']))
                    <news:genres>{{ implode(',', $item['googlenews']['genres']) }}</news:genres>;
                @endif

                @if (isset($item['googlenews']['stock_tickers']))
                    <news:stock_tickers>{{ implode(',', $item['googlenews']['stock_tickers']) }}</news:stock_tickers>
                @endif
            </news:news>
        </url>
    @endforeach
</urlset>
