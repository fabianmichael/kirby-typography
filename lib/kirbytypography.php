<?php 
 
namespace Kirby\Plugins\Typography;
use PHP_Typography\PHP_Typography;

class KirbyTypography extends PHP_Typography {

  protected $domainListUrl = 'http://data.iana.org/TLD/tlds-alpha-by-domain.txt';
  protected $domainListCacheLifetime = 10080; // one week in minutes
  
  protected $dashStylesMap = [
    'en' => 'international',
    'em' => 'traditionalUS',
  ];
  
  private $cssClassKeys = [
    'typography.class.caps'                 => 'caps', 
    'typography.class.numbers'              => 'numbers',
    'typography.class.ampersand'            => 'amp', 
    'typography.class.quote.initial.single' => 'quo', 
    'typography.class.quote.initial.double' => 'dquo', 
    'typography.class.push.single'          => 'push-single',
    'typography.class.push.double'          => 'push-double',
    'typography.class.pull.single'          => 'pull-single',
    'typography.class.pull.double'          => 'pull-double',
    'typography.class.fraction.numerator'   => 'numerator', 
    'typography.class.fraction.denominator' => 'denominator',
    'typography.class.ordinal'              => 'ordinal',
  ];
    
  protected $kirby;
  
  function __construct($set_defaults = true, $init = 'now')	{
    $this->kirby = kirby();
    
    // ignore arguments of the constructor
		parent::__construct(true);
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
    
    // set css classes
    foreach($this->cssClassKeys as $optionKey => $classKey) {
      $this->css_classes[$classKey] = $o[$optionKey];
    }
	}
  
  /**
   * Sets the typographical conventions used by smart_dashes.
   *
   * Allowed values for $style:
   * - "em" (alias: "traditionalUS")
   * - "en" (alias: "international")
   *
   * @param string $style Optional. Default "en".
   */
  public function set_smart_dashes_style( $style = 'en' ) {
    if (isset($this->dashStylesMap[$style])) {
      // Translate dash styles for PHP-Typography
      $style = $this->dashStylesMap[$style];
    }
    
    return parent::set_smart_dashes_style($style);
  }
  
  /**
	 * Try to fetch a list of top-level domains from the IANA. If fetching of that
   * file fails, load a list of top-level domains from a file.
	 *
	 * @param string $path The full path and filename.
	 * @return string A list of top-level domains concatenated with '|'.
	 */
  function get_top_level_domains_from_file($path) {
    
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
      $cache->set($cacheKeyLastAttempt, $this->domainListUrl, $cacheKeyMaxFetchInterval);
      // Fallback to local copy of IANA domains
      $domains = parent::get_top_level_domains_from_file($path);
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
    
}
