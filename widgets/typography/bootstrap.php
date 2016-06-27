<?php

namespace Kirby\Plugins\Typography;
use Response;
use Kirby\Plugins\Typography\KirbyTypography;

// This helper function looks pretty ugly … but this is a temporary solution
// until the Kirby panel fully supports localization.
function kirby_typography_widget_get_translations( $code = null ) {
  $language_directory = __DIR__ . DS . 'languages';
  
  if ($code !== 'en') {
    if (!preg_match('/^[a-z]{2}([_-][a-z0-9]{2,})?$/i', $code) ||
        !file_exists($language_directory . DS . $code . '.php')) {
      // Set to fallback language, if not a valid code or no translation available.
      $code = 'en';
    }
  }
  
  return require($language_directory . DS . $code . '.php');
}

$kirby->set('widget', 'typography', __DIR__ );
$kirby->set('route', [
  'pattern' => 'plugins/typography/widget/api/(:any)',
  'action'  => function($action) {
    
    $user = site()->user()->current();
    if (!$user || !$user->isAdmin()) {
      return Response::error('Only administrators can access the widget API of the typography plugin.', 401);
    }
    
    $l = kirby_typography_widget_get_translations($user->language());
    
    switch ($action) {
      case 'status';
        $data = Cache::instance()->status();
        return Response::success(sprintf($l['typography.widget.cache.status'], round($data['size'] / 1024, 2), $data['files']), $data);
        break;
      
      case 'flush':
        $success = Cache::instance()->flush();
        if ($success) {
          $data = Cache::instance()->status();
          return Response::success(sprintf($l['typography.widget.cache.status'], round($data['size'] / 1024, 2), $data['files']), $data);
        } else {
          // Sending a success message here, although it is an error message,
          // but our wiget just displays the text and should not care.
          return Response::success($l['typography.widget.cache.error']);
        }
        
      default:
        return Response::error('Invalid plugin action. The action "' . html($action) . '" is not defined.');
    }
  }
]);
