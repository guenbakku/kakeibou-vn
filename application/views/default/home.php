<script type="text/javascript">
$(function(){
    $('.toggle').click(function(){
        $('.toggle-target').toggleClass('hide');        
    });
});
</script>

<div class="container">
    <div class="panel panel-default">
        <a class="panel-heading" href="<?=base_url()?>summary/viewlist" style="display:block">
            <strong><span class="glyphicon glyphicon-menu-right pull-right"></span> Thu chi trong tháng</strong>
        </a>
        <table class="table">
            <tr>
                <td style="width:33.3%">Thu</td>
                <td style="width:33.3%">Chi</td>
                <td style="width:33.3%">Chênh lệch</td>
            </tr>
            <tr>
                <td>20,000</td>
                <td>10,000</td>
                <td>10,000</td>
            </tr>
            <tr>
                <th colspan="2">Mỗi ngày có thể tiêu:</th>
                <th>1,500</th>
            </tr>
        </table>
    </div>
</div>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Còn lại</strong></div>
        <table class="table">
            <tr data-toggle="collapse" data-target="#collapse-target">
                <td>Tiền mặt</td>
                <td style="width:40%">10,000,000 <span class="glyphicon glyphicon-triangle-bottom pull-right"></span></td>
            </tr>
            <tbody class="panel-collapse collapse" id="collapse-target">
                <tr>
                    <td><span class="glyphicon glyphicon-arrow-right" style="padding-left:30px"></span> Bách</td>
                    <td>10,000</td>
                </tr>
                <tr>
                    <td><span class="glyphicon glyphicon-arrow-right" style="padding-left:30px"></span> Hiệp</td>
                    <td>10,000</td>
                </tr>
            </tbody>
            <tr>
                <td>Tài khoản</td>
                <td>10,000</td>
            </tr>
            <tr>
                <th>Tổng cộng</th>
                <th>10,000</th>
            </tr>
        </table>
    </div>
</div>