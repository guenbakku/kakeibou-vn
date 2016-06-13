<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <table>
                <tr>
                    <td>
                        <select class="form-control">
                            <option>Năm 2016</option>
                            <option>Năm 2017</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control">
                            <option>Tất cả</option>
                            <option>Tháng 01</option>
                            <option>Tháng 02</option>
                        </select>
                    </td>
                </tr>
            </table>
            <hr style="margin:10px 0">
            <div>
                <strong>Tổng cộng</strong><br>
                <span class="w30 text-blue">200,000</span>
                <span class="w30 text-red">-1,5000,000</span>
                <span class="w30">-5,000,000,000</span>
            </div>
        </div>
        <div class="list-group">
            <?php for($i=0; $i<50; $i++) : ?>
            <a class="list-group-item" href="<?=base_url()?>summary/detail">
                <span class="glyphicon glyphicon-menu-right pull-right"></span>
                <p class="list-group-item-heading"><strong>2016/04/09</strong></p>
                <p class="list-group-item-text">
                    <span class="w30 text-blue">1,000</span>
                    <span class="w30 text-red">-1,000</span>
                    <span class="w30">1,00000</span>
                </p>
            </a>
            <?php endfor ?>
        </div>
    </div>
</div>