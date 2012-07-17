<?php

class Admin_ReportsController extends Zend_Controller_Action {

    public function init() {
        $page = $this->view->navigation()->findOneByLabel('Reports'); 
        if ($page) {
            $page->setActive();
        }
        $this->_helper->Layout->setLayout('admin');
        $this->_admin_svc = new Service_Admin;
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
        $search_form = new Form_Admin_Report_Sales;
        $this->view->search_form = $search_form;
        $search_form->populate($params);
        if ($request->isPost() && $search_form->isValid($params)) {
            $sales = $orders_mapper->getSalesReport($search_form);
            if (count($sales)) {
                $date = new DateTime;
                $filename = $date->format('Y-m-d') . '-sales-all.csv';
                $this->_response->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Content-Disposition', "attachment;filename=$filename");
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
        $request = $this->_request;
        $ops_mapper = new Model_Mapper_OrderProductSubscriptions;
        $params = $request->getParams();
        $search_form = new Form_Admin_Report_Subscribers;
        $this->view->search_form = $search_form;
        $search_form->populate($params);
        if ($request->isPost() && $search_form->isValid($params)) {
            $users = $ops_mapper->getSubscribersReport($search_form);
            if (count($users)) {
                $date = new DateTime;
                $filename = $date->format('Y-m-d') . '-apet-subscribers.csv';
                $this->_response->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Content-Disposition', "attachment;filename=$filename");
                $this->_admin_svc->outputReportCsv($users);
                $this->_helper->Layout->disableLayout(); 
                $this->_helper->ViewRenderer->setNoRender(true);
                return;

            }
            $this->view->no_data = true;
        }
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");
    }


    public function mailingListAction() {
        $request = $this->_request;
        if ($request->getParam('no-results')) {
            $this->view->no_data = true;
        }
        $ops_mapper = new Model_Mapper_OrderProductSubscriptions;
        $params = $request->getParams();
        $search_form = new Form_Admin_Report_MailingList;
        $this->view->search_form = $search_form;
        $search_form->populate($params);
        if ($request->isPost() && $search_form->isValid($params)) {
            if (!$this->_outputMailingListReport($search_form)) {
                $this->view->no_data = true;
            }
        }
        $this->view->inlineScriptMin()
            ->appendScript("Pet.loadView('Admin');");

    }

    public function mailingListAllAction() {
        $ops_mapper = new Model_Mapper_OrderProductSubscriptions;
        $search_form = new Form_Admin_Report_MailingList;
        if (!$this->_outputMailingListReport($search_form)) {
            $this->_helper->Redirector->gotoSimple('mailing-list', 'reports',
                'admin', array('no-results' => 1));
        }
    }
    
    private function _outputMailingListReport(Form_Admin_Report_MailingList $search_form) {
        $ops_mapper = new Model_Mapper_OrderProductSubscriptions;
        $usa_users = $ops_mapper->getMailingListReport('usa', $search_form);
        $intl_users = $ops_mapper->getMailingListReport('intl', $search_form);
        if (count($usa_users) || count($intl_users)) {
            $tmp = tempnam('tmp', 'zip');
            $zip = new ZipArchive;
            $zip->open($tmp, ZipArchive::OVERWRITE);
            $date = new DateTime;
            $date_str = $date->format('Y-m-d');
            if (count($usa_users)) {
                $usa_csv = $this->_admin_svc->getCsvAsString($usa_users);
                $zip->addFromString("$date_str-usa.csv", $usa_csv);
            }
            if (count($intl_users)) {
                $intl_csv = $this->_admin_svc->getCsvAsString($intl_users);
                $date = new DateTime;
                $date = $date->format('Y-m-d');
                $zip->addFromString("$date_str-intl.csv", $intl_csv);
            }
            $zip->close();
            $filename = "$date_str-postal-mailing-list.zip";
            $this->_response->setHeader('Content-Type', 'application/zip')
                ->setHeader('Content-Length', filesize($tmp))
                ->setHeader('Content-Disposition', "attachment; filename=$filename");
            readfile($tmp);
            unlink($tmp); 
            $this->_helper->Layout->disableLayout(); 
            $this->_helper->ViewRenderer->setNoRender(true);
            return true;
        }
        return false;
    }

    public function transactionsAction() {
        $op_mapper = new Model_Mapper_OrderPayments;
        $request = $this->_request;
        $params = $request->getParams();
        $search_form = new Form_Admin_Report_Transactions;
        $this->view->search_form = $search_form;
        $search_form->populate($params);
        if ($request->isPost() && $search_form->isValid($params)) {
            $transactions = $op_mapper->getTransactionsReport($search_form);
            if (count($transactions)) {
                $date = new DateTime;
                $filename = $date->format('Y-m-d') . '-apet-transaction-report.csv';
                $this->_response->setHeader('Content-Type', 'text/csv')
                    ->setHeader('Content-Disposition', "attachment;filename=$filename");
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
    

}
