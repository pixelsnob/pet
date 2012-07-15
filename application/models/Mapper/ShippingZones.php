<?php
/**
 * @package Model_Mapper_ShippinZones
 * 
 */
class Model_Mapper_ShippingZones extends Pet_Model_Mapper_Abstract {

    /**
     * @return void
     * 
     */
    public function __construct() {
        $this->_shipping_zones = new Model_DbTable_ShippingZones;
    }

    /**
     * @param int $id
     * @return Model_ShippingZone
     * 
     */
    public function getById($id) {
        $sz = $this->_shipping_zones->getById($id);
        if ($sz) {
            return new Model_ShippingZone($sz->toArray());
        }
    }
        
    /**
     * @return array
     * 
     */
    public function getAll() {
        $zones = $this->_shipping_zones->fetchAll(
            $this->_shipping_zones->select());
        if ($zones) {
            $out = array();
            foreach ($zones as $zone) {
                $out[] = new Model_ShippingZone($zone->toArray());
            }
            return $out;
        }
    }
    
    /**
     * @param array $data
     * @return int New shipping_zone id
     * 
     */
    public function insert(array $data) {
        $zone_model = new Model_ShippingZone($data);
        $zone = $zone_model->toArray();
        return $this->_shipping_zones->insert($zone);
    }
    
    /**
     * @param array $data
     * @param int $id
     * @return void
     * 
     */
    public function update(array $data, $id) {
        $zone_model = new Model_ShippingZone($data);
        $zone = $zone_model->toArray();
        unset($zone['id']);
        $this->_shipping_zones->update($zone, $id);
    }

    /**
     * @param int $id
     * @return void
     * 
     */
    public function delete($id) {
        $where = $this->_shipping_zones->getAdapter()->quoteInto('id = ?', $id);
        $this->_shipping_zones->delete($where);
    }
}

