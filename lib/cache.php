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
use Cache\Driver\File as FileCache;
use Dir;
use F;

class Cache {
  
  protected $cacheRoot;
  protected $driver;
  
  protected function __construct() {
    $this->cacheRoot = kirby()->roots()->cache() . DS . 'plugins' . DS . 'typography';
    
    if (!f::exists($this->cacheRoot)) {
      dir::make($this->cacheRoot);
    }
      
    $this->driver = new FileCache($this->cacheRoot);
  }
  
  public static function instance() {
    static $instance;
    return $instance ?: $instance = new self();
  }
  
  public function driver() {
    return $this->driver;
  }
  
  public function root() {
    return $this->cacheRoot;
  }
  
  public function status() {
    return [
      'size'  => dir::size($this->cacheRoot),
      'files' => sizeof(dir::read($this->cacheRoot)),
    ];
  }
  
  public function __call($name, $arguments) {
    return call_user_func_array([$this->driver, $name], $arguments);
  }
}
