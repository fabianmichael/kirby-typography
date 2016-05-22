<?php 
 
namespace Kirby\Plugins\Typography;
use PHP_Typography\PHP_Typography;

class KirbyTypography extends PHP_Typography {

  protected $domainListUrl = 'http://data.iana.org/TLD/tlds-alpha-by-domain.txt';
  protected $domainListCacheLifetime = 60 * 24 * 7; // one week
    
  protected $kirby;
  
  function __construct($set_defaults = true, $init = 'now')	{
    $this->kirby = kirby();
    
    // ignore arguments of the constructor
		parent::__construct(false, 'now');
    $o = $this->kirby->options;
    
    // general attributes
    $this->set_tags_to_ignore($o['typography.ignore.tags']);
    $this->set_classes_to_ignore($o['typography.ignore.classes']);
    $this->set_ids_to_ignore($o['typography.ignore.ids']);
    
    // smart characters
    $this->set_smart_quotes($o['typography.quotes']);
    $this->set_smart_quotes_primary($o['typography.quotes.primary']);
    $this->set_smart_quotes_secondary($o['typography.quotes.secondary']);
    
    $this->set_smart_dashes($o['typography.dashes']);
    $this->set_smart_dashes_style($o['typography.dashes.style']);
    $this->set_dash_spacing($o['typography.dashes.spacing']);
    
    $this->set_smart_diacritics($o['typography.diacritics']);
    $this->set_diacritic_language($o['typography.diacritics.language']);
    $this->set_diacritic_custom_replacements($o['typography.diacritics.custom']);
    
    $this->set_smart_ellipses($o['typography.ellipses']);
    $this->set_smart_marks($o['typography.marks']);
    $this->set_smart_ordinal_suffix($o['typography.ordinal.suffix']);
    $this->set_smart_math($o['typography.math']);
    $this->set_smart_fractions($o['typography.fractions']);
    $this->set_smart_exponents($o['typography.exponents']);
    
    // smart spacing
    $this->set_single_character_word_spacing($o['typography.wordspacing.singlecharacter']);
    $this->set_fraction_spacing($o['typography.fraction.spacing']);
    $this->set_french_punctuation_spacing($o['typography.punctuation.spacing.french']);
    $this->set_unit_spacing($o['typography.units.spacing']);        
    $this->set_units($o['typography.units.custom']);   
    
    $this->set_dewidow($o['typography.dewidow']);     
    $this->set_max_dewidow_length($o['typography.dewidow.maxlength']);
    $this->set_max_dewidow_pull($o['typography.dewidow.maxpull']);
    
    $this->set_wrap_hard_hyphens($o['typography.wrap.hardhyphens']);
    $this->set_url_wrap($o['typography.wrap.url']);
    $this->set_email_wrap($o['typography.wrap.email']);
    $this->set_min_after_url_wrap($o['typography.wrap.url.minafter']);
    
    $this->set_space_collapse($o['typography.space.collapse']);
    $this->set_true_no_break_narrow_space($o['typography.space.nobreak.narrow']);
    
    // character styling
    $this->set_style_ampersands($o['typography.style.ampersands']);
    $this->set_style_caps($o['typography.style.caps']);
    $this->set_style_numbers($o['typography.style.numbers']);
    $this->set_style_hanging_punctuation($o['typography.style.punctuation.hanging']);
    $this->set_style_initial_quotes($o['typography.style.quotes.initial']);
    $this->set_initial_quote_tags($o['typography.style.quotes.initial.tags']);
    
    // hyphenation
    $this->set_hyphenation($o['typography.hyphenation']);
    $this->set_hyphenation_language($o['typography.hyphenation.language']);
    $this->set_min_length_hyphenation($o['typography.hyphenation.minlength']);
    $this->set_min_before_hyphenation($o['typography.hyphenation.minbefore']);
    $this->set_min_after_hyphenation($o['typography.hyphenation.minafter']);
    $this->set_hyphenate_headings($o['typography.hyphenation.headings']);
    $this->set_hyphenate_all_caps($o['typography.hyphenation.allcaps']);
    $this->set_hyphenate_title_case($o['typography.hyphenation.titlecase']);
    $this->set_hyphenate_compounds($o['typography.hyphenation.compounds']);
    $this->set_hyphenation_exceptions($o['typography.hyphenation.exceptions']);

    $this->initialize_components();
    $this->initialize_patterns();
        
	}
  
  function get_top_level_domains_from_file( $path ) {
    
    $cache                    = Cache::instance();
    $cacheKey                 = 'domain-list-' . md5($this->domainListUrl);
    $cacheKeyLastAttempt      = 'domain-list-last-attempt';
    $cacheKeyMaxFetchInterval = 30;
    
    $domains = $cache->get($cacheKey);
    
    if (!$domains) {
      $lastAttempt = $cache->get($cacheKeyLastAttempt, false);
      
      if (!$lastAttempt || $lastAttempt !== $this->domainListUrl) { // only retry, if last attempt to fetch the domain list failed.
        
        if ($domainListTextFileContents = @file_get_contents($this->domainListUrl)) {
          $domains = [];
          
          foreach (explode("\n", $domainListTextFileContents) as $line) {
            if ( preg_match('#^[a-zA-Z0-9][a-zA-Z0-9-]*$#', $line, $matches ) ) {
              $domains[] = strtolower( $matches[0] );
            }
          }
          
          if (sizeof($domains) > 0) {
            $domains = implode('|', $domains);
            $cache->set($cacheKey, $domains, $this->domainListCacheLifetime);
          } else {
            $domains = null;
          }
        }
      }
    }
    
    if (!$domains) {
      // Fallback to local copy of IANA domains
      
      //$domains = 'ac|ad|aero|ae|af|ag|ai|al|am|an|ao|aq|arpa|ar|asia|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|biz|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|cat|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|com|coop|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|info|int|in|io|iq|ir|is|it|je|jm|jobs|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mobi|mo|mp|mq|mr|ms|mt|museum|mu|mv|mw|mx|my|mz|name|na|nc|net|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pro|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tel|tf|tg|th|tj|tk|tl|tm|tn|to|tp|travel|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw';
      $cache->set($cacheKeyLastAttempt, $this->domainListUrl, $cacheKeyMaxFetchInterval);
      $domains = [];
      $domainListTextFileContents = file_get_contents(TYPOGRAPHY_PLUGIN_BASE_DIR . DS . 'vendors' . DS . 'iana' . DS . 'tlds-alpha-by-domain.txt');
      foreach (explode("\n", $domainListTextFileContents) as $line) {
        if ( preg_match('#^[a-zA-Z0-9][a-zA-Z0-9-]*$#', $line, $matches ) ) {
          $domains[] = strtolower( $matches[0] );
        }
      }
      $domains = implode('|', $domains);
    }
    
    if (is_array($this->kirby->options['typography.domains.custom']) &&
        sizeof($this->kirby->options['typography.domains.custom']) > 0) {
          
      $domains .= '|' . implode('|', $this->kirby->options['typography.domains.custom']);
    }
    
    return $domains;
  }  
  
  protected function getClassAttr($str = '') {
    return (!empty($str) ? ' class="' . $str . '"' : '');
  }
  
  /* =-=-=-=-=  Overridden styling methods  =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */
  
  function style_caps( \DOMText $textnode ) {
    if (empty($this->settings['styleCaps'])) return;
    $textnode->data = preg_replace($this->regex['styleCaps'], '<span class="' . $this->kirby->options['typography.class.caps'] . '">$1</span>', $textnode->data);
  }
  
  function style_ampersands( \DOMText $textnode ) {
    if (empty( $this->settings['styleAmpersands'])) return;
    $textnode->data = preg_replace($this->regex['styleAmpersands'], '<span class="' . $this->kirby->options['typography.class.ampersand'] . '">$1</span>', $textnode->data);
  }
  
  function style_numbers( \DOMText $textnode ) {
    if (empty($this->settings['styleNumbers'])) return;
    $textnode->data = preg_replace($this->regex['styleNumbers'], '<span class="' . $this->kirby->options['typography.class.numbers'] . '">$1</span>', $textnode->data);
  }
  
  function style_initial_quotes( \DOMText $textnode, $is_title = false ) {
    if ( empty( $this->settings['styleInitialQuotes'] ) || empty( $this->settings['initialQuoteTags'] ) ) {
      return;
    }

    if ( '' === $this->get_prev_chr( $textnode )) { // we have the first text in a block level element

      $func = $this->str_functions[ mb_detect_encoding( $textnode->data, $this->encodings, true ) ];
      $first_character = $func['substr']( $textnode->data, 0, 1 );

      switch ( $first_character ) {
        case "'":
        case $this->chr['singleQuoteOpen']:
        case $this->chr['singleLow9Quote']:
        case ',':
        case '"':
        case $this->chr['doubleQuoteOpen']:
        case $this->chr['guillemetOpen']:
        case $this->chr['guillemetClose']:
        case $this->chr['doubleLow9Quote']:

          $block_level_parent = $this->get_block_parent( $textnode );
          $block_level_parent = isset( $block_level_parent->tagName ) ? $block_level_parent->tagName : false;

          if ( $is_title ) {
            // assume page title is h2
            $block_level_parent = 'h2';
          }

          if ( $block_level_parent && isset( $this->settings['initialQuoteTags'][ $block_level_parent ] ) ) {
            switch( $first_character ) {
              case "'":
              case $this->chr['singleQuoteOpen']:
              case $this->chr['singleLow9Quote']:
              case ",":
                $span_class = $this->kirby->options['typography.class.quote.initial.single'];
                break;

              default: // double quotes or guillemets
                $span_class = $this->kirby->options['typography.class.quote.initial.double'];
            }

            $textnode->data =  '<span class="' . $span_class . '">' . $first_character . '</span>' . $func['substr']( $textnode->data, 1, $func['strlen']( $textnode->data ) );
          }
      }
    }
  }
  
  function style_hanging_punctuation( \DOMText $textnode ) {
    if ( empty( $this->settings['styleHangingPunctuation'] ) ) {
      return;
    }

    // we need the parent
    $block = $this->get_block_parent( $textnode );
    $firstnode = ! empty( $block ) ? $this->get_first_textnode( $block ) : null;

    // need to get context of adjacent characters outside adjacent inline tags or HTML comment
    // if we have adjacent characters add them to the text
    $next_character = $this->get_next_chr( $textnode );
    if ( '' !== $next_character ) {
      $textnode->data =  $textnode->data.$next_character;
    }

    // echo '<pre>', $this->regex['styleHangingPunctuationDouble'] . '</pre>';

    $textnode->data = preg_replace( $this->regex['styleHangingPunctuationDouble'], '$1<span class="' . $this->kirby->options['typography.class.push.double'] . '"></span>' . $this->chr['zeroWidthSpace'] . '<span class="' . $this->kirby->options['typography.class.pull.double'] . '">$2</span>$3', $textnode->data );
    $textnode->data = preg_replace( $this->regex['styleHangingPunctuationSingle'], '$1<span class="' . $this->kirby->options['typography.class.push.single'] . '"></span>' . $this->chr['zeroWidthSpace'] . '<span class="' . $this->kirby->options['typography.class.pull.single'] . '">$2</span>$3', $textnode->data );

    if ( empty( $block ) || $firstnode === $textnode ) {
      $textnode->data = preg_replace( $this->regex['styleHangingPunctuationInitialDouble'], '<span class="' . $this->kirby->options['typography.class.pull.double'] . '">$1</span>$2', $textnode->data );
      $textnode->data = preg_replace( $this->regex['styleHangingPunctuationInitialSingle'], '<span class="' . $this->kirby->options['typography.class.pull.single'] . '">$1</span>$2', $textnode->data );
    } else {
      $textnode->data = preg_replace( $this->regex['styleHangingPunctuationInitialDouble'], '<span class="' . $this->kirby->options['typography.class.push.double'] . '"></span>' . $this->chr['zeroWidthSpace'] . '<span class="' . $this->kirby->options['typography.class.pull.double'] . '">$1</span>$2', $textnode->data );
      $textnode->data = preg_replace( $this->regex['styleHangingPunctuationInitialSingle'], '<span class="' . $this->kirby->options['typography.class.push.single'] . '"></span>' . $this->chr['zeroWidthSpace'] . '<span class="' . $this->kirby->options['typography.class.pull.single'] . '">$1</span>$2', $textnode->data );
    }

    // remove any added characters;
    if ( '' !== $next_character ) {
      $func = $this->str_functions[ mb_detect_encoding( $textnode->data, $this->encodings, true ) ];
      $textnode->data = $func['substr']( $textnode->data, 0, $func['strlen']( $textnode->data ) - 1 );
    }
  }
  
  function smart_ordinal_suffix(\DOMText $textnode) {
    if (empty( $this->settings['smartOrdinalSuffix'])) return;
    
    $classAttr = $this->getClassAttr($this->kirby->options['typography.class.ordinal.suffix']);    
    $textnode->data = preg_replace( $this->regex['smartOrdinalSuffix'], '$1'.'<sup' . $classAttr . '>$2</sup>', $textnode->data );
  }
  
  function smart_fractions(\DOMText $textnode) {
    if ( empty( $this->settings['smartFractions'] ) && empty( $this->settings['fractionSpacing'] ) ) {
      return;
    }

    if ( ! empty( $this->settings['fractionSpacing'] ) && ! empty( $this->settings['smartFractions'] ) ) {
      $textnode->data = preg_replace( $this->regex['smartFractionsSpacing'], '$1'.$this->chr['noBreakNarrowSpace'].'$2', $textnode->data );
    } elseif ( ! empty( $this->settings['fractionSpacing'] ) && empty( $this->settings['smartFractions'] ) ) {
      $textnode->data = preg_replace( $this->regex['smartFractionsSpacing'], '$1'.$this->chr['noBreakSpace'].'$2', $textnode->data );
    }

    if ( !empty($this->settings['smartFractions']) ) {
      // Escape sequences we don't want fractionified
      $textnode->data = preg_replace( $this->regex['smartFractionsEscapeYYYY/YYYY'], '$1_E_S_C_A_P_E_D_$2$3$4', $textnode->data );
      $textnode->data = preg_replace( $this->regex['smartFractionsEscapeMM/YYYY'], '$1_E_S_C_A_P_E_D_$2$3$4', $textnode->data );

      // Replace fractions
      $nominatorClassAttr   = $this->getClassAttr($this->kirby->options['typography.class.fraction.nominator']);
      $denominatorClassAttr = $this->getClassAttr($this->kirby->options['typography.class.fraction.denominator']);
      
      $textnode->data = preg_replace( $this->regex['smartFractionsReplacement'], '<sup' . $nominatorClassAttr . '>$1</sup>'.$this->chr['fractionSlash'].'<sub' . $denominatorClassAttr . '>$2</sub>$3', $textnode->data );

      // Unescape escaped sequences
      $textnode->data = str_replace( '_E_S_C_A_P_E_D_', '', $textnode->data );
    }
  }
  
}
