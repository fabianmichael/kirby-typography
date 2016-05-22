<?php 

return array(
  'title' => [
    'text' => 'Kirby-Typography',
  ],
  
  'options' => [
    [
      'text' => 'Flush cache',
      'icon' => 'trash',
      'link' => kirby()->urls()->index() . '/plugins/typography/cache/clean'
    ],
  ],
  'html' => function() {
    return tpl::load(__DIR__ . DS . 'typography.html.php');
  }  
);
