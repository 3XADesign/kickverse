<?php
/**
 * Team Model
 */

require_once __DIR__ . '/Model.php';

class Team extends Model {
    protected $table = 'teams';
    protected $primaryKey = 'team_id';

    public function getAll() {
        return $this->all('display_order ASC');
    }

    public function getAllActive() {
        return $this->where(['is_active' => 1], 'display_order ASC');
    }

    public function getBySlug($slug) {
        return $this->whereOne(['slug' => $slug, 'is_active' => 1]);
    }

    public function getByLeague($leagueId) {
        return $this->where(['league_id' => $leagueId, 'is_active' => 1], 'display_order ASC');
    }

    public function getTopTeams() {
        return $this->where(['is_top_team' => 1, 'is_active' => 1], 'display_order ASC');
    }

    public function getWithLeague($teamId) {
        $sql = "SELECT t.*, l.name as league_name, l.slug as league_slug, l.country as league_country
                FROM teams t
                LEFT JOIN leagues l ON t.league_id = l.league_id
                WHERE t.team_id = ?";
        return $this->fetchOne($sql, [$teamId]);
    }

    public function getAllWithLeague($filters = []) {
        $sql = "SELECT t.*, l.name as league_name, l.slug as league_slug,
                (SELECT COUNT(*) FROM products WHERE team_id = t.team_id) as product_count
                FROM teams t
                LEFT JOIN leagues l ON t.league_id = l.league_id
                WHERE 1=1";

        $params = [];

        if (isset($filters['league_id']) && $filters['league_id'] !== '') {
            $sql .= " AND t.league_id = ?";
            $params[] = $filters['league_id'];
        }

        if (isset($filters['is_top_team']) && $filters['is_top_team'] !== '') {
            $sql .= " AND t.is_top_team = ?";
            $params[] = $filters['is_top_team'];
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $sql .= " AND t.is_active = ?";
            $params[] = $filters['is_active'];
        }

        $sql .= " ORDER BY t.display_order ASC, t.name ASC";

        return $this->fetchAll($sql, $params);
    }

    public function getWithProducts($teamId) {
        $team = $this->getWithLeague($teamId);
        if (!$team) return null;

        $sql = "SELECT p.*,
                COALESCE((SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.product_id), 0) as total_stock
                FROM products p
                WHERE p.team_id = ? AND p.is_active = 1
                ORDER BY p.created_at DESC";

        $team['products'] = $this->fetchAll($sql, [$teamId]);

        return $team;
    }
}
