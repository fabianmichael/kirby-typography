# Kirby-Typography

This is a port of [wp-Typography](https://de.wordpress.org/plugins/wp-typography/) for Kirby CMS. Based on the `PHP_Typography` class, this plugin enhances the typography of you kirby-powered website. Think of it a more advanced alternative to the built-in `SmartyPants` parser.

Current version: `1.0.0-alpha`

## Key Features

-	**Hyphenation:** Hyphenate text, very handy for justified text or languages with very long words, but also for English. Supports a large number of languages and offers fine-grained control over hyphenation.
-	**Smart Replacements:** Includes Smart quotes (i.e. „Curly Quotes“, «Guillemets», »Chevrons« or German „Gänsefüßchen“) and smart ordinal suffixes.
-	**CSS Hooks:** Add `<span class="[…]">` tags around characters like ampersands, CAPITALIZED WORDS etc.
-	**Hanging Punctuation:** Add the twinkle of the print-era to your site.
-	**Wraps URLs:** Prevents long URLs from overflowing their container element on small screens.
-	**Smart Math:** Converts mathematical symbols into their correct counterpart. E.g. `(2x6)/3=4` becomes `(2×6)÷3=4`.
-	**Caching:** Processing text like this plugin does is a very performance-hungry task. So the results are always cached, even if Kirby’s cache option is turned off. Cache will automatically be updated, if you change your content or update the plugin. It also comes with a *Dashboard Widget*, so you can flush the cache from your panel.

… and basically every other feature of wp-Typography :-)

## Download & Installation

### Requirements

-	PHP 5.4.0+ with multibyte extension (mbstring) enabled
-	Kirby 2.3.0+

### Git Submodule

To install this plugin as a git submodule, execute the following command from the root of your kirby project:

```
$ git submodule add https://github.com/fabianmichael/kirby-typography.git site/plugins/typography
```

### Copy & Paste

1. Download the contents of this repository as ZIP-file.
2. Rename the downloaded folder to `typography` and copy it into the `site/plugins/` directory in your Kirby project.

## Usage

The plugin is enabled by default and replaces the [SmartyPants](https://michelf.ca/projects/php-smartypants/) parser of your Kirby installation. That means, typographic enhancements are applied to *Kirbytext* by default.

I tried to setup *sane* defaults, but you might have different requirements for your site. You can change any of the plugin’s settings in your `config.php` file.

### CSS & JavaScript Setup

**CSS:** Not all features of the plugin need extra CSS rules, but for stuff like hanging punctuation, and character styling to work properly, it is recommended or even necessary to add the plugin’s CSS file to your site.
Add `<?= css('assets/plugins/typography/css/typography.css') ?>` to your template or – even better – copy the rules to your own stylesheet. Note, that some features like hanging punctuation should be tuned in accordance to the font you have chosen.

**JavaScript:** Hyphenation works by inserting invisible so-called »shy hyphens« (`&shy;`) into works, telling the browser where a word may be hyphenated. Unfortunately, these invisible characters stay in the text, when it is copied from your site. To keep copied text easily editable, it is recommended (though not mandatory) to also include the supplied JavaScript file to your code. Just add `<?= js('assets/plugins/typography/js/dist/copyfix.min.js') ?>` into to your template.

## Configuration

You can find a description of all available configuration options below. Customization works just like with any other configuration of Kirby. Your can read more about configuration in the [Kirby docs](https://getkirby.com/docs/developer-guide/configuration/options).

### Localization for Multilingual Sites

If your Kirby installation is configured for multiple languages, you might want to use different settings for every language. In this case, you can define a common baseline of settings – shared by all languages – in your config file. Use the language files in `site/languages/` to define localized versions of your settings. Note, that in the language files, settings need to be configured with `l::set()`, because those localizations are treated as language variables:

```php
# site/config/config.php
c::set('typography.class.ampersand', 'ch-amp'); // CSS classes are most likely shared across languages
```

```php
# site/languages/en.php
l::set('typography.quotes.primary', 'doubleCurled'); // Quote styles are language-specific …
l::set('typography.quotes.secondary', 'singleCurled');
l::set('typography.hyphenation.language', 'en-US'); // … and so are hyphenation rules

# site/languages/de.php
l::set('typography.quotes.primary', 'doubleLow9');
l::set('typography.quotes.secondary', 'singleLow9');
l::set('typography.hyphenation.language', 'de-DE');
```

### General Options

| Option                    | Default value                                                                                                                                        | Description                                                    |
|:--------------------------|:-----------------------------------------------------------------------------------------------------------------------------------------------------|:---------------------------------------------------------------|
| typography                | `true`                                                                                                                                               | Set to `false` to disable plugin functionality.                |
| typography.widget         | `true`                                                                                                                                               | Set to `false` to disable automatic parsing of your Kirbytext. |
| typography.ignore.tags    | `['code', 'head', 'kbd', 'object', 'option', 'pre', 'samp', 'script', 'noscript', 'noembed', 'select', 'style', 'textarea', 'title', 'var', 'math']` | Defines elements to be ignored by their tag name.              |
| typography.ignore.classes | `['vcard', 'typography--off']`                                                                                                                       | Defines elements to be ignored by their class.                 |
| typography.ignore.ids     | `[]`                                                                                                                                                 | Defines elements to be ignored by their ID.                    |

### Smart Quotes

| Option                      | Default value                | Description                                                                |
|:----------------------------|:-----------------------------|:---------------------------------------------------------------------------|
| typography.quotes           | `true`                       | Transform straight quotes [ \' \" ] to typographically correct characters. |
| typography.quotes.primary   | `'doubleGuillemetsReversed'` | Primary quotes style.                                                      |
| typography.quotes.secondary | `'singleGuillemetsReversed'` | Secondary quotes style (for nested quotations).                            |

**Available Styles for Smart Quotes:**  

| Option value                              | Example                       |
|:------------------------------------------|:------------------------------|
| `'doubleCurled'`                          | &ldquo;foo&rdquo;             |
| `'doubleCurledReversed'`                  | &rdquo;foo&rdquo;             |
| `'doubleLow9'`                            | &bdquo;foo&rdquo;             |
| `'doubleLow9Reversed'`                    | &bdquo;foo&ldquo;             |
| `'singleCurled'`                          | &lsquo;foo&rsquo;             |
| `'singleCurledReversed'`                  | &rsquo;foo&rsquo;             |
| `'singleLow9'`                            | &sbquo;foo&rsquo;             |
| `'singleLow9Reversed'`                    | &sbquo;foo&lsquo;             |
| `'doubleGuillemetsFrench'`                | &laquo;&nbsp;foo&nbsp;&raquo; |
| `'doubleGuillemets'`                      | &laquo;foo&raquo;             |
| `'doubleGuillemetsReversed'` *(default)†* | &raquo;foo&laquo;             |
| `'singleGuillemets'`                      | &lsaquo;foo&rsaquo;           |
| `'singleGuillemetsReversed'` *(default)†* | &rsaquo;foo&lsaquo;           |
| `'cornerBrackets'`                        | &#x300c;foo&#x300d;           |
| `'whiteCornerBracket'`                    | &#x300e;foo&#x300f;           |

†) Default style was chosen based on my personal preference. :-P

### Smart Character Replacements

| Option                         | Default value     | Description                                                                                                                                                                                                                                                                              |
|:-------------------------------|:------------------|:-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| typography.dashes              | `true`            | Transform minus-hyphens [ \- \-\- ] to contextually appropriate dashes, minus signs, and hyphens [ &ndash; &mdash; &#8722; &#8208; ].                                                                                                                                                    |
| typography.dashes.style        | `'international'` | Sets the style of smart dashes. Alternative value: `'traditionalUS'`.\*                                                                                                                                                                                                                  |
| typography.dashes.spacing      | `true`            | Force thin spaces between em &amp; en dashes and adjoining words.                                                                                                                                                                                                                        |
| typography.diacritics          | `true`            | Force diacritics where appropriate *(i.e. creme brulee becomes crème brûlée)*.                                                                                                                                                                                                           |
| typography.diacritics.language | `en-US`           | Language for diacritic replacements. Language definitions will purposefully **not** process words that have alternate meaning without diacritics like *resume* & *résumé*, *divorce* & *divorcé*, and *expose* & *exposé*. Currently available options are only `'en-US'` and `'de-DE'`. |
| typography.diacritics.custom   | `[]`              | Custom diacritic word replacements: Takes an associative array with the searches as keys and the replacements as values. Note, that this is case-sensitive.                                                                                                                              |
| typography.ellipses            | `true`            | Transform three periods [ \.\.\. ] to ellipses [ … ].                                                                                                                                                                                                                                    |
| typography.marks               | `true`            | Transform registration marks [ (c) (r) (tm) (sm) (p) ] to proper characters [ © ® ™ ℠ ℗ ].                                                                                                                                                                                               |
| typography.ordinal.suffix      | `true`            | Transform ordinal suffixes [ 1st ] to pretty ordinals [ 1<sup>st</sup> ].                                                                                                                                                                                                                |
| typography.math                | `true`            | Transforms math symbols [ (2x6)/3=4 ] to correct symbols [ (2&#215;6)&#247;3=4 ].                                                                                                                                                                                                        |
| typography.fractions           | `true`            | Transform fractions [ 1/2 ] to pretty fractions [ <sup>1</sup>&#8260;<sub>2</sub>. ]                                                                                                                                                                                                     |
| typography.exponents           | `true`            | Transform exponents [ 3\^2 ] to pretty exponents [ 3<sup>2</sup> ].                                                                                                                                                                                                                      |

\*) In the US, the em dash&#8202;&mdash;&#8202;with no or very little spacing&#8202;&mdash;&#8202;is used for parenthetical expressions, while internationally, the en dash &ndash; with spaces &ndash; is more prevalent.

### Smart Spacing

| Option                                 | Default value                                          | Description |
|:---------------------------------------|:-------------------------------------------------------|:------------|
| typography.wordspacing.singlecharacter | `true`                                                 | Enable forcing single character words to next line with the insertion of `&nbsp;` (no-break space). |
| typography.fraction.spacing            | `true`                                                 | Inserts a no-break space into fractions to prevent wrapping [ 5&nbsp;3/4 ]. |
| typography.punctuation.spacing.french  | `false`                                                | Enable extra whitespace before certain punctuation marks, as is the French custom. |
| typography.units.spacing               | `true`                                                 | Keep values and units together [ 12&nbsp;cm ]. |
| typography.units.custom                | `[]`                                                   | Additional units to match (Kirby-Typography already recognizes a with a large amount of different units). |
| typography.dewidow                     | `true`                                                 | Prevent widows. |
| typography.dewidow.maxlength           | `5`                                                    | Only protect widows with `x` or fewer letters. |
| typography.dewidow.maxpull             | `5`                                                    | Pull at most `x` letters from the previous line to keep the widow company. |
| typography.wrap.hardhyphens            | `true`                                                 | Enable wrapping after hard hyphens. Adds zero-width spaces after hard hyphens (like in &ldquo;zero-width&rdquo;). |
| typography.wrap.url                    | `true`                                                 | Enable wrapping of long URLs. Adds zero-width spaces throughout the URL. |
| typography.wrap.email                  | `true`                                                 | Enable wrapping of long emails. Adds zero-width spaces throughout the email. |
| typography.wrap.url.minafter           | `5`                                                    | Keep at least the last `x` characters of a URL together. |
| typography.domains.custom              | `['local', 'onion', 'exit', 'tor', 'test', 'example']` | The parser looks for domains in your content, based on a list of all valid Top-Level-Domains (updated and cached automatically once a week). Use this option to define additional domains, the parser should look for. |
| typography.space.collapse              | `true`                                                 | Remove superfluous whitespace from your content. |
| typography.space.nobreak.narrow        | `false`                                                | Enable to use true no-break narrow space instead of regular no-break space (`&nbsp;`). Can cause problems in some browsers. |

### Character Styling & Hanging Punctuation

The following options add `<span>` tags around certain parts of your content to create *CSS hooks*, allowing you to style specific parts of your text.

| Option                               | Default value                                                               | Description                                                                         |
|:-------------------------------------|:----------------------------------------------------------------------------|:------------------------------------------------------------------------------------|
| typography.style.ampersands          | `false`                                                                     | Wrap ampersands [ & ] with a `<span>` tag.                                          |
| typography.style.caps                | `true`                                                                      | Wrap words in ALL CAPS with a `<span>` tag.                                         |
| typography.style.numbers             | `false`                                                                     | Wrap numbers with a `<span>` tag.                                                   |
| typography.style.punctuation.hanging | `true`                                                                      | Enable hanging punctuation. Also see the included stylesheet to see how this works. |
| typography.style.quotes.initial      | `true`                                                                      | Wrap initial quotes with a `<span>` tag.                                            |
| typography.style.quotes.initial.tags | `['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'li', 'dd', 'dt']` | Only style initial quotes within these tags.                                        |

### Hyphenation

| Option                            | Default value | Description                                                                                                                                                                  |
|:----------------------------------|:--------------|:-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| typography.hyphenation            | `true`        | Enables hyphenation.                                                                                                                                                         |
| typography.hyphenation.language   | `'en-US'`     | Sets the language for hyphenation rules.\*\*                                                                                                                                 |
| typography.hyphenation.minlength  | `5`           | Do not hyphenate words with less than `x` letters.                                                                                                                           |
| typography.hyphenation.minbefore  | `3`           | Number of letters to keep before hyphenation.                                                                                                                                |
| typography.hyphenation.minafter   | `2`           | Number of letters to keep after hyphenation.                                                                                                                                 |
| typography.hyphenation.headings   | `true`        | Disabling this option will disallow hyphenation of headings, even if allowed in the general scope.                                                                           |
| typography.hyphenation.allcaps    | `true`        | Hyphenate words in ALL CAPS.                                                                                                                                                 |
| typography.hyphenation.titlecase  | `true`        | Allow hyphenation of words that begin with a capital letter. Disable to avoid hyphenation of proper nouns.                                                                   |
| typography.hyphenation.compounds  | `true`        | Allow hyphenation of the components of hyphenated compound words. Disable to disallow the hyphenation of the words making up a hyphenated compound *(e.g. editor-in-chief)*. |
| typography.hyphenation.exceptions | `[]`          | Exception List (array): Mark allowed hyphenations with "-" (e.g. typo-graphy).                                                                                                                  |

\*\*) Hyphenation rules are available for the following languages: `af`, `hy`, `eu`, `bg`, `ca`, `zh-Latn`, `hr`, `cs`, `da`, `nl`, `en-GB`, `en-US`, `et`, `fi`, `fr`, `gl`, `ka`, `de`, `de-1901`, `grc`, `el-Mono`, `el-Poly`, `hu`, `is`, `id`, `ia`, `ga`, `it`, `la`, `la-classic`, `lv`, `lt`, `mn-Cyrl`, `no`, `pl`, `pt`, `ro`, `ru`, `sa`, `sr-Cyrl`, `sh-Cyrl`, `sh-Latn`, `sk`, `sl`, `es`, `sv`, `th`, `tr`, `tk`, `uk`, `cy`

### CSS Classes

Some of the options above add `<span>` tags around single characters or sequences of characters, so you hook into their presentation via good ol’ CSS. If you don’t like the rather long class names I have chosen as default, feel free to change ’em to whatever you like by settings the options below:

| Option                                | Default value                |
|:--------------------------------------|:-----------------------------|
| typography.class.ampersand            | `'char--ampersand'`          |
| typography.class.caps                 | `'char--caps'`               |
| typography.class.numbers              | `'char--numbers'`            |
| typography.class.fraction.nominator   | `'frac--nominator'`          |
| typography.class.fraction.denominator | `'frac--denominator'`        |
| typography.class.quote.initial.single | `'char--singleQuoteInitial'` |
| typography.class.quote.initial.double | `'char--doubleQuoteInitial'` |
| typography.class.push.single          | `'push--singleQuote'`        |
| typography.class.push.double          | `'push--doubleQuote'`        |
| typography.class.pull.single          | `'pull--singleQuote'`        |
| typography.class.pull.double          | `'pull--doubleQuote'`        |
| typography.class.ordinal.suffix       | `'char--ordinalSuffix'`      |

## License

Kirby-Typography is released under the GNU General Public License 3.0 or later. See `LICENSE` file for details (FYI: This is mandatory, because wp-Typography is also released under GPL. I would have preferred the MIT license).

## Credits

**Kirby-Typography** is developed and maintained by [Fabian Michael](https://fabianmichael.de).

The plugin includes/is based the following third-party libraries:

-	**PHP-Typography:** Copyright 2014-2016 Peter Putzer; 2012-2013 Marie Hogebrandt; 2009-2011 KINGdesk, LLC. Released under the [GPL 2.0](http://www.gnu.org/licenses/gpl-2.0.html) or later. Large parts of this documentation have also been copied and/or adapted from the original WordPress plugin.
-	**HTML5-PHP:** Released under the *HTML5Lib License* (see `vendors/masterminds/HTML5/LICENSE` for details and contributors)
-	**A List of all Top-Level-Domains**, maintained by the [Internet Assigned Numbers Authority (IANA)](http://www.iana.org) (automatically updated once a week)
- **JavaScript** (copyfix.js) based on [Hyphenator](https://github.com/mnater/Hyphenator) 5.2.0(devel). Copyright (C) 2015 by Mathias Nater. Originally Released under the MIT license.
