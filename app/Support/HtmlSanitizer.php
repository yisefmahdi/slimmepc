<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Server-side HTML sanitizer for admin-authored rich text
 * (TinyMCE product descriptions, AI-generated descriptions, legacy imports).
 *
 * Goal: keep everyday formatting (p, headings, lists, links, images, tables)
 * while stripping active content: scripts, event handlers, javascript:/data:
 * URLs, iframes/objects/embeds and style-based expression() payloads.
 *
 * This is deliberately dependency-free (no HTMLPurifier) so it works
 * everywhere including SQLite test runs.
 */
class HtmlSanitizer
{
    /** Tags allowed in product descriptions. Everything else is unwrapped (children kept). */
    protected const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'strike',
        'h2', 'h3', 'h4',
        'ul', 'ol', 'li',
        'a', 'img',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
        'blockquote', 'pre', 'code', 'hr', 'span', 'div',
    ];

    /** Per-tag allowed attributes. */
    protected const ALLOWED_ATTRS = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'th' => ['colspan', 'rowspan', 'scope'],
        'td' => ['colspan', 'rowspan'],
    ];

    /**
     * Clean a product description. Always returns a string (never null).
     */
    public static function productDescription(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $prev = libxml_use_internal_errors(true);

        $doc = new \DOMDocument('1.0', 'UTF-8');
        // Wrap in a container so fragments with several roots survive.
        $doc->loadHTML(
            '<?xml encoding="UTF-8"><div id="__root__">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = $doc->getElementById('__root__') ?? $doc->documentElement;

        /** @var \DOMElement[] $all */
        $all = iterator_to_array($root->getElementsByTagName('*'));

        foreach ($all as $el) {
            $tag = strtolower($el->tagName);

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                // Unwrap disallowed tags (script/style/iframe/object/…):
                // drop the element itself for known-dangerous tags,
                // keep children as text flow for the rest.
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'link', 'meta', 'base', 'noscript'], true)) {
                    $el->parentNode?->removeChild($el);
                } else {
                    $frag = $doc->createDocumentFragment();
                    while ($el->firstChild) {
                        $frag->appendChild($el->firstChild);
                    }
                    $el->parentNode?->replaceChild($frag, $el);
                }
                continue;
            }

            // Strip every attribute that is not explicitly allowed…
            $allowed = self::ALLOWED_ATTRS[$tag] ?? [];
            foreach (iterator_to_array($el->attributes) as $attr) {
                $name = strtolower($attr->nodeName);
                if (str_starts_with($name, 'on') || ! in_array($name, $allowed, true)) {
                    $el->removeAttribute($attr->nodeName);
                }
            }

            // …and validate the remaining URL attributes.
            foreach (['href', 'src'] as $urlAttr) {
                if ($el->hasAttribute($urlAttr)) {
                    $url = trim((string) $el->getAttribute($urlAttr));
                    if (! self::isSafeUrl($url, $urlAttr === 'src')) {
                        $el->removeAttribute($urlAttr);
                    } elseif ($tag === 'a') {
                        // Harden outbound links opened in a new tab.
                        if (strtolower((string) $el->getAttribute('target')) === '_blank') {
                            $el->setAttribute('rel', 'noopener noreferrer');
                        }
                    }
                }
            }

            // Drop style attributes entirely (expression()/behavior URLs).
            if ($el->hasAttribute('style')) {
                $el->removeAttribute('style');
            }
        }

        $out = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim((string) $out);
    }

    /**
     * Only http(s), mailto, tel and site-relative URLs are allowed.
     * Images additionally allow data:image/* (TinyMCE inline pastes).
     */
    protected static function isSafeUrl(string $url, bool $isImage = false): bool
    {
        if ($url === '' || $url === '#') {
            return $url === '#';
        }

        // Site-relative or anchor links.
        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if ($scheme === '') {
            // Relative URL without scheme (e.g. "page", "../x") — harmless.
            return ! preg_match('/^\s*(javascript|data|vbscript|file)\s*:/i', $url);
        }

        if (in_array($scheme, ['http', 'https', 'mailto', 'tel'], true)) {
            return true;
        }

        if ($isImage && $scheme === 'data') {
            return (bool) preg_match('#^data:image/(png|jpeg|gif|webp|avif);base64,#i', $url);
        }

        return false;
    }

    /**
     * Quick check used by tests: does the string still contain active content?
     */
    public static function containsActiveContent(string $html): bool
    {
        return (bool) preg_match(
            '/<\s*(script|iframe|object|embed)\b|on\w+\s*=|javascript\s*:|data\s*:\s*text\/html/i',
            $html
        );
    }
}
