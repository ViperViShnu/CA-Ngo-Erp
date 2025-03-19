<!DOCTYPE html>
<html>
<head>
    <title><?= lang('receipt_payment_report') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php
    $direction = $this->session->userdata('direction');
    if (!empty($direction) && $direction == 'rtl') {
        $RTL = 'on';
    } else {
        $RTL = config_item('RTL');
    }?>

    <style type="text/css">
        th {
            text-align: left;
        }
    </style>

    <!-- <style>
        th {
            padding: 10px 0px 5px 5px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }else{?>text-align: left;<?php }?>
            font-size: 13px;
            border: 1px solid black;
        }

        td {
            padding: 5px 0px 0px 5px;
            border: 1px solid black;
            font-size: 13px;
        <?php if(!empty($RTL)){?> text-align: right;<?php }else{?>text-align: left;<?php }?>
        }
    </style> -->

</head>
<body style="min-width: 98%; min-height: 100%; overflow: hidden; alignment-adjust: central;">
<br/>
<?php
$img_path = ROOTPATH . '/' . config_item('company_logo');
if (!file_exists($img_path)) {
    $img_path = ROOTPATH . '/uploads/default_logo.png'; // Fallback image
}

if (file_exists($img_path)) {
    $image_data = file_get_contents($img_path);
    $base64_img = 'data:image/png;base64,' . base64_encode($image_data);
} else {
    $base64_img = ''; // Empty fallback
}
?>
<div style="width: 100%; border-bottom: 2px solid black;">
    <table style="width: 100%; vertical-align: middle;">
        <tr>
            <td style="width: 50px; border: 0px;">
                <img style="width: 130px;height: 50px;margin-bottom: 5px;"
                     src="<?= $base64_img ?>" alt="" class="img-circle"/>
            </td>

            <td style="border: 0px;">
                <p style="margin-left: 10px; font: 14px lighter;"><?= config_item('company_name') ?></p>
            </td>
        </tr>
    </table>
</div>
<br/>
<div style="width: 100%;">
    <table style="width: 100%; font-family: Arial, Helvetica, sans-serif; border-collapse: collapse;">
        <thead>
            <tr>
                <th></th>
                <th><?php echo lang('particulars'); ?></th>
                <th><?php echo lang('receipts') ?></th>
                <th><?php echo lang('payments') ?></th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>

            <tr style="background: #dff0d8; font-weight: bold; font-size: 20px;">
                <td colspan="2" style="text-align: center;"><?php echo lang('opening_balance'); ?></td>
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

                foreach($opening_balance as $key => $balance) {
                    $total_receipts += $balance; ?>
                    <tr>
                        <td></td>
                        <td><?php echo 'Cash in ' . $key; ?></td>
                        <td><?php echo $balance; ?></td>
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
                <td colspan="2" style="text-align: center;"><?php echo lang('closing_balance'); ?></td>
                <td></td>
                <td></td>
            </tr>

            <?php
                $closing_balance = array();
                $account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
                foreach($account_info as $info) {
                    $closing_balance[$info->account_name] = $this->db->where(array('date <= ' => $start_date, 'account_id' => $info->account_id))->order_by('date', 'DESC')->order_by('transactions_id', 'DESC')->get('tbl_transactions')->row()->total_balance;
                }

                foreach($closing_balance as $key => $balance) {
                    $total_payments += $balance; ?>
                    <tr>
                        <td></td>
                        <td><?php echo 'Cash in ' . $key; ?></td>
                        <td></td>
                        <td><?php echo $balance; ?></td>
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
</body>
</html>