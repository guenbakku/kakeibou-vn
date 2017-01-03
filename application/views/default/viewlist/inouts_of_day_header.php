<script type="text/javascript">
    $(function(){
        $('.submit-on-change').change(function(){
            $(this).parents('form').submit();
        });
    });
</script>

<div class="container">
    <h4 class="text-center" style="margin-top:0">
        <a class="glyphicon glyphicon-chevron-left clean" href="<?=$url['dateChange']['prev']?>"></a> 
        <?=$date?> 
        <a class="glyphicon glyphicon-chevron-right clean" href="<?=$url['dateChange']['next']?>"></a>
    </h4>
    
    <ul class="nav nav-tabs navigation">
        <li href="<?=$url['navTabs']['list']?>"><a href="<?=$url['navTabs']['list']?>">Danh sách</a></li>
        <li href="<?=$url['navTabs']['chart']?>"><a href="<?=$url['navTabs']['chart']?>">Biểu đồ</a></li>
    </ul>
</div>
<br>