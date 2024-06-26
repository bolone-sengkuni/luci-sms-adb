<?php
$shell = 'adb shell content query --uri content://sms --projection _id,address,date,body';
$excec_adb = shell_exec($shell);

if ($excec_adb !== null) {
    $excec_adb = explode("\n", $excec_adb);

    $maxData = 20;
    $count = 0;

    $result = array();
    foreach ($excec_adb as $line) {
        try {
            $asu = explode("=", $line);
            if (isset($asu[2]) && isset($asu[3]) && isset($asu[4])) {
                $addressParts = explode(",", $asu[2]);
                $dateParts = explode(",", $asu[3]);
                $pesanParts = explode(",", $asu[4]);
                if (isset($addressParts[0]) && isset($dateParts[0]) && isset($pesanParts[0])) {
                    $address = $addressParts[0];
                    $date = (int)($dateParts[0] / 1000);
                    $dateTime = new DateTime();
                    $dateTime->setTimestamp($date);
                    $pesan = $pesanParts[0];
                    $result[] = array(
                        'date' => $dateTime->format('Y-m-d H:i:s'),
                        'address' => $address,
                        'pesan' => $pesan
                    );

                    $count++;
                    if ($count >= $maxData) {
                        break;
                    }
                }
            }
        } catch (Exception $e) {
            echo 'Kesalahan: ' . $e->getMessage();
        }
    }

    $html = '';
    foreach ($result as $sms) {
        $html .= "<tr>";
        $html .= "<td>{$sms['date']}</td>";
        $html .= "<td>{$sms['address']}</td>";
        $html .= "<td>{$sms['pesan']}</td>";
        $html .= "<td><button class='copy-button' data-clipboard-text='{$sms['pesan']}'>Salin</button></td>";
        $html .= "</tr>";
    }
    echo $html;
} else {
    $html = '';
    $html .= "<td>ADB ERROR</td>";
    echo $html;
}
?>
