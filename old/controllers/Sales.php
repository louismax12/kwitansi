<?php
// controllers/Sales.php
require_once 'models/KwitansiModel.php';

class Sales {
    private $model;

    public function __construct() {
        $this->model = new KwitansiModel();
    }

    public function index() {
        require_once 'views/sales/register.php';
    }

    public function searchItem() {
        header('Content-Type: application/json');
        if (isset($_GET['keyword'])) {
            $data = $this->model->searchBarang($_GET['keyword']);
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "error", "message" => "Keyword required"));
        }
    }

    public function save() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $payload = json_decode($json, true);
            
            if (!$payload || !isset($payload['nama_pasien']) || !isset($payload['items'])) {
                echo json_encode(array("status" => "error", "message" => "Invalid payload"));
                return;
            }
            
            $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 2; // Default kasir
            $result = $this->model->simpanTransaksi(
                $payload['nama_pasien'], 
                isset($payload['no_faktur']) ? $payload['no_faktur'] : null,
                isset($payload['untuk_pembayaran']) ? $payload['untuk_pembayaran'] : null,
                isset($payload['id_pelanggan']) ? $payload['id_pelanggan'] : null,
                $payload['items'],
                $id_user
            );
            
            echo json_encode($result);
        }
    }
}
?>
