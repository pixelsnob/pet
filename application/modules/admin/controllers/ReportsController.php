<?php

class Admin_ReportsController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->Layout->setLayout('admin');
        $this->_admin_svc = new Service_Admin;
        //$this->_orders_svc = new Service_Orders;
        $this->_users_svc = new Service_Users;
        if (!$this->_users_svc->isAuthenticated(true)) {
            $this->_helper->Redirector->gotoSimple('index', 'index');
        }
    }
    
    public function indexAction() {
        $this->_forward('sales');
    }

    public function salesAction() {
        $orders_mapper = new Model_Mapper_Orders;
        $request = $this->_request;
        $params = $request->getParams();
        $date = new DateTime;
        $date->sub(new DateInterval('P1D'));
        $params['end_date'] = $request->getParam('end_date',
            $date->format('Y-m-d'));
        $params['start_date'] = $request->getParam('start_date',
            $date->format('Y-m-d'));
        $search_form = new Form_Admin_Report_Sales;
        $this->view->search_form = $search_form;
        $search_form->populate($params);
        if ($request->isPost() && $search_form->isValid($params)) {
            $sales = $orders_mapper->getSalesReport($params['start_date'],
                $params['end_date']);
            if ($sales) {
                $date = new DateTime;
                $filename = $date->format('Y-m-d') . '-sales-all.csv';
                $this->_response->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Content-Disposition',
                        "attachment;filename=$filename");
                $this->_admin_svc->outputReportCsv($sales);
                $this->_helper->Layout->disableLayout(); 
                $this->_helper->ViewRenderer->setNoRender(true);
                return;
            }
            $this->view->no_data = true;
        }
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
    }

    public function subscribersAction() {

    }


    public function mailingListAction() {

    }

    public function transactionsAction() {
        $op_mapper = new Model_Mapper_OrderPayments;
        $request = $this->_request;
        $params = $request->getParams();
        $date = new DateTime;
        $date->sub(new DateInterval('P1D'));
        $params['end_date'] = $request->getParam('end_date',
            $date->format('Y-m-d'));
        $params['start_date'] = $request->getParam('start_date',
            $date->format('Y-m-d'));
        $search_form = new Form_Admin_Report_Sales;
        $this->view->search_form = $search_form;
        $search_form->populate($params);
        if ($request->isPost() && $search_form->isValid($params)) {
            //$sales = $orders_mapper->getSalesReport($params['start_date'],
            //    $params['end_date']);
            $transactions = $op_mapper->getTransactionsReport(
                $params['start_date'], $params['end_date']);
            if ($transactions) {
                $date = new DateTime;
                $filename = $date->format('Y-m-d') .
                    '-apet-transaction-report.csv';
                $this->_response->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Content-Disposition',
                        "attachment;filename=$filename");
                $this->_admin_svc->outputReportCsv($transactions);
                $this->_helper->Layout->disableLayout(); 
                $this->_helper->ViewRenderer->setNoRender(true);
                return;
            }
            $this->view->no_data = true;
        }
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
    }
    
    public function testAction() {
        $file = tempnam('tmp', 'zip');
        $zip = new ZipArchive;
        $zip->open($file, ZipArchive::OVERWRITE);
        $zip->addFromString('test.txt', 'xxxxxxxxxxxxx');
        $zip->close();
        $date = new DateTime;
        $filename = $date->format('Y-m-d') . '-apet-transaction-report.csv';
        // Stream the file to the client
        $this->_response->setHeader('Content-Type', 'application/zip')
            ->setHeader('Content-Length', filesize($file))
            ->setHeader('Content-Disposition', "attachment; filename=$filename");
        //header("Content-Type: application/zip");
        //header("Content-Length: " . filesize($file));
        //header("Content-Disposition: attachment; filename=\"a_zip_file.zip\"");
        readfile($file);
        unlink($file); 
        $this->_helper->Layout->disableLayout(); 
        $this->_helper->ViewRenderer->setNoRender(true);
        //exit;
    }

}
