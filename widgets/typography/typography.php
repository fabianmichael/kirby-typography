<?php 

namespace Kirby\Plugins\Typography;
use Tpl;

$l = kirby_typography_widget_get_translations(panel()->translation()->code());

return [
  'title' => [
    'text' => $l['typography.widget.title'],
  ],
  
  'options' => [
    [
      'text' => $l['typography.widget.action.flush'],
      'icon' => 'trash',
      'link' => kirby()->urls()->index() . '/plugins/typography/widget/api/flush',
    ],
  ],
  'html' => function() use ($l) {
    return tpl::load(__DIR__ . DS . 'typography.html.php', [ 'l' => $l ]);
  }  
];
