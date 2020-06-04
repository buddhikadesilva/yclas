<form class="form-horizontal" method="post" role="form" action="<?=Route::url('default', array('controller'=> 'authorize','action'=>'pay' , 'id' => $order->id_order))?>">
    <fieldset>
        <legend><?=_e('Pay with Credit Card')?></legend>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="card-number"><?=_e('Card Number')?></label>
            <div class="col-sm-9">
            <input type="text" class="form-control" name="card-number" id="card-number" placeholder="<?=__('Card Number')?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="expiry-month"><?=_e('Expiration Date')?></label>
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-4 col-xs-12">
                        <select class="form-control col-sm-2" name="expiry-month" id="expiry-month">
                            <?foreach (Date::months(Date::MONTHS_SHORT) as $month=>$name):?>
                            <option value="<?=$month?>" ><?=$month?> - <?=$name?></option>
                            <?endforeach?>
                        </select>
                    </div>
                    <div class="col-sm-3 col-xs-12">
                        <select class="form-control" name="expiry-year">
                            <?foreach (range(date('y'),date('y')+10) as $year):?>
                            <option><?=$year?></option>
                            <?endforeach?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-success btn-lg pull-right"><?=_e('Pay With Card')?> <span class="glyphicon glyphicon-chevron-right"></span></button>
            </div>
        </div>
    </fieldset>
</form>