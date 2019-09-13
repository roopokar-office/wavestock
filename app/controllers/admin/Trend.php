<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Trend extends MY_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        $this->lang->admin_load('reports', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('reports_model');
        $this->load->admin_model('trend_model');
        $this->data['pb'] = array(
            'cash' => lang('cash'),
            'CC' => lang('CC'),
            'Cheque' => lang('Cheque'),
            'paypal_pro' => lang('paypal_pro'),
            'stripe' => lang('stripe'),
            'gift_card' => lang('gift_card'),
            'deposit' => lang('deposit'),
            'authorize' => lang('authorize'),
        );

    }

    private function monthAndYearStartAndEnd($startDate, $endDate) {
        $start = (new DateTime($startDate))->modify('first day of this month');
        $end = (new DateTime($endDate))->modify('last day of this month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        $dates = [];

        foreach ($period as $key => $dt) {
            $dates[$key]['year'] = $dt->format("Y");
            $dates[$key]['month'] = $dt->format("F");
        }
        return $dates;
    }

    private function start_end_date($start_date = null, $end_date = null) {
        $date = [];
        if ($start_date) {
            $date['start_date'] = explode(' ', $this->sma->fld($start_date))[0];
            if ($end_date) {
                $date['end_date'] = explode(' ', $this->sma->fld($end_date))[0];
            } else {
                $date['end_date'] = mdate("%Y-%m-%d", time());
            }
        } else {
            $date['start_date'] = mdate("%Y", time()) . "-01-01";
            $date['end_date'] = mdate("%Y-%m-%d", time());
        }

        return $date;
    }

    function index() {
        $this->sma->checkPermissions();
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['monthly_sales'] = $this->reports_model->getChartData();
        $this->data['stock'] = $this->reports_model->getStockValue();
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('reports')));
        $meta = array('page_title' => lang('reports'), 'bc' => $bc);
        $this->page_construct('reports/index', $meta, $this->data);

    }

    function purchases() {
        $this->sma->checkPermissions('purchases');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('Trend_Purchase_Report')));
        $meta = array('page_title' => lang('Trend_Purchase_Report'), 'bc' => $bc);

        $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : NULL;
        $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : NULL;

        $getDate = $this->start_end_date($start_date, $end_date);
        $start_date = $getDate['start_date'];
        $end_date = $getDate['end_date'];

        $this->data['months'] = $this->monthAndYearStartAndEnd($start_date, $end_date);
        $this->page_construct('trend/purchases', $meta, $this->data);
    }

    function getPurchaseReport($pdf = NULL, $xls = NULL) {
        $this->sma->checkPermissions('purchases', TRUE);
        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $supplier = $this->input->get('supplier') ? $this->input->get('supplier') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $getDate = $this->start_end_date($start_date, $end_date);
        $start_date = $getDate['start_date'];
        $end_date = $getDate['end_date'];

        $times = $this->monthAndYearStartAndEnd($start_date, $end_date);
        $select_months = "";
        $month_year = "";
        foreach ($times as $time) {
            $select_months .= ", ( SUM(IF(`month` = '" . $time['month'] . "' AND `year` = '" . $time['year'] . "', QTY, 0)) ) AS " . $time['month'] . "_" . $time['year'];
            $month_year .= "," . $time['month'] . "_" . $time['year'] . " ";
        }

        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        $pi = "( SELECT supplier_id, product_id, product_code, product_name, {$this->db->dbprefix('purchase_items')}.purchase_id AS purchase_id, {$this->db->dbprefix('purchase_items')}.`date`, YEAR({$this->db->dbprefix('purchase_items')}.`date`) AS `year`, MONTHNAME({$this->db->dbprefix('purchase_items')}.`date`) AS `month`, SUM(unit_quantity) AS QTY
FROM {$this->db->dbprefix('purchases')}
JOIN {$this->db->dbprefix('purchase_items')}
ON {$this->db->dbprefix('purchases')}.id = {$this->db->dbprefix('purchase_items')}.purchase_id";

        if ($start_date || $supplier) {
            $pi .= " WHERE ";
        }
        if ($start_date) {
            $pi .= "{$this->db->dbprefix('purchase_items')}.date BETWEEN '$start_date' AND '$end_date'";
        }
        if ($start_date && $supplier) {
            $pi .= " AND ";
        }
        if ($supplier) {
            $pi .= "supplier_id = $supplier";
        }
        $pi .= " GROUP BY YEAR({$this->db->dbprefix('purchase_items')}.`date`), MONTH({$this->db->dbprefix('purchase_items')}.`date`), purchase_id, product_id ) pi";

        $transposed = " ( SELECT * $select_months FROM $pi";
        $transposed .= " GROUP BY product_id ) tr";

        if ($pdf || $xls) {
            $this->db
                ->select("product_id, product_code, product_name AS product_name $month_year", FALSE)
                ->from('purchases')
                ->join($transposed, 'tr.purchase_id=purchases.id', 'right')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
                ->group_by('product_id');

            if ($product) {
                $this->db->where('tr.product_id', $product);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $char = chr(833); // chr(833) is A
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('Trend_Purchase_Report'));
                if ($supplier) {
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Supplier_Name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', $this->trend_model->getCustomerById($supplier)->name);
                }
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('From'));
                $this->excel->getActiveSheet()->SetCellValue('F1', $start_date);
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('To'));
                $this->excel->getActiveSheet()->SetCellValue('H1', $end_date);

                $first_data_row = 3;
                $this->excel->getActiveSheet()->SetCellValue($char++ . $first_data_row, lang('SL.'));
                $this->excel->getActiveSheet()->SetCellValue($char++ . $first_data_row, lang('Product_Code'));
                $this->excel->getActiveSheet()->SetCellValue($char++ . $first_data_row, lang('Product_Name'));
                foreach ($times as $time) {
                    $this->excel->getActiveSheet()->SetCellValue($char++ . $first_data_row, $time['month'] . "_" . $time['year']);
                }

                $row = $first_data_row + 1;
                $row_number = 1;
                foreach ($data as $data_row) {
                    $char = chr(833); // chr(833) is A
                    $this->excel->getActiveSheet()->SetCellValue($char++ . $row, $row_number++);
                    $this->excel->getActiveSheet()->SetCellValue($char++ . $row, $data_row->product_code);
                    $this->excel->getActiveSheet()->SetCellValue($char++ . $row, $data_row->product_name);
                    foreach ($times as $time) {
                        $month_name = $time['month'] . "_" . $time['year'];
                        $this->excel->getActiveSheet()->SetCellValue($char++ . $row, $data_row->$month_name);
                    }
                    $row++;
                }

                $char = chr(833); // chr(833) is A
                $this->excel->getActiveSheet()->getColumnDimension($char++)->setWidth(10);
                $this->excel->getActiveSheet()->getColumnDimension($char++)->setWidth(10);
                $this->excel->getActiveSheet()->getColumnDimension($char++)->setWidth(50);
                foreach ($times as $time) {
                    $this->excel->getActiveSheet()->getColumnDimension($char++)->setWidth(15);
                }
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('B1:B' . $row)->getAlignment()->setWrapText(true);
                $filename = 'Trend_Purchase_Report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {
            $this->load->library('datatables');
            $this->datatables
                ->select("product_id, product_name AS product_name $month_year", FALSE)
                ->from('purchases')
                ->join($transposed, 'tr.purchase_id=purchases.id', 'right')
                ->join('warehouses', 'warehouses.id=purchases.warehouse_id', 'left')
                ->group_by('product_id');

            if ($product) {
                $this->datatables->where('tr.product_id', $product, FALSE);
            }

            echo $this->datatables->generate();
        }
    }

    public function sales() {
        $this->sma->checkPermissions('sales');
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['users'] = $this->reports_model->getStaff();
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['billers'] = $this->site->getAllCompanies('biller');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('reports'), 'page' => lang('reports')), array('link' => '#', 'page' => lang('Trend_Sales_Report')));
        $meta = array('page_title' => lang('Trend_Sales_Report'), 'bc' => $bc);

        $start_date = $this->input->post('start_date') ? $this->input->post('start_date') : NULL;
        $end_date = $this->input->post('end_date') ? $this->input->post('end_date') : NULL;

        $getDate = $this->start_end_date($start_date, $end_date);
        $start_date = $getDate['start_date'];
        $end_date = $getDate['end_date'];

        $this->data['months'] = $this->monthAndYearStartAndEnd($start_date, $end_date);
        $this->page_construct('trend/sales', $meta, $this->data);
    }

    public function getSalesReport($pdf = NULL, $xls = NULL) {
        $this->sma->checkPermissions('sales', TRUE);
        $product = $this->input->get('product') ? $this->input->get('product') : NULL;
        $customer = $this->input->get('customer') ? $this->input->get('customer') : NULL;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : NULL;
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : NULL;

        $getDate = $this->start_end_date($start_date, $end_date);
        $start_date = $getDate['start_date'];
        $end_date = $getDate['end_date'];

        $times = $this->monthAndYearStartAndEnd($start_date, $end_date);
        $select_months = "";
        $month_year = "";
        foreach ($times as $time) {
            $select_months .= ", ( SUM(IF(`month` = '" . $time['month'] . "' AND `year` = '" . $time['year'] . "', QTY, 0)) ) AS " . $time['month'] . "_" . $time['year'];
            $month_year .= "," . $time['month'] . "_" . $time['year'] . " ";
        }

        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $user = $this->session->userdata('user_id');
        }

        $si = "( SELECT customer_id, product_id, product_code, product_name, {$this->db->dbprefix('sale_items')}.sale_id AS sale_id, {$this->db->dbprefix('sales')}.`date`, YEAR({$this->db->dbprefix('sales')}.`date`) AS `year`, MONTHNAME({$this->db->dbprefix('sales')}.`date`) AS `month`, SUM(unit_quantity) AS QTY
FROM {$this->db->dbprefix('sales')}
JOIN {$this->db->dbprefix('sale_items')}
ON {$this->db->dbprefix('sales')}.id = {$this->db->dbprefix('sale_items')}.sale_id";

        if ($start_date || $customer) {
            $si .= " WHERE ";
        }
        if ($start_date) {
            $si .= "{$this->db->dbprefix('sales')}.date BETWEEN '$start_date' AND '$end_date'";
        }
        if ($start_date && $customer) {
            $si .= " AND ";
        }
        if ($customer) {
            $si .= "customer_id = $customer";
        }
        $si .= " GROUP BY YEAR({$this->db->dbprefix('sales')}.`date`), MONTH({$this->db->dbprefix('sales')}.`date`), sale_id, product_id ) si";

        $transposed = " (SELECT * $select_months FROM $si";
        $transposed .= " GROUP BY product_id) tr";

        if ($pdf || $xls) {
            $this->db
                ->select("product_id, product_code, product_name AS product_name $month_year", FALSE)
                ->from('sales')
                ->join($transposed, 'tr.sale_id=sales.id', 'right')
                ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')
                ->group_by('product_id');

            if ($product) {
                $this->db->where('tr.product_id', $product);
            }

            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
            } else {
                $data = NULL;
            }

            if (!empty($data)) {
                $char = chr(833); // chr(833) is A
                $this->load->library('excel');
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(lang('Trend_Sales_Report'));
                if ($customer){
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('Customer_Name'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', $this->trend_model->getCustomerById($customer)->name);
                }
                $this->excel->getActiveSheet()->SetCellValue('E1', lang('From'));
                $this->excel->getActiveSheet()->SetCellValue('F1', $start_date);
                $this->excel->getActiveSheet()->SetCellValue('G1', lang('To'));
                $this->excel->getActiveSheet()->SetCellValue('H1', $end_date);

                $first_data_row = 3;
                $this->excel->getActiveSheet()->SetCellValue($char++ . $first_data_row, lang('SL.'));
                $this->excel->getActiveSheet()->SetCellValue($char++ . $first_data_row, lang('Product_Code'));
                $this->excel->getActiveSheet()->SetCellValue($char++ . $first_data_row, lang('Product_Name'));
                foreach ($times as $time) {
                    $this->excel->getActiveSheet()->SetCellValue($char++ . $first_data_row, $time['month'] . "_" . $time['year']);
                }

                $row = $first_data_row + 1;
                $row_number = 1;
                foreach ($data as $data_row) {
                    $char = chr(833); // chr(833) is A
                    $this->excel->getActiveSheet()->SetCellValue($char++ . $row, $row_number++);
                    $this->excel->getActiveSheet()->SetCellValue($char++ . $row, $data_row->product_code);
                    $this->excel->getActiveSheet()->SetCellValue($char++ . $row, $data_row->product_name);
                    foreach ($times as $time) {
                        $month_name = $time['month'] . "_" . $time['year'];
                        $this->excel->getActiveSheet()->SetCellValue($char++ . $row, $data_row->$month_name);
                    }
                    $row++;
                }

                $char = chr(833); // chr(833) is A
                $this->excel->getActiveSheet()->getColumnDimension($char++)->setWidth(10);
                $this->excel->getActiveSheet()->getColumnDimension($char++)->setWidth(10);
                $this->excel->getActiveSheet()->getColumnDimension($char++)->setWidth(50);
                foreach ($times as $time) {
                    $this->excel->getActiveSheet()->getColumnDimension($char++)->setWidth(15);
                }
                $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $this->excel->getActiveSheet()->getStyle('B1:B' . $row)->getAlignment()->setWrapText(true);
                $filename = 'Trend_Sales_Report';
                $this->load->helper('excel');
                create_excel($this->excel, $filename);
            }
            $this->session->set_flashdata('error', lang('nothing_found'));
            redirect($_SERVER["HTTP_REFERER"]);

        } else {
            $this->load->library('datatables');
            $this->datatables
                ->select("product_code, product_name AS product_name $month_year", FALSE)
                ->from('sales')
                ->join($transposed, 'tr.sale_id=sales.id', 'right')
                ->join('warehouses', 'warehouses.id=sales.warehouse_id', 'left')
                ->group_by('product_id');

            if ($product) {
                $this->datatables->where('tr.product_id', $product, FALSE);
            }

            echo $this->datatables->generate();
        }
    }

}
