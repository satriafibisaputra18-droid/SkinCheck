<?php
/**
 * SkinCheck - Product Model
 * Manages skincare catalog data, concern-based filtering, and recommendations.
 */

class Product extends Model {

    /**
     * Fetch all active products ordered by brand & name.
     * @return array
     */
    public function getProducts() {
        if ($this->db->isConnected()) {
            $this->db->query('SELECT * FROM products WHERE status = "active" ORDER BY brand ASC, name ASC');
            return $this->db->resultSet();
        }

        return $this->getMockProducts();
    }

    /**
     * Fetch products filtered by skin concern.
     * @param string $concern e.g. 'Acne', 'Redness', 'Hyperpigmentation', 'Texture', 'Dryness', 'Sun Protection'
     * @param int $limit
     * @return array
     */
    public function getProductsByConcern(string $concern, int $limit = 6) {
        if ($this->db->isConnected()) {
            $this->db->query('SELECT * FROM products WHERE status = "active" AND (skin_concern LIKE :concern OR description LIKE :concern2) ORDER BY id ASC LIMIT :limit');
            $this->db->bind(':concern', "%{$concern}%");
            $this->db->bind(':concern2', "%{$concern}%");
            $this->db->bind(':limit', $limit);
            return $this->db->resultSet();
        }

        $all = $this->getMockProducts();
        $filtered = array_filter($all, function($p) use ($concern) {
            return stripos($p->skin_concern, $concern) !== false || stripos($p->description, $concern) !== false;
        });
        return array_slice($filtered, 0, $limit);
    }

    /**
     * Fetch all active products grouped by skin concern for prompt context or catalog displays.
     * @return array
     */
    public function getActiveProductsGrouped() {
        if ($this->db->isConnected()) {
            $this->db->query('SELECT * FROM products WHERE status = "active" ORDER BY skin_concern ASC, category ASC, id ASC');
            $rows = $this->db->resultSet();
        } else {
            $rows = $this->getMockProducts();
        }

        $grouped = [];
        foreach ($rows as $row) {
            $concern = $row->skin_concern ?: 'General';
            if (!isset($grouped[$concern])) {
                $grouped[$concern] = [];
            }
            $grouped[$concern][] = $row;
        }

        return $grouped;
    }

    /**
     * Search products by keyword (in name, brand, ingredients, or concern).
     * @param string $keyword
     * @return array
     */
    public function searchProducts(string $keyword) {
        if ($this->db->isConnected()) {
            $this->db->query('SELECT * FROM products WHERE status = "active" AND (name LIKE :kw OR brand LIKE :kw OR ingredients LIKE :kw OR skin_concern LIKE :kw) ORDER BY brand ASC LIMIT 20');
            $this->db->bind(':kw', "%{$keyword}%");
            return $this->db->resultSet();
        }

        $all = $this->getMockProducts();
        return array_filter($all, function($p) use ($keyword) {
            return stripos($p->name, $keyword) !== false || stripos($p->brand, $keyword) !== false || stripos($p->ingredients, $keyword) !== false;
        });
    }

    /**
     * Add a cosmetic product into the database.
     * @param array $data
     * @return bool
     */
    public function addProduct(array $data) {
        if ($this->db->isConnected()) {
            $this->db->query('INSERT INTO products (brand, name, category, description, ingredients, skin_concern, usage_guidance, status, price, skin_type) VALUES (:brand, :name, :category, :description, :ingredients, :skin_concern, :usage_guidance, :status, :price, :skin_type)');
            $this->db->bind(':brand', $data['brand']);
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':category', $data['category']);
            $this->db->bind(':description', $data['description'] ?? '');
            $this->db->bind(':ingredients', $data['ingredients'] ?? '');
            $this->db->bind(':skin_concern', $data['skin_concern'] ?? 'All');
            $this->db->bind(':usage_guidance', $data['usage_guidance'] ?? '');
            $this->db->bind(':status', $data['status'] ?? 'active');
            $this->db->bind(':price', $data['price'] ?? 0);
            $this->db->bind(':skin_type', $data['skin_type'] ?? 'All Skin Types');
            return $this->db->execute();
        }
        return true;
    }

    /**
     * Mock skincare catalog fallback
     * @return array
     */
    private function getMockProducts() {
        return [
            (object)[
                'id' => 1,
                'brand' => 'Somethinc',
                'name' => 'Low pH Gentle Jelly Cleanser',
                'category' => 'Cleanser',
                'skin_concern' => 'Acne',
                'skin_type' => 'Acne-Prone, Sensitive',
                'price' => 99000,
                'ingredients' => 'Japanese Mugwort, Tea Tree Oil, Centella Asiatica',
                'description' => 'Pembersih wajah bertekstur jelly dengan pH seimbang.',
                'usage_guidance' => 'Pagi & malam busakan dengan air dan bilas bersih.'
            ],
            (object)[
                'id' => 2,
                'brand' => 'SKINTIFIC',
                'name' => '2% Salicylic Acid Anti Acne Serum',
                'category' => 'Serum',
                'skin_concern' => 'Acne',
                'skin_type' => 'Acne-Prone, Oily',
                'price' => 139000,
                'ingredients' => '2% Salicylic Acid, Pionin, Centella, Ceramide',
                'description' => 'Serum penenang jerawat meradang dan pembersih pori tersumbat.',
                'usage_guidance' => 'Teteskan 2-3 tetes pagi dan malam sebelum pelembap.'
            ],
            (object)[
                'id' => 3,
                'brand' => 'SKIN1004',
                'name' => 'Madagascar Centella Ampoule',
                'category' => 'Serum',
                'skin_concern' => 'Redness',
                'skin_type' => 'Sensitive, Redness-Prone',
                'price' => 195000,
                'ingredients' => '100% Centella Asiatica Extract',
                'description' => 'Menenangkan iritasi dan kemerahan secara instan serta memperkuat barrier.',
                'usage_guidance' => 'Gunakan 3-4 tetes merata ke seluruh wajah.'
            ],
            (object)[
                'id' => 4,
                'brand' => 'SKINTIFIC',
                'name' => '5X Ceramide Barrier Repair Moisture Gel',
                'category' => 'Moisturizer',
                'skin_concern' => 'Redness',
                'skin_type' => 'Sensitive, Damaged Barrier',
                'price' => 139000,
                'ingredients' => '5X Ceramide, Hyaluronic Acid, Centella Asiatica',
                'description' => 'Pelembap pemulih skin barrier dan pengunci hidrasi mendalam.',
                'usage_guidance' => 'Oleskan merata pagi dan malam.'
            ],
            (object)[
                'id' => 5,
                'brand' => 'The Ordinary',
                'name' => 'Niacinamide 10% + Zinc 1%',
                'category' => 'Serum',
                'skin_concern' => 'Hyperpigmentation',
                'skin_type' => 'Dull, Pigmented, Oily',
                'price' => 165000,
                'ingredients' => '10% Niacinamide, 1% Zinc PCA',
                'description' => 'Menyamarkan noda hitam bekas jerawat dan mengontrol sebum.',
                'usage_guidance' => 'Pagi dan malam sebelum pelembap.'
            ],
            (object)[
                'id' => 6,
                'brand' => 'EltaMD',
                'name' => 'UV Clear Broad-Spectrum SPF 46',
                'category' => 'Sunscreen',
                'skin_concern' => 'Sun Protection',
                'skin_type' => 'Acne-Prone, Sensitive',
                'price' => 399000,
                'ingredients' => 'Zinc Oxide, Niacinamide, Hyaluronic Acid',
                'description' => 'Sunscreen hibrida bebas minyak yang aman untuk kulit berjerawat.',
                'usage_guidance' => 'Aplikasikan 2 jari setiap pagi sebelum keluar ruangan.'
            ]
        ];
    }
}
