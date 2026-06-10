<div class="page">
    <div class="page-title blue">
        <h3>
            <?php echo $breadcrumb; ?>
        </h3>
       
    </div>
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h3 class="panel-title">Welcome to the Administrator's Panel</h3>
                    </div>
                    <div class="panel-body container-fluid">
                        <div class="blockquote gray">
                            <h3>Hello,
                                <?php echo $admin->admin_nama; ?>
                            </h3>
                            <p>This information is for administrators or staff to run the data within the system.</p>
	<!-- <div style="margin-top: 20px;" class='onesignal-customlink-container'></div> -->
                        </div>
                    </div>
                </div>
                <div class="row dashboard-card-row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 dashboard-card-col">
                        <div class="small-box dashboard-small-box bg-green">
                            <div class="inner">
                                <h3>
                                    <?php echo $jml_data_transaksi_masuk ?>
                                </h3>
                                <p>Incoming Goods</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-archive"></i>
                            </div>
                            <a href="<?php echo site_url();?>website/masuk" class="small-box-footer">
                            View Incoming Goods
                                <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 dashboard-card-col">
                        <div class="small-box dashboard-small-box bg-red">
                            <div class="inner">
                                <h3>
                                    <?php echo $jml_data_transaksi_keluar ?>
                                </h3>
                                <p>Outgoing Goods</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-archive"></i>
                            </div>
                            <a href="<?php echo site_url();?>website/keluar" class="small-box-footer">
                            View Outgoing Goods
                                <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 dashboard-card-col">
                        <div class="small-box dashboard-small-box bg-warning">
                            <div class="inner">
                                <h3>
                                    <?php echo isset($jml_data_supplier) ? (int) $jml_data_supplier : 0;?>
                                </h3>
                                <p>Suppliers</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-th-large"></i>
                            </div>
                            <a href="<?php echo site_url();?>website/supplier" class="small-box-footer">
                            View Suppliers Info
                                <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 dashboard-card-col">
                        <div class="small-box dashboard-small-box bg-aqua">
                            <div class="inner">
                                <h3>
                                    <?php echo isset($jml_data_customer) ? (int) $jml_data_customer : 0;?>
                                </h3>
                                <p>Customers</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                            <a href="<?php echo site_url();?>website/customer" class="small-box-footer">
                            View Customers Info
                                <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row dashboard-card-row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 dashboard-card-col">
                        <div class="small-box dashboard-small-box dashboard-money-card bg-green">
                            <div class="inner">
                                <h3><?php echo format_ksh(isset($sales_dashboard['today_sales']) ? $sales_dashboard['today_sales'] : 0); ?></h3>
                                <p>Today's Sales</p>
                            </div>
                            <div class="icon"><i class="fa fa-money"></i></div>
                            <a href="<?php echo site_url();?>sales/report?range=daily" class="small-box-footer">View Sales Report <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 dashboard-card-col">
                        <div class="small-box dashboard-small-box dashboard-money-card bg-aqua">
                            <div class="inner">
                                <h3><?php echo format_ksh(isset($sales_dashboard['monthly_sales']) ? $sales_dashboard['monthly_sales'] : 0); ?></h3>
                                <p>Monthly Sales</p>
                            </div>
                            <div class="icon"><i class="fa fa-line-chart"></i></div>
                            <a href="<?php echo site_url();?>sales/report?range=monthly" class="small-box-footer">View Monthly Sales <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 dashboard-card-col">
                        <div class="small-box dashboard-small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo isset($sales_dashboard['total_invoices']) ? (int) $sales_dashboard['total_invoices'] : 0; ?></h3>
                                <p>Total Invoices</p>
                            </div>
                            <div class="icon"><i class="fa fa-file-text-o"></i></div>
                            <a href="<?php echo site_url();?>sales/records" class="small-box-footer">View Invoices <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 dashboard-card-col">
                        <div class="small-box dashboard-small-box bg-red">
                            <div class="inner">
                                <h3><?php echo isset($sales_dashboard['low_stock_items']) ? (int) $sales_dashboard['low_stock_items'] : 0; ?></h3>
                                <p>Low Stock Items</p>
                            </div>
                            <div class="icon"><i class="fa fa-warning"></i></div>
                            <a href="<?php echo site_url();?>website/barang" class="small-box-footer">View Goods <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
