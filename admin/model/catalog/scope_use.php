<?php 
class ModelCatalogScopeUse extends Model {

    public function addScopeUse($data){
        $this->db->query("INSERT INTO " . DB_PREFIX . "spheres SET name = '" . $this->db->escape($data['name']) . "'");

        $scope_use_id = $this->db->getLastId();

        $this->cache->delete('spheres');

        return $scope_use_id;
    }

    public function editScopeUse($scope_use_id, $data) {

		$this->db->query("UPDATE " . DB_PREFIX . "spheres SET name = '" . $this->db->escape($data['name']) . "' WHERE sphere_id = '" . (int)$scope_use_id . "'");

		$this->cache->delete('spheres');
	}

    public function getScopeUse($scope_use_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "spheres WHERE sphere_id = '" . (int)$scope_use_id . "'");

		return $query->row;
	}

    public function deleteScopeUse($scope_use_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "spheres WHERE sphere_id = '" . (int)$scope_use_id . "'");

		$this->cache->delete('spheres');
	}

    public function getScopeUses($data = array()) {

		$sql = "SELECT * FROM " . DB_PREFIX . "spheres";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
}