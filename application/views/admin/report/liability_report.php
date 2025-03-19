<script src="<?= base_url() ?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/morris/morris.min.js"></script>
<div id="printReport">
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
                <strong><?= lang('liability_report') ?></strong>
                <div class="pull-right hidden-print">
                    <a href="<?php echo base_url() ?>admin/report/liability_report_pdf/" class="btn btn-xs btn-success"
                       data-toggle="tooltip" data-placement="top" title="<?= lang('pdf') ?>"><?= lang('pdf') ?></a>
                    <a onclick="print_sales_report('printReport')" class="btn btn-xs btn-danger" data-toggle="tooltip"
                       data-placement="top" title="<?= lang('print') ?>"><?= lang('print') ?></a>
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <h5><strong><?= lang('liability_summary') ?></strong></h5>
            <hr>
            <strong>
                <p>
                    <?= lang('total_liability') ?>: <?php
                    $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                    $mdate = date('Y-m-d');
                    //first day of month
                    $first_day_month = date('Y-m-01');
                    //first day of Weeks
                    $this_week_start = date('Y-m-d', strtotime('previous sunday'));
                    // 30 days before
                    $before_30_days = date('Y-m-d', strtotime('today - 30 days'));

                    $income_categories = array(4); // Income income_category_id to get those specific category id only in the Liabilities summary...
                    $expense_categories = array(1); // Expense expense_category_id to get those specific category id only in the Liabilities summary...

                    // $total_income = $this->db->select_sum('credit')->get('tbl_transactions')->row();
                    // $this_month = $this->db->where(array('date >=' => $first_day_month, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
                    // $this_week = $this->db->where(array('date >=' => $this_week_start, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();
                    // $this_30_days = $this->db->where(array('date >=' => $before_30_days, 'date <=' => $mdate))->select_sum('credit')->get('tbl_transactions')->row();

                    $total_income = $this->db->select_sum('credit')
                        ->select_sum('debit')
                        ->group_start()
                            ->where('type', 'Income')->where_in('category_id', $income_categories)
                            ->or_group_start()
                                ->where('type', 'Expense')->where_in('category_id', $expense_categories)
                            ->group_end()
                        ->group_end()
                        ->get('tbl_transactions')->row();
                    $total_income_value = $total_income->credit + $total_income->debit;

                    $this_month = $this->db->select_sum('credit')
                        ->select_sum('debit')
                        ->where('date >= ', $first_day_month)
                        ->where('date <= ', $mdate)
                        ->group_start()
                            ->where('type', 'Income')->where_in('category_id', $income_categories)
                            ->or_group_start()
                                ->where('type', 'Expense')->where_in('category_id', $expense_categories)
                            ->group_end()
                        ->group_end()
                        ->get('tbl_transactions')->row();
                    $this_month_value = $this_month->credit + $this_month->debit;

                    $this_week = $this->db->select_sum('credit')
                        ->select_sum('debit')
                        ->where('date >= ', $this_week_start)
                        ->where('date <= ', $mdate)
                        ->group_start()
                            ->where('type', 'Income')->where_in('category_id', $income_categories)
                            ->or_group_start()
                                ->where('type', 'Expense')->where_in('category_id', $expense_categories)
                            ->group_end()
                        ->group_end()
                        ->get('tbl_transactions')->row();
                    $this_week_value = $this_week->credit + $this_week->debit;

                    $this_30_days = $this->db->select_sum('credit')
                        ->select_sum('debit')
                        ->where('date >= ', $before_30_days)
                        ->where('date <= ', $mdate)
                        ->group_start()
                            ->where('type', 'Income')->where_in('category_id', $income_categories)
                            ->or_group_start()
                                ->where('type', 'Expense')->where_in('category_id', $expense_categories)
                            ->group_end()
                        ->group_end()
                        ->get('tbl_transactions')->row();
                    $this_30_days_value = $this_30_days->credit + $this_30_days->debit;

                    echo display_money($total_income_value, $curency->symbol);
                    ?></p>
                <p><?= lang('total_liability_this_month') ?>
                    : <?= display_money($this_month_value, $curency->symbol) ?></p>
                <p><?= lang('total_liability_this_week') ?>
                    : <?= display_money($this_week_value, $curency->symbol) ?></p>
                <p><?= lang('total_liability_last_30') ?>
                    : <?= display_money($this_30_days_value, $curency->symbol) ?></p>
            </strong>

            <hr>

            <h4><?= lang('last_deposit_liability') ?></h4>
            <hr>
            <table class="table table-striped table-bordered">
                <tbody>
                <tr>
                    <th><?= lang('date') ?></th>
                    <th><?= lang('account') ?></th>
                    <th><?= lang('deposit_category') ?></th>
                    <th><?= lang('paid_by') ?></th>
                    <th><?= lang('description') ?></th>
                    <th><?= lang('amount') ?></th>
                    <th><?= lang('credit') ?></th>
                    <th><?= lang('debit') ?></th>
                    <th><?= lang('balance') ?></th>
                </tr>
                <?php
                $total_amount = 0;
                $total_credit = 0;
                $total_balance = 0;

                // $all_deposit_info = $this->db->where_in('category_id', array(1,4,6))->limit(20)->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();

                $income_categories = array(4); // Income income_category_id to get those specific category id only in the Liabilities table...
                $expense_categories = array(1); // Expense expense_category_id to get those specific category id only in the Liabilities table...

                $this->db->group_start()
                    ->where('type', 'Income')->where_in('category_id', $income_categories)
                    ->or_group_start()
                        ->where('type', 'Expense')->where_in('category_id', $expense_categories)
                    ->group_end()
                ->group_end();

                $all_deposit_info = $this->db->limit(20)
                    ->order_by('date', 'ASC')
                    ->get('tbl_transactions')
                    ->result();

                foreach ($all_deposit_info as $v_deposit) :
                    $account_info = $this->report_model->check_by(array('account_id' => $v_deposit->account_id), 'tbl_accounts');
                    $client_info = $this->report_model->check_by(array('client_id' => $v_deposit->paid_by), 'tbl_client');
                    if($v_deposit->type == 'Income') {
                        $category_info = $this->report_model->check_by(array('income_category_id' => $v_deposit->category_id), 'tbl_income_category');
                    } elseif($v_deposit->type == 'Expense') {
                        $category_info = $this->report_model->check_by(array('expense_category_id' => $v_deposit->category_id), 'tbl_expense_category');
                    }
                    if (!empty($client_info)) {
                        $client_name = $client_info->name;
                    } else {
                        $client_name = '-';
                    }
                    ?>
                    <tr>
                        <td><?= strftime(config_item('date_format'), strtotime($v_deposit->date)); ?></td>
                        <td><?= !empty($account_info->account_name) ? $account_info->account_name : '-' ?></td>
                        <td><?php
                            if (!empty($category_info)) {
                                if($v_deposit->type == 'Income') {
                                    echo $category_info->income_category;
                                } elseif($v_deposit->type == 'Expense') {
                                    echo $category_info->expense_category;
                                }
                            } else {
                                echo '-';
                            }
                            ?></td>
                        <td><?= $client_name ?></td>
                        <td><?= $v_deposit->notes ?></td>
                        <td><?= display_money($v_deposit->amount, $curency->symbol) ?></td>
                        <td><?= display_money($v_deposit->credit, $curency->symbol) ?></td>
                        <td><?= display_money($v_deposit->debit, $curency->symbol) ?></td>
                        <td><?= display_money($v_deposit->total_balance, $curency->symbol) ?></td>

                    </tr>
                    <?php
                    $total_amount += $v_deposit->amount;
                    $total_credit += $v_deposit->credit;
                    $total_debit += $v_deposit->debit;
                    $total_balance += $v_deposit->total_balance;
                    ?>
                    <?php
                endforeach;
                ?>
                <tr class="custom-color-with-td">
                    <td style="text-align: right;" colspan="5"><strong><?= lang('total') ?>:</strong></td>
                    <td><strong><?= display_money($total_amount, $curency->symbol) ?></strong></td>
                    <td><strong><?= display_money($total_credit, $curency->symbol) ?></strong></td>
                    <td><strong><?= display_money($total_debit, $curency->symbol) ?></strong></td>
                    <td><strong><?= display_money($total_balance, $curency->symbol) ?></strong></td>
                </tr>
                </tbody>
            </table>
            <hr>

        </div>
    </div>
</div>
<div class="panel panel-custom ">
    <div class="panel-heading">
        <div class="panel-title">
            <strong><?= lang('liability_report') . ' ' . lang('graph') . ' ' . date('F-Y') ?></strong>
        </div>
    </div>
    <div class="panel-body">
        <div id="morris-line"></div>
    </div>
</div>
<script type="text/javascript">
    $(function () {

        if (typeof Morris === 'undefined') return;

        var chartdata = [
            <?php foreach ($transactions_report as $days => $v_report){
            $total_expense = 0;
            $total_income = 0;
            $total_transfer = 0;
            foreach ($v_report as $Expense) {
                if ($Expense->type == 'Income') {
                    $total_income += $Expense->amount;
                }

                if($Expense->type == 'Expense') {
                    $total_expense += $Expense->amount;
                }
            }
            ?>
            {
                y: "<?= $days ?>",
                income: <?= $total_income?>,
                expense: <?= $total_expense?>,
            },
            <?php }?>


        ];
        // Line Chart
        // -----------------------------------

        new Morris.Line({
            element: 'morris-line',
            data: chartdata,
            xkey: 'y',
            ykeys: ["income", "expense"],
            labels: ["<?= lang('Income')?>", "<?= lang('Expense')?>" ],
            lineColors: ["#27c24c", "#f05050"],
            parseTime: false,
            resize: true
        });

    });
    function print_sales_report(printReport) {
        var printContents = document.getElementById(printReport).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>