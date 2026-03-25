<?php
class Statistici {
    private $db;
    public function __construct($pdo_connection) {
        $this->db = $pdo_connection;
    }
    public function getComenziNoiCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM comenzi WHERE STATUS = 'Noua' OR STATUS IS NULL");
        return $stmt->fetchColumn();
    }
    public function getDateGraficVanzari() {
        $sql = "SELECT DATE(DATA_COMANDA) as data_zi, SUM(TOTAL_PLATIT) as total_zi 
                FROM comenzi 
                WHERE DATA_COMANDA >= DATE(NOW()) - INTERVAL 7 DAY 
                GROUP BY DATE(DATA_COMANDA) 
                ORDER BY data_zi ASC";
        $rezultate = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $zile = [];
        $totaluri = [];
        foreach($rezultate as $v) {
            $zile[] = date('d.m', strtotime($v['data_zi'])); 
            $totaluri[] = $v['total_zi'];
        }

        return [
            'zile_json' => json_encode($zile),
            'totaluri_json' => json_encode($totaluri)
        ];
    }
    public function getDateGraficStatus() {
        $sql = "SELECT STATUS, COUNT(ID) as numar FROM comenzi GROUP BY STATUS";
        $rezultate = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $nume = [];
        $numar = [];
        foreach($rezultate as $s) {
            $nume[] = empty($s['STATUS']) ? 'Noua' : $s['STATUS'];
            $numar[] = $s['numar'];
        }

        return [
            'nume_json' => json_encode($nume),
            'numar_json' => json_encode($numar)
        ];
    }
}
?>