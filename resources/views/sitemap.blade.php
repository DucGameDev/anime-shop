<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Trang chủ --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    {{-- Trang sản phẩm --}}
    <url>
        <loc>{{ route('products.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    {{-- Static pages --}}
    <url>
        <loc>{{ route('static.order-guide') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('static.payment') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('static.shipping') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc>{{ route('static.returns') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    {{-- Danh mục --}}
    @foreach ($categories as $category)
    <url>
        <loc>{{ route('products.index', ['category' => $category->slug]) }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
        @if ($category->updated_at)
        <lastmod>{{ $category->updated_at->toAtomString() }}</lastmod>
        @endif
    </url>
    @endforeach

    {{-- Sản phẩm --}}
    @foreach ($products as $product)
    <url>
        <loc>{{ route('products.show', $product->slug) }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
        @if ($product->updated_at)
        <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
        @endif
    </url>
    @endforeach

</urlset>
