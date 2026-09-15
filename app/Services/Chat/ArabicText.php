<?php

namespace App\Services\Chat;

/**
 * Arabische normalisatie + AR→NL producttermen voor chat-zoekopdrachten.
 */
class ArabicText
{
    /**
     * Arabische productwoorden → Nederlandse/Dutch-equivalenten (LIKE-baar).
     *
     * @var array<string, string>
     */
    protected static array $productTerms = [
        'لابتوب' => 'laptop',
        'لالبتوب' => 'laptop',
        'حاسوب' => 'computer',
        'كمبيوتر' => 'computer',
        'جوال' => 'telefoon',
        'هاتف' => 'telefoon',
        'ايفون' => 'iphone',
        'تابلت' => 'tablet',
        'ايباد' => 'ipad',
        'بلاي' => 'playstation',
        'ستيشن' => 'playstation',
        'اكس' => 'xbox',
        'بوكس' => 'xbox',
        'العاب' => 'gaming',
        'شاشة' => 'scherm',
        'رام' => 'ram',
        'ذاكرة' => 'ram',
        'معالج' => 'processor',
        'تخزين' => 'ssd',
        'قرص' => 'ssd',
        'طابعة' => 'printer',
        'سماعة' => 'headset',
        'كيبورد' => 'toetsenbord',
        'لوحة' => 'moederbord',
        'كرت' => 'videokaart',
        'شاحن' => 'oplader',
        'ماوس' => 'muis',
    ];

    /**
     * Normaliseer één woord: kleine letters, alef/ة/ى/diakrieten,
     * meervoud-achtervoegsels (ات/ين/ون) eraf, ال-voorvoegsels eraf.
     */
    public static function normalize(string $word): string
    {
        $w = mb_strtolower(trim($word, " \t\n\r\0\x0B.,!?;:\"'()€"));
        $w = str_replace(['أ', 'إ', 'آ'], 'ا', $w);
        $w = str_replace('ة', 'ه', $w);
        $w = str_replace('ى', 'ي', $w);
        $w = preg_replace('/[\x{064B}-\x{0652}\x{0640}]/u', '', $w) ?? $w;

        foreach (['وال', 'بال', 'كال', 'لل', 'ال', 'و'] as $prefix) {
            if (mb_strlen($w) > mb_strlen($prefix) + 2 && str_starts_with($w, $prefix)) {
                $w = mb_substr($w, mb_strlen($prefix));
                break;
            }
        }

        // Arabisch meervoud/bezit: لابتوبات → لابتوب, موظفين → موظف,
        // تساعدني → تساعد, موقعكم → موقع
        foreach (['ات', 'ين', 'ون', 'ية', 'ني', 'كم', 'هم', 'هن', 'ها', 'نا', 'ك', 'ه'] as $suffix) {
            if (mb_strlen($w) > mb_strlen($suffix) + 2 && str_ends_with($w, $suffix)) {
                $w = mb_substr($w, 0, mb_strlen($w) - mb_strlen($suffix));
                break;
            }
        }

        return $w;
    }

    /**
     * @return array<int, string> genormaliseerde woorden (≥ $min).
     */
    public static function tokens(string $text, int $min = 3, int $max = 8): array
    {
        $out = [];
        foreach (preg_split('/\s+/u', $text) ?? [] as $w) {
            $n = self::normalize((string) $w);
            if (mb_strlen($n) >= $min) {
                $out[] = $n;
            }
        }

        return array_values(array_unique(array_slice($out, 0, $max)));
    }

    /**
     * Vertaal Arabische producttermen naar LIKE-bare NL-termen.
     *
     * @param array<int, string> $words genormaliseerde woorden
     * @return array<int, string>
     */
    public static function toDutchTerms(array $words): array
    {
        $out = [];
        foreach ($words as $w) {
            if (isset(self::$productTerms[$w])) {
                $out[] = self::$productTerms[$w];
            }
        }

        return array_values(array_unique($out));
    }
}
