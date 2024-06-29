<?php if(in_array($dateFormatType, array('ym', 'y'))): ?>
<div class="btn-group">
    <a class="btn btn-primary btn-sm" href="<?=$url['dateChange']['prev']?>">
        <span class="glyphicon glyphicon-triangle-left"></span>
    </a>
    <a class="btn btn-default btn-sm disabled">
        <?=$date?>
    </a>
    <a class="btn btn-primary btn-sm" href="<?=$url['dateChange']['next']?>">
        <span class="glyphicon glyphicon-triangle-right"></span>
    </a>
</div>
<?php else: ?>
<div class="btn-group">
    <a class="btn btn-primary btn-sm disabled" href="<?=$url['dateChange']['prev']?>">
        <span class="glyphicon glyphicon-triangle-left"></span>
    </a>
    <a class="btn btn-default btn-sm disabled">
        ----
    </a>
    <a class="btn btn-primary btn-sm disabled" href="<?=$url['dateChange']['next']?>">
        <span class="glyphicon glyphicon-triangle-right"></span>
    </a>
</div>
<?php endif ?>
<a class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#date-selection">
    <span class="glyphicon glyphicon-calendar"></span>
</a>

<!-- Date selecting modal -->
<div class="modal fade" id="date-selection" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?=form_open($url['dateSelectionForm'], array('method'=>'get', 'id'=>'dateSelectionForm', 'class'=>'form-horizon'))?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Lựa chọn thời gian</h4>
                </div>
                <div class="modal-body">
                    <?=$this->template->get_view('elements/timeline/header_ym_modal')?>
                </div>
                <div class="modal-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary" onClick="Cashbook.submitButton(this, 'submit')">Chọn</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        <?=form_close()?>
    </div>
</div>
