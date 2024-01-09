<?php
class ModelExtensionModuleCustomeSlide extends Model {
    public function addSlider($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "slider SET image_pc = '" . $this->db->escape($data['image_pc']) . "', image_mobile = '" . $this->db->escape($data['image_mobile']) . "', link = '" . $this->db->escape($data['link']) . "', alt = '" . $this->db->escape($data['alt']) . "', title = '" . $this->db->escape($data['title']) . "'");
        
        $banner_id = $this->db->getLastId();
        
        if (isset($data['banner_image'])) {
			foreach ($data['banner_image'] as $language_id => $value) {
				foreach ($value as $banner_image) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', language_id = '" . (int)$language_id . "', title = '" .  $this->db->escape($banner_image['title']) . "', link = '" .  $this->db->escape($banner_image['link']) . "', image = '" .  $this->db->escape($banner_image['image']) . "', sort_order = '" .  (int)$banner_image['sort_order'] . "'");
				}
			}
		}

		return $banner_id;
    }

    public function editSlider($slider_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "slider SET image_pc = '" . $this->db->escape($data['image_pc']) . "', image_mobile = '" . $this->db->escape($data['image_mobile']) . "', link = '" . $this->db->escape($data['link']) . "', alt = '" . $this->db->escape($data['alt']) . "', title = '" . $this->db->escape($data['title']) . "' WHERE slider_id = '" . (int)$slider_id . "'");
    }

    public function deleteSlider($slider_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "slider WHERE slider_id = '" . (int)$slider_id . "'");
    }

    public function getSlider($slider_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "slider WHERE slider_id = '" . (int)$slider_id . "'");

        return $query->row;
    }

    public function getSliders() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "slider");

        return $query->rows;
    }
}
?>