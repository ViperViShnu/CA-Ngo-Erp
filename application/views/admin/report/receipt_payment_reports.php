<form role="form" enctype="multipart/form-data" id="form"
      action="<?php echo base_url(); ?>admin/report/receipt_payment" method="post" class="form-horizontal  ">

    <div class="form-group">
        <label class="col-lg-3 control-label"><?= lang('start_date') ?></label>
        <div class="col-lg-3">
            <div class="input-group">
                <input type="text" name="start_date" class="form-control datepicker" value="<?php
                if (!empty($start_date)) {
                    echo $start_date;
                } else {
                    echo date('Y-m-d');
                }
                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                <div class="input-group-addon">
                    <a href="#"><i class="fa fa-calendar"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label"><?= lang('end_date') ?></label>
        <div class="col-lg-3">
            <div class="input-group">
                <input type="text" name="end_date" class="form-control datepicker" value="<?php
                if (!empty($end_date)) {
                    echo $end_date;
                } else {
                    echo date('Y-m-d');
                }
                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                <div class="input-group-addon">
                    <a href="#"><i class="fa fa-calendar"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label"></label>
        <div class="col-lg-2">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-check"></i> <?= lang('submit') ?>
            </button>
        </div>
    </div>
</form>

<?php if (!empty($report)): ?>

	<div id="printLedger">
	    <div class="show_print">
	        <div style="width: 100%; border-bottom: 2px solid black;">
	            <table style="width: 100%; vertical-align: middle;">
	                <tr>
	                    <td style="width: 50px; border: 0px;">
	                        <img style="width: 50px;height: 50px;margin-bottom: 5px;"
	                             src="<?= base_url() . config_item('company_logo') ?>" alt="" class="img-circle"/>
	                    </td>
	                    <td style="border: 0px;">
	                        <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
	                    </td>
	                </tr>
	            </table>
	        </div>
	        <br/>
	    </div>
	    
	    <div class="panel panel-custom">
	        <div class="panel-heading">
	            <div class="panel-title">
	                <strong><?= lang('receipt_payment_reports') ?></strong>
	                <div class="pull-right hidden-print">
	                    <a href="<?php echo base_url() ?>admin/report/receipt_payment_report_pdf/<?= date('Y-m-d', strtotime($start_date)) . '/' . date('Y-m-d', strtotime($end_date)) ?>" 
	                       class="btn btn-xs btn-success" title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
	                    <a onclick="print_ledger_report('printLedger')" class="btn btn-xs btn-danger" 
	                       title="<?= lang('print') ?>"><?= lang('print') ?></a>
	                </div>
	            </div>
	        </div>
	        
	        <div class="table-responsive">
	            <table class="table table-borderless DataTables" cellspacing="0" width="100%">
	                <thead>
	                    <tr>
	                        <th></th>
	                        <th><?= lang('particulars') ?></th>
	                        <th><?= lang('receipts') ?></th>
	                        <th><?= lang('payments') ?></th>
	                        <!-- <th><?= lang('balance') ?></th> -->
	                    </tr>
	                </thead>
	                <tbody>

	                	<tr>
		                    <td colspan="4">&nbsp;</td>
		                </tr>

	                    <tr style="background: #dff0d8; font-weight: bold; font-size: 20px;">
	                        <td colspan="2" style="text-align: center;"><?= lang('opening_balance') ?></td>
	                        <td></td>
	                        <td></td>
	                    </tr>

	                    <?php
	                    	$opening_balance = array();
	                    	$total_receipts = $total_payments = 0;
	                    	$account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
	                    	foreach($account_info as $info) {
	                    		$opening_balance[$info->account_name] = $this->db->where(array('date >= ' => $start_date, 'account_id' => $info->account_id))->order_by('date')->order_by('transactions_id')->get('tbl_transactions')->row()->previous_total_balance;
	                    	}

	                    	foreach($opening_balance as $key => $data) {
	                    		$total_receipts += $data; ?>
	                    		<tr>
	                    			<td></td>
	                    			<td><?php echo 'Cash in ' . $key; ?></td>
	                    			<td><?php echo $data; ?></td>
	                    			<td></td>
	                    		</tr>
	                    	<?php } ?>

	                    	<tr>
			                    <td colspan="4">&nbsp;</td>
			                </tr>

	                    <?php

	                    if(!empty($receipts_info)) {
	                    	foreach($receipts_info as $receipt) {
	                    		$total_receipts += $receipt->credit; ?>
	                    		<tr>
	                    			<td>To</td>
	                    			<td><?php echo $receipt->name; ?></td>
	                    			<td><?php echo $receipt->credit; ?></td>
	                    			<td></td>
	                    		</tr>
	                    	<?php }
	                    } ?>

	                    <tr>
	                    	<td colspan="4">&nbsp;</td>
	                    </tr>

	                    <?php

	                    if(!empty($payments_info)) {
	                    	foreach($payments_info as $payment) {
	                    		$total_payments += $payment->debit; ?>
	                    		<tr>
	                    			<td>By</td>
	                    			<td><?php echo $payment->name; ?></td>
	                    			<td></td>
	                    			<td><?php echo $payment->debit; ?></td>
	                    		</tr>
	                    	<?php }
	                    }
	                    ?>

	                    <tr>
	                    	<td colspan="4">&nbsp;</td>
	                    </tr>

	                    <tr style="background: #dff0d8; font-weight: bold; font-size: 20px;">
	                        <td colspan="2" style="text-align: center;"><?= lang('closing_balance') ?></td>
	                        <td></td>
	                        <td></td>
	                    </tr>

	                    <?php
	                    	$closing_balance = array();
	                    	$account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
	                    	foreach($account_info as $info) {
	                    		// $closing_balance[$info->account_name] = $this->db->where(array('date >= ' => $start_date, 'date <= ' => $end_date, 'account_id' => $info->account_id))->order_by('transactions_id')->get('tbl_transactions')->row()->total_balance;
	                    		$closing_balance[$info->account_name] = $this->db->where(array('date <= ' => $end_date, 'account_id' => $info->account_id))->order_by('date', 'DESC')->order_by('transactions_id', 'DESC')->get('tbl_transactions')->row()->total_balance;
	                    	}

	                    	foreach($closing_balance as $key => $data) { 
	                    		$total_payments += $data; ?>
	                    		<tr>
	                    			<td></td>
	                    			<td><?php echo 'Cash in ' . $key; ?></td>
	                    			<td></td>
	                    			<td><?php echo $data; ?></td>
	                    		</tr>
	                    	<?php }
	                    ?>

	                    <tr style="box-shadow: 0px -2px 0px black, 0px 2px 0px black;">
	                    	<td></td>
	                    	<td></td>
	                    	<td><?php echo $total_receipts; ?></td>
	                    	<td><?php echo $total_payments; ?></td>
	                    </tr>

	                </tbody>
	            </table>
	        </div>
	    </div>
	</div>

	<script type="text/javascript">
	    function print_ledger_report(printLedger) {
	        var printContents = document.getElementById(printLedger).innerHTML;
	        var originalContents = document.body.innerHTML;
	        document.body.innerHTML = printContents;
	        window.print();
	        document.body.innerHTML = originalContents;
	    }
	</script>

<?php endif; ?>