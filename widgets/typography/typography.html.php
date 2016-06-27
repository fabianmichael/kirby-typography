<script>
jQuery(function($) {

  $.ajax({
    url: '<?= kirby()->urls()->index() ?>/plugins/typography/widget/api/status',
    dataType: 'json',
    success: function(response) {
      $(".js-typography-status").html(response.message);
    },
  });
  
  
  var _cleaning = false,
      _classBefore = '',
      _textBefore = '',
      $i,
      $span;
  
  $("#typography-widget .hgroup-options a").click(function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    e.stopPropagation();
    if (_cleaning) return;
    
    _cleaning = true;
    
    $i = $("i", this);
    _classBefore = $i.attr("class");
    $i.attr("class", "fa fa-refresh fa-spin fa-fw");
    
    $span = $("span", this);
    _textBefore = $span.html();
    $span.html(" <?= addslashes($l['typography.widget.action.flushing']) ?>");
    
    $.ajax({
      url:     '<?= kirby()->urls()->index() ?>/plugins/typography/widget/api/flush',
      dataType: 'json',
      success: function(response) {
        $i.attr("class", _classBefore);
        $span.html(_textBefore);
        $(".js-typography-status").html(response.message);
        _cleaning = false;
      },
      error: function(xhr, status, error) {
        $i.attr("class", "fa fa-warning");
        $span.html(" " + _textBefore);
        $(".js-typography-status").html(xhr.responseJSON.message);
        _cleaning = false;
      }
    });
  });
  
});
</script>

<div class="dashboard-box">
  <div class="js-typography-status / text">…</div>
</div>
<?php if (kirby()->option('typography.debug')): ?>
  <p class="debug-warning marginalia" style="position: relative; padding-left: 24px; font-size: 14px;">
    <span class="fa fa-exclamation-circle " style="position: absolute; top: 2px; left: 5px; font-size: 14px;"></span> <?= $l['typography.widget.notice.debug']; ?>
  </p>
<?php endif; ?>
