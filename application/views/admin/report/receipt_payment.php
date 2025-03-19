<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><?= lang('receipt_payment') ?>
            <div class="btn-group pull-right btn-with-tooltip-group _filter_data filtered" data-toggle="tooltip"
                 data-title="<?php echo lang('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu group animated zoomIn"
                    style="width:300px;">
                    <li class="filter_by all_filter"><a href="#"><?php echo lang('all'); ?></a></li>
                    <li class="divider"></li>

                    <li class="dropdown-submenu pull-left  " id="from_account">
                        <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('account'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left from_account"
                            style="">
                            <?php
                            $account_info = $this->db->order_by('account_id', 'DESC')->get('tbl_accounts')->result();
                            if (!empty($account_info)) {
                                foreach ($account_info as $v_account) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_account->account_id ?>" search-type="by_account">
                                        <a href="#"><?php echo $v_account->account_name; ?></a>
                                    </li>
                                <?php }
                            }
                            ?>
                        </ul>
                    </li>
                    <!-- <div class="clearfix"></div>
                    <li class="dropdown-submenu pull-left " id="to_account">
                        <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('categories'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left to_account"
                            style="">
                            <?php
                            $expense_category = $this->db->get('tbl_expense_category')->result();
                            if (count(array($expense_category)) > 0) { ?>
                                <?php foreach ($expense_category as $v_category) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_category->expense_category_id ?>"
                                        search-type="by_category">
                                        <a href="#"><?php echo $v_category->expense_category; ?></a>
                                    </li>
                                <?php }
                                ?>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </ul>
                    </li> -->
                    <div class="clearfix"></div>
                    <li class="dropdown-submenu pull-left " id="from_to_account">
                        <a href="#" tabindex="-1"><?php echo lang('by') . ' ' . lang('paid_out_of'); ?></a>
                        <ul class="dropdown-menu dropdown-menu-left from_to_account"
                            style="">
                            <?php
                            $income_category = $this->db->get('tbl_income_category')->result();
                            if (count(array($income_category)) > 0) { ?>
                                <?php foreach ($income_category as $v_category) {
                                    ?>
                                    <li class="filter_by" id="<?= $v_category->income_category_id ?>"
                                        search-type="by_paid_out_of">
                                        <a href="#"><?php echo $v_category->income_category; ?></a>
                                    </li>
                                <?php }
                                ?>
                                <div class="clearfix"></div>
                            <?php } ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <!-- <th><?= lang('id') ?></th> -->
                    <th style="width: 15%"><?= lang('date') ?></th>
                    <th style="width: 15%"><?= lang('account') ?></th>
                    <th><?= lang('type') ?></th>
                    <th><?= lang('notes') ?></th>
                    <th><?= lang('amount') ?></th>
                    <th><?= lang('credit') ?></th>
                    <th><?= lang('debit') ?></th>
                    <th><?= lang('balance') ?></th>
                </tr>
                </thead>
                <tbody>
                <script type="text/javascript">
                    $(document).ready(function () {
                        list = base_url + "admin/report/paymentReceiptList";
                        $('.filtered > .dropdown-toggle').on('click', function () {
                            if ($('.group').css('display') == 'block') {
                                $('.group').css('display', 'none');
                            } else {
                                $('.group').css('display', 'block')
                            }
                        });
                        $('.all_filter').on('click', function () {
                            $('.to_account').removeAttr("style");
                            $('.from_account').removeAttr("style");
                        });
                        $('.from_account li').on('click', function () {
                            if ($('.to_account').css('display') == 'block') {
                                $('.to_account').removeAttr("style");
                                $('.from_account').css('display', 'block');
                            } else {
                                $('.from_account').css('display', 'block')
                            }
                        });

                        $('.to_account li').on('click', function () {
                            if ($('.from_account').css('display') == 'block') {
                                $('.from_account').removeAttr("style");
                                $('.to_account').css('display', 'block');
                            } else {
                                $('.to_account').css('display', 'block');
                            }
                        });
                        $('.filter_by').on('click', function () {
                            $('.filter_by').removeClass('active');
                            $('.group').css('display', 'block');
                            $(this).addClass('active');
                            var filter_by = $(this).attr('id');
                            if (filter_by) {
                                filter_by = filter_by;
                            } else {
                                filter_by = '';
                            }
                            var search_type = $(this).attr('search-type');
                            if (search_type) {
                                search_type = '/' + search_type;
                            } else {
                                search_type = '';
                            }
                            table_url(base_url + "admin/report/paymentReceiptList/" + filter_by + search_type);

                            $.ajax({
                                url: base_url + "admin/report/paymentReceiptTotalList/" + filter_by + search_type,
                                type: "POST",
                                dataType: "json",
                                data: {
                                    filterBy : filter_by,
                                    type : search_type.replace(/[^a-zA-Z0-9_-]/g, ''),
                                },
                                success: function (response) {
                                    if (response.success) {
                                        console.log(response.data);
                                        updateTotals(response.data);
                                    }
                                }
                            });
                        });
                    });

                    function updateTotals(totals) {
                        $(".total_balance").text(totals.total_balance);
                        $(".total_amount").text(totals.total_amount);
                        $(".total_credit").text(totals.total_credit);
                        $(".total_debit").text(totals.total_debit);
                    }
                </script>

                <?php
                $total_amount = 0;
                $total_credit = 0;
                $total_debit = 0;
                $total_balance = 0;
                $curency = $this->report_model->check_by(array('code' => config_item('default_currency')), 'tbl_currencies');
                $all_expense_info = $this->db->order_by('transactions_id', 'DESC')->get('tbl_transactions')->result();
                foreach ($all_expense_info as $v_expense) :
                    $total_amount += $v_expense->amount;
                    $total_credit += $v_expense->credit;
                    $total_debit += $v_expense->debit;
                endforeach;
                $total_balance = $total_credit - $total_debit;
                ?>

                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer">
        <strong style="width: 25%"><?= lang('balance') ?>:<span
                class="label label-info total_balance"><?= display_money($total_credit - $total_debit, $curency->symbol) ?></span>
        </strong>
        <strong class="col-sm-3"><?= lang('total_amount') ?>:<span
                class="label label-success total_amount">
                <?= display_money($total_amount, $curency->symbol) ?>
            </span>
        </strong>
        <strong class="col-sm-3"><?= lang('credit') ?>:<span
                class="label label-primary total_credit">
                <?= display_money($total_credit, $curency->symbol) ?>
            </span>
        </strong>
        <strong class="col-sm-3"><?= lang('debit') ?>:<span
                class="label label-danger total_debit">
                <?= display_money($total_debit, $curency->symbol) ?>
                </span>
        </strong>

    </div>
</div>