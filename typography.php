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
use Response;

define('TYPOGRAPHY_PLUGIN_BASE_DIR', __DIR__);

load([
  // TypeSetter classes
  'kirby\\plugins\\typography\\kirbytypography'        => 'lib' . DS . 'kirbytypography.php',
  'kirby\\plugins\\typography\\cache'                  => 'lib' . DS . 'cache.php',
  'kirby\\plugins\\typography\\component\\typography'  => 'lib' . DS . 'component' . DS . 'typography.php',
  
  // HTML5 Parser
  'masterminds\\html5'                                 => 'vendors' . DS . 'masterminds' . DS . 'HTML5.php',
  'masterminds\\html5\\elements'                       => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Elements.php',
  'masterminds\\html5\\entities'                       => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Entities.php',
  'masterminds\\html5\\exception'                      => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Exception.php',
  'masterminds\\html5\\instructionprocessor'           => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'InstructionProcessor.php',
  'masterminds\\html5\\parser\\characterreference'     => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'CharacterReference.php',
  'masterminds\\html5\\parser\\domtreebuilder'         => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'DOMTreeBuilder.php',
  'masterminds\\html5\\parser\\eventhandler'           => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'EventHandler.php',
  'masterminds\\html5\\parser\\fileinputstream'        => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'FileInputStream.php',
  'masterminds\\html5\\parser\\inputstream'            => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'InputStream.php',
  'masterminds\\html5\\parser\\parseerror'             => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'ParseError.php',
  'masterminds\\html5\\parser\\scanner'                => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'Scanner.php',
  'masterminds\\html5\\parser\\stringinputstream'      => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'StringInputStream.php',
  'masterminds\\html5\\parser\\tokenizer'              => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'Tokenizer.php',
  'masterminds\\html5\\parser\\treebuildingrules'      => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'TreeBuildingRules.php',
  'masterminds\\html5\\parser\\utf8utils'              => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Parser' . DS . 'UTF8Utils.php',
  'masterminds\\html5\\serializer\\html5entities'      => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Serializer' . DS . 'HTML5Entities.php',
  'masterminds\\html5\\serializer\\outputrules'        => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Serializer' . DS . 'OutputRules.php',
  'masterminds\\html5\\serializer\\rulesinterface'     => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Serializer' . DS . 'RulesInterface.php',
  'masterminds\\html5\\serializer\\traverser'          => 'vendors' . DS . 'masterminds' . DS . 'HTML5' . DS . 'Serializer' . DS . 'Traverser.php',
      
  // WP Typography classes    
  'php_typography\\parse_text'                         => 'vendors' . DS . 'php-typography' . DS . 'class-parse-text.php',
  'php_typography\\php_typography'                     => 'vendors' . DS . 'php-typography' . DS . 'class-php-typography.php',
    
], TYPOGRAPHY_PLUGIN_BASE_DIR);

// Register plugin component
$kirby->set('component', 'smartypants', 'kirby\\plugins\\typography\\component\\typography');

// Register dashboard widget if not disabled in config file
if ( c::get('typography.widget') === null ) {
  c::set('typography.widget', true); // enable by default, if not explicitly set to `false`.
}

if (c::get('typography.widget') !== false) {
  $kirby->set('widget', 'typography', TYPOGRAPHY_PLUGIN_BASE_DIR . DS . 'widgets'  .DS . 'typography');
  $kirby->set('route', [
    'pattern' => 'plugins/typography/cache/(:any)',
    'action'  => function($action) {
      
      $user = site()->user();
      if (!$user || !$user->isAdmin()) {
        return Response::error('Only administrators can access the widget API of the typography plugin.', 401);
      }
      
      switch ($action) {
        case 'status';
          $data = Cache::instance()->status();
          return Response::success(sprintf('Total cache size: <b>%s kB</b><br>Cache Files: <b>%s</b>', round($data['size'] / 1024, 2), $data['files']), $data);
          break;
        
        case 'flush':
          $success = Cache::instance()->flush();
          if ($success) {
            $data = Cache::instance()->status();
            return Response::success(sprintf('Total cache size: <b>%s kB</b><br>Cache Files: <b>%s</b>', round($data['size'] / 1024, 2), $data['files']), $data);
          } else {
            return Response::success('Could not clear cache. This is likely related to file permission issues.');
          }
          
        default:
          return Response::error('Invalid plugin action. The action "' . html($action) . '" is not defined.');
      }
    }
  ]);
}
