<script type="text/javascript">
    $(function(){
        $('#sortable').sortable({
            placeholder: 'ui-state-highlight',
        });
        $('#sortable').disableSelection();
    });
</script>

<ul id="sortable">
  <li class="ui-state-default">Item 1</li>
  <li class="ui-state-default">Item 2</li>
  <li class="ui-state-default">Item 3</li>
  <li class="ui-state-default">Item 4</li>
  <li class="ui-state-default">Item 5</li>
  <li class="ui-state-default">Item 6</li>
  <li class="ui-state-default">Item 7</li>
</ul>