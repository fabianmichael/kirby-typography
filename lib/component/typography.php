<?php

/**
 *  This file is part of Kirby-Typography. A port of wp-Typography
 *  for Kirby CMS (https://getkirby.com).
 *  
 *  Copyright of Kirby-Typography:
 *  2016 Fabian Michael.
 *
 *  Copyright of wp-Typography (included in this package):
 *	2014-2016 Peter Putzer.
 *	2012-2013 Marie Hogebrandt.
 *	2009-2011 KINGdesk, LLC.
 *
 *	This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *  ***
 *
 *  @package Kirby\Plugings\Typography
 *  @author Fabian Michael <hallo@fabianmichael.de>
 *  @license http://www.gnu.org/licenses/gpl-2.0.html
 */
 
namespace Kirby\Plugins\Typography\Component;
use Kirby\Plugins\Typography\KirbyTypography;
use Kirby\Plugins\Typography\Cache;
use L;

class Typography extends \Kirby\Component\Smartypants {
  
  public $version = '1.0.0-beta1';
  
  public function defaults() {
    return [
      'typography'                             => true,
      'typography.debug'                       => false,
      
      // general attributes
      'typography.ignore.tags'                 => ['code', 'head', 'kbd', 'object',
                                                   'option', 'pre', 'samp', 'script',
                                                   'noscript', 'noembed', 'select',
                                                   'style', 'textarea', 'title',
                                                   'var', 'math'],
      'typography.ignore.classes'              => [ 'vcard', 'typography--off' ],
      'typography.ignore.ids'                  => [],
      
      // smart characters
      'typography.quotes'                      => true,
      'typography.quotes.primary'              => 'doubleCurled',
      'typography.quotes.secondary'            => 'singleCurled', 
      
      'typography.dashes'                      => true,
      'typography.dashes.style'                => 'em', 
      'typography.dashes.spacing'              => true,
      
      'typography.diacritics'                  => false,
      'typography.diacritics.language'         => 'en-US',
      'typography.diacritics.custom'           => [],
      
      'typography.ellipses'                    => true,
      'typography.marks'                       => true,
      'typography.ordinal.suffix'              => true,
      'typography.math'                        => true,
      'typography.fractions'                   => true,
      'typography.exponents'                   => true,
      
      // smart spacing
      'typography.wordspacing.singlecharacter' => true,
      'typography.fraction.spacing'            => true,
      'typography.punctuation.spacing.french'  => false, 
      'typography.units.spacing'               => true,
      'typography.units.custom'                => [],
         
      'typography.dewidow'                     => true,
      'typography.dewidow.maxlength'           => 5,
      'typography.dewidow.maxpull'             => 5,
      
      'typography.wrap.hardhyphens'            => true,
      'typography.wrap.url'                    => true,
      'typography.wrap.email'                  => true,
      'typography.wrap.url.minafter'           => 5,
      'typography.domains.custom'              => ['local', // local network links
                                                   'onion', 'exit', 'tor', // TOR Project
                                                   'test', 'example',
                                                  ],
      
      'typography.space.collapse'              => true,
      'typography.space.nobreak.narrow'        => false,
      
      // character styling 
      'typography.style.ampersands'            => false,
      'typography.style.caps'                  => true,
      'typography.style.numbers'               => false,
      
      // hanging punctuation
      'typography.style.punctuation.hanging'   => true,
      'typography.style.quotes.initial'        => true,
      'typography.style.quotes.initial.tags'   => ['p', 'h1', 'h2', 'h3', 'h4',
                                                   'h5', 'h6', 'blockquote',
                                                   'li', 'dd', 'dt'],
      
      // hyphenation
      'typography.hyphenation'                 => true,
      'typography.hyphenation.language'        => 'en-US',
      'typography.hyphenation.minlength'       => 7,
      'typography.hyphenation.minbefore'       => 3,
      'typography.hyphenation.minafter'        => 2,
      
      'typography.hyphenation.headings'        => true,
      'typography.hyphenation.allcaps'         => true,
      'typography.hyphenation.titlecase'       => true,
      'typography.hyphenation.compounds'       => true,
      'typography.hyphenation.exceptions'      => [],
      
      // css classes
      'typography.class.ampersand'             => 'char--ampersand', 
      'typography.class.caps'                  => 'char--caps',
      'typography.class.numbers'               => 'char--numbers',
      'typography.class.fraction.numerator'    => 'frac--numerator',
      'typography.class.fraction.denominator'  => 'frac--denominator',
      
      'typography.class.quote.initial.single'  => 'char--singleQuoteInitial',
      'typography.class.quote.initial.double'  => 'char--doubleQuoteInitial',
      'typography.class.push.single'           => 'push--singleQuote',
      'typography.class.push.double'           => 'push--doubleQuote',
      'typography.class.pull.single'           => 'pull--singleQuote',
      'typography.class.pull.double'           => 'pull--doubleQuote',
      'typography.class.ordinal'               => 'char--ordinalSuffix',
    ];
  }
  
  protected $settingsSerialized;
  protected $typo;
  protected $_localized = false;
  
  
  protected function hash($text) {
    if (is_null($this->settingsSerialized)) {
      $settings = ['typography.version' => $this->version];
      foreach ($this->defaults() as $key => $val) {
        $settings[$key] = $this->kirby->options[$key];
      }  
      $this->settingsSerialized = serialize($settings);
    }
    return md5($this->settingsSerialized . $text);
  }
  
  
  public function typography() {
    if (!$this->typo) {
      $this->typo = new KirbyTypography(false);
    }
    return $this->typo;
  }
  
  protected function localize() {
    if ($this->_localized) return; // this method can only be called once
    
    if ($this->kirby->site->multilang()) {
      $language = l::get();
      foreach (array_keys($this->defaults()) as $option) {
        if (isset($language[$option])) {
          // override defaults and settings from config file, if localized setting is available
          $this->kirby->options[$option] = $language[$option];
        }
      }
    }
    
    $this->_localized = true;
  }
  
  protected function processText($text) {
    $typo = $this->typography();
    return $typo->process($text);
  }
  
  
  public function parse($text, $force = false) {
    $this->localize(); // cannot use the `configure` method to do this, because it gets executed before languages are loaded
    
    if (!$this->kirby->option('typography')) {
      return $text;
    } else {
      
      if ($this->kirby->option('typography.debug')) {
        // Skip caching when in debug mode
        return $this->processText($text);
      }
      
      $cache      = Cache::instance();
      $cacheKey   = $this->hash($text);
      $parsedText = $cache->get($cacheKey, false);
      
      if ($parsedText === false) {
        $parsedText = $this->processText($text);
        $cache->set($cacheKey, $parsedText);
      }

      return $parsedText;
    }
  }  
}
