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

namespace Kirby\Plugins\Typography;
use C;

define('TYPOGRAPHY_PLUGIN_BASE_DIR', __DIR__);

load([
  // TypeSetter classes
  'kirby\\plugins\\typography\\kirbytypography'        => 'lib' . DS . 'kirbytypography.php',
  'kirby\\plugins\\typography\\cache'                  => 'lib' . DS . 'cache.php',
  'kirby\\plugins\\typography\\component\\typography'  => 'lib' . DS . 'component' . DS . 'typography.php',
      
  // WP Typography classes    
  'php_typography\\parse_text'                         => 'php-typography' . DS . 'class-parse-text.php',
  'php_typography\\php_typography'                     => 'php-typography' . DS . 'class-php-typography.php',
    
], TYPOGRAPHY_PLUGIN_BASE_DIR);

// Register plugin component
$kirby->set('component', 'smartypants', 'kirby\\plugins\\typography\\component\\typography');

// Register dashboard widget if not disabled in config file
if ( c::get('typography.widget') === null ) {
  c::set('typography.widget', true); // enable by default, if not explicitly set to `false`.
  $kirby->options['typography.widget'] = true;
}

if ($kirby->option('typography.widget') !== false) {
  require_once __DIR__ . DS . 'widgets' . DS . 'typography' . DS . 'bootstrap.php';
}
