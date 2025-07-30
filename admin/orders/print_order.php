<?php
require '../../includes/db.php';
require '../../vendor/autoload.php';

use Mpdf\Mpdf;

$order_id = $_GET['id'] ?? '';
if (!$order_id) exit('Thiếu mã đơn hàng');

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) exit('Không tìm thấy đơn hàng');

$mpdf = new Mpdf();
$html = "
<h2>Đơn hàng: {$order['order_id']}</h2>
<p><strong>Ngày đặt:</strong> {$order['created_at']}</p>
<p><strong>Trạng thái:</strong> {$order['status']}</p>
<hr>
<p><strong>Thông tin khác...</strong></p>
";

$mpdf->WriteHTML($html);
$mpdf->Output("donhang_{$order['order_id']}.pdf", \Mpdf\Output\Destination::INLINE);
?>