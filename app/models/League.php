<?php
/**
 * League Model
 */

require_once __DIR__ . '/Model.php';

class League extends Model {
    protected $table = 'leagues';
    protected $primaryKey = 'league_id';

    public function getAll() {
        return $this->all('display_order ASC');
    }

    public function getAllActive() {
        return $this->where(['is_active' => 1], 'display_order ASC');
    }

    public function getBySlug($slug) {
        return $this->whereOne(['slug' => $slug, 'is_active' => 1]);
    }

    public function getWithTeams($leagueSlug) {
        $league = $this->getBySlug($leagueSlug);
        if (!$league) return null;

        $sql = "SELECT * FROM teams WHERE league_id = ? AND is_active = 1 ORDER BY display_order";
        $league['teams'] = $this->fetchAll($sql, [$league['league_id']]);

        return $league;
    }
}
