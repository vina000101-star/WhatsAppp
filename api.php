<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');

$file = 'user.json';

// Inisialisasi file jika kosong
if (!file_exists($file) || filesize($file) == 0) {
    file_put_contents($file, json_encode(["users" => [], "messages" => []]));
}

$input = json_decode(file_get_contents('php://input'), true);

if ($input) {
    $current_data = json_decode(file_get_contents($file), true);

    if ($input['type'] == 'auth') {
        // Simpan User Baru ke user.json di server
        $exists = false;
        foreach ($current_data['users'] as $u) {
            if ($u['name'] == $input['data']['name']) { $exists = true; break; }
        }
        if (!$exists) {
            $current_data['users'][] = $input['data'];
            file_put_contents($file, json_encode($current_data, JSON_PRETTY_PRINT));
        }
    } 
    else if ($input['type'] == 'send_msg') {
        // Simpan Pesan ke user.json di server
        $current_data['messages'][] = $input['data'];
        file_put_contents($file, json_encode($current_data, JSON_PRETTY_PRINT));
    }

    echo json_encode(["status" => "success", "db" => $current_data]);
} else {
    // Ambil data terbaru untuk sinkronisasi chat
    echo file_get_contents($file);
}
?>
