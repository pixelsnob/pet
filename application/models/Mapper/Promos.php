<?php
/**
 * @package Model_Mapper_Promos
 * 
 */
class Model_Mapper_Promos extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_promos = new Model_DbTable_Promos;
    }

    /**
     * @return array
     * 
     */
    public function getAll() {
        $promos = $this->_promos->fetchAll($this->_promos->select());
        if ($promos) {
            $out = array();
            foreach ($promos as $promo) {
                $out[] = new Model_Promo($promo->toArray());
            }
            return $out;
        }
    }

    /**
     * @param int $id
     * @return array
     * 
     */
    public function getById($id) {
        $promo_products_mapper = new Model_Mapper_PromoProducts;
        $promo = $this->_promos->getById($id); 
        if ($promo) {
            $promo = new Model_Promo($promo->toArray());
            $promo->promo_products = $promo_products_mapper->getByPromoId(
                $promo->id);
            return $promo;
        }
    }

    /**
     * @param string $code Promo code
     * @param bool $expired_check
     * @return Model_Promo|void
     * 
     */
    public function getByCode($code, $expired_check = true) {
        $promo_products_mapper = new Model_Mapper_PromoProducts;
        $promo = $this->_promos->getByCode($code, $expired_check);
        if ($promo) {
            $promo = new Model_Promo($promo->toArray());
            $promo->promo_products = $promo_products_mapper->getByPromoId(
                $promo->id);
            return $promo;
        }
    }

    /** 
     * Builds a query out of search params and paginates the results
     * 
     * @param array $params
     * @return array Returns the paginator object as well as an array of model
     *               objects
     */
    public function getPaginatedFiltered(array $params) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sel = $this->_promos->select();
        if (isset($params['code']) && $params['code']) {
            $code = $db->quote('%' . $params['code'] . '%');
            $sel->where("code like $code");
        }
        $this->addDateRangeToSelect($sel, 'expiration', $params);
        $this->addSortToSelect($sel, 'id', 'desc', $params);
        $adapter = new Zend_Paginator_Adapter_DbSelect($sel);
        $paginator = new Zend_Paginator($adapter);
        if (isset($params['page'])) {
            $paginator->setCurrentPageNumber((int) $params['page']);
        }
        $paginator->setItemCountPerPage(35);
        $promos = array();
        foreach ($paginator as $row) {
            $promos[] = new Model_Promo($row);
        }
        return array('paginator' => $paginator, 'data' => $promos);
    }

    /**
     * @param array $data
     * @return int New shipping_zone id
     * 
     */
    public function insert(array $data) {
        $promo_model = new Model_Promo($data);
        $promo = $promo_model->toArray();
        return $this->_promos->insert($promo);
    }

    /**
     * @param string $banner_filename
     * @param int $id
     * @return void
     * 
     */
    public function updateBanner($banner_filename, $id) {
        $this->_promos->updateBanner($banner_filename, $id);
    }

    /**
     * @param array $data
     * @param int $id
     * @return void
     * 
     */
    public function update(array $data, $id) {
        $promo_model = new Model_Promo($data);
        $promo = $promo_model->toArray();
        unset($promo['banner']);
        $this->_promos->update($promo, $id);
    }

    /**
     * @param int $id
     * @return void
     * 
     */
    public function delete($id) {
        $where = $this->_promos->getAdapter()->quoteInto('id = ?', $id);
        $this->_promos->delete($where);
    }
}

